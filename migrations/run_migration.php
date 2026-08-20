<?php
// Run once from the project root in a trusted/local environment:
// php migrations/run_migration.php
require_once __DIR__ . '/../config.php';

function columnExists(PDO $pdo, string $table, string $column): bool {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?");
    $stmt->execute([$table, $column]);
    return (bool)$stmt->fetchColumn();
}
function indexExists(PDO $pdo, string $table, string $index): bool {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?");
    $stmt->execute([$table, $index]);
    return (bool)$stmt->fetchColumn();
}

$pdo->beginTransaction();
try {
    $sql = file_get_contents(__DIR__ . '/2026_08_19_arnquickfix_features.sql');
    // Execute statements that do not contain comments-only chunks.
    foreach (preg_split('/;\s*(?:\r?\n|$)/', $sql) as $statement) {
        $statement = preg_replace('/^\s*--.*(?:\r?\n|$)/m', '', trim($statement));
        $statement = trim($statement);
        if ($statement === '') continue;
        $pdo->exec($statement);
    }

    $alter = [
        ['service_requests', 'asset_ref_id', 'BIGINT UNSIGNED NULL AFTER asset_id'],
        ['maintenance_schedules', 'asset_ref_id', 'BIGINT UNSIGNED NULL AFTER asset_id'],
        ['service_requests', 'technician_id', 'BIGINT UNSIGNED NULL AFTER asset_ref_id'],
        ['service_requests', 'original_price', 'DECIMAL(12,2) NULL AFTER amount'],
        ['service_requests', 'current_price', 'DECIMAL(12,2) NULL AFTER original_price'],
        ['users', 'position', 'VARCHAR(150) NULL AFTER specialization'],
    ];
    foreach ($alter as [$table, $column, $definition]) {
        if (!columnExists($pdo, $table, $column)) {
            $pdo->exec("ALTER TABLE `$table` ADD COLUMN `$column` $definition");
        }
    }
    foreach ([
        ['service_requests','idx_service_requests_asset_ref','asset_ref_id'],
        ['service_requests','idx_service_requests_technician','technician_id'],
    ] as [$table,$index,$column]) {
        if (!indexExists($pdo,$table,$index)) $pdo->exec("ALTER TABLE `$table` ADD INDEX `$index` (`$column`)");
    }

    // Priority is intentionally retired system-wide. Drop the legacy columns only after application code no longer references them.
    foreach (['service_requests','client_requests'] as $priorityTable) {
        if (columnExists($pdo, $priorityTable, 'priority')) {
            $pdo->exec("ALTER TABLE `$priorityTable` DROP COLUMN `priority`");
        }
    }

    // Legacy completion records used an `_C1`, `_C2` suffix to simulate versions of an asset.
    // Those suffixes are removed so every complaint/history row points to the same immutable canonical Asset ID.
    $pdo->exec("UPDATE service_requests SET asset_id=SUBSTRING_INDEX(asset_id,'_C',1) WHERE asset_id LIKE '%_C%'");
    if (columnExists($pdo, 'maintenance_schedules', 'asset_id')) {
        $pdo->exec("UPDATE maintenance_schedules SET asset_id=SUBSTRING_INDEX(asset_id,'_C',1) WHERE asset_id LIKE '%_C%'");
    }

    // Backfill customer assets from existing request rows without deleting or rewriting legacy records.
    $legacy = $pdo->query("SELECT client_email, asset_type, asset_brand, asset_id FROM service_requests WHERE asset_id IS NOT NULL AND TRIM(asset_id) <> '' GROUP BY client_email, asset_type, asset_brand, asset_id")->fetchAll(PDO::FETCH_ASSOC);
    $find = $pdo->prepare('SELECT id FROM assets WHERE customer_email=? AND asset_id=? LIMIT 1');
    $insert = $pdo->prepare('INSERT INTO assets (asset_id, customer_id, customer_email, asset_type, asset_brand, asset_details) VALUES (?,?,?,?,?,?)');
    $user = $pdo->prepare('SELECT id FROM users WHERE email=? LIMIT 1');
    $updateLegacyId = $pdo->prepare('UPDATE service_requests SET asset_id=? WHERE client_email=? AND asset_id=? AND asset_type=?');
    $seq = $pdo->prepare("INSERT INTO asset_sequences(prefix,next_number) VALUES(?,1) ON DUPLICATE KEY UPDATE next_number=next_number+1");
    $seqRead = $pdo->prepare('SELECT next_number FROM asset_sequences WHERE prefix=?');
    foreach ($legacy as $row) {
        $find->execute([$row['client_email'], $row['asset_id']]);
        if ($find->fetchColumn()) continue;
        $user->execute([$row['client_email']]);
        $uid = $user->fetchColumn() ?: null;
        $candidate = $row['asset_id'];
        try {
            $insert->execute([$candidate, $uid, $row['client_email'], $row['asset_type'], $row['asset_brand'], 'Migrated from legacy service request']);
        } catch (PDOException $e) {
            if ($e->getCode() !== '23000') throw $e;
            $prefix = match (strtolower(trim($row['asset_type']))) { 'ac' => 'AC', 'elevator','lift','elv' => 'ELV', 'generator','gen' => 'GEN', default => 'AST' };
            $seq->execute([$prefix]); $seqRead->execute([$prefix]); $num=(int)$seqRead->fetchColumn();
            $candidate=sprintf('%s-%06d',$prefix,$num);
            $insert->execute([$candidate, $uid, $row['client_email'], $row['asset_type'], $row['asset_brand'], 'Migrated duplicate legacy Asset ID; canonical ID generated automatically']);
            $updateLegacyId->execute([$candidate,$row['client_email'],$row['asset_id'],$row['asset_type']]);
        }
    }

    // Advance generators past any already-existing canonical numeric Asset IDs.
    foreach (['AC','ELV','GEN'] as $prefix) {
        $st=$pdo->prepare("SELECT COALESCE(MAX(CAST(SUBSTRING_INDEX(asset_id,'-',-1) AS UNSIGNED)),0) FROM assets WHERE asset_id LIKE ?");
        $st->execute([$prefix.'-%']); $next=(int)$st->fetchColumn();
        $up=$pdo->prepare('UPDATE asset_sequences SET next_number=GREATEST(next_number,?) WHERE prefix=?');$up->execute([$next,$prefix]);
    }

    // Link service requests to the migrated asset rows.
    $pdo->exec("UPDATE service_requests sr JOIN assets a ON a.customer_email=sr.client_email AND a.asset_id=sr.asset_id SET sr.asset_ref_id=a.id WHERE sr.asset_ref_id IS NULL");

    // Seed original/current price from existing amount where available.
    $pdo->exec("UPDATE service_requests SET original_price=amount, current_price=amount WHERE amount IS NOT NULL AND amount > 0 AND original_price IS NULL");

    // Resolve technician IDs from the legacy '(Assigned to: Name)' location marker.
    $rows = $pdo->query("SELECT id, location FROM service_requests WHERE technician_id IS NULL AND location LIKE '%(Assigned to:%'")->fetchAll(PDO::FETCH_ASSOC);
    $findTech = $pdo->prepare("SELECT id FROM users WHERE name=? AND role LIKE 'tech_%' LIMIT 1");
    $updateTech = $pdo->prepare('UPDATE service_requests SET technician_id=? WHERE id=?');
    foreach ($rows as $r) {
        if (preg_match('/\(Assigned to:\s*([^)]+)\)/i', $r['location'] ?? '', $m)) {
            $findTech->execute([trim($m[1])]);
            $tid = $findTech->fetchColumn();
            if ($tid) $updateTech->execute([$tid, $r['id']]);
        }
    }

    // Link existing maintenance schedules to canonical assets when possible.
    if (columnExists($pdo, 'maintenance_schedules', 'asset_ref_id')) {
        $pdo->exec("UPDATE maintenance_schedules ms JOIN assets a ON a.customer_email=ms.client_email AND a.asset_id=ms.asset_id SET ms.asset_ref_id=a.id WHERE ms.asset_ref_id IS NULL");
    }

    // Set sensible legacy positions without changing role or permissions.
    $pdo->exec("UPDATE users SET position = CASE WHEN role='tech_ac' THEN 'AC Technician' WHEN role='tech_generator' THEN 'Generator Technician' WHEN role='tech_elevator' THEN 'Elevator Technician' ELSE position END WHERE position IS NULL OR TRIM(position)=''");

    $pdo->commit();
    echo "ARNQuickFix migration completed successfully.\n";
} catch (Throwable $e) {
    $pdo->rollBack();
    fwrite(STDERR, "Migration failed: {$e->getMessage()}\n");
    exit(1);
}
