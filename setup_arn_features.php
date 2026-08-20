<?php
// One-time local setup/repair for ARNQuickFix feature database changes.
// Open while logged in as Manager, then remove/rename this file after success.
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/arn_features.php';
arn_require_role(['manager']);

ini_set('display_errors', '1');
error_reporting(E_ALL);

function setup_col_exists(PDO $pdo, string $table, string $column): bool {
    $q = $pdo->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name=? AND column_name=?");
    $q->execute([$table, $column]);
    return (bool)$q->fetchColumn();
}
function setup_table_exists(PDO $pdo, string $table): bool {
    $q=$pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema=DATABASE() AND table_name=?");
    $q->execute([$table]);
    return (bool)$q->fetchColumn();
}
function setup_index_exists(PDO $pdo, string $table, string $index): bool {
    $q=$pdo->prepare("SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema=DATABASE() AND table_name=? AND index_name=?");
    $q->execute([$table,$index]);
    return (bool)$q->fetchColumn();
}

$messages=[]; $errors=[];
try {
    $pdo->beginTransaction();

    $pdo->exec("CREATE TABLE IF NOT EXISTS assets (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        asset_id VARCHAR(32) NOT NULL,
        customer_id BIGINT UNSIGNED NULL,
        customer_email VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
        asset_type VARCHAR(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
        asset_brand VARCHAR(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
        asset_details TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY(id), UNIQUE KEY uq_assets_asset_id(asset_id), KEY idx_assets_customer_email(customer_email), KEY idx_assets_customer_id(customer_id), KEY idx_assets_type(asset_type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS asset_sequences (
        prefix VARCHAR(8) NOT NULL, next_number BIGINT UNSIGNED NOT NULL DEFAULT 0, PRIMARY KEY(prefix)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $pdo->exec("INSERT IGNORE INTO asset_sequences(prefix,next_number) VALUES ('AC',0),('ELV',0),('GEN',0)");

    $tables=[
      ['technician_reviews',"CREATE TABLE IF NOT EXISTS technician_reviews (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, service_request_id BIGINT UNSIGNED NOT NULL, asset_id BIGINT UNSIGNED NULL, customer_id BIGINT UNSIGNED NULL, customer_email VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, technician_id BIGINT UNSIGNED NULL, rating TINYINT UNSIGNED NOT NULL, review TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY(id), UNIQUE KEY uq_review_service_request(service_request_id), KEY idx_reviews_technician(technician_id), KEY idx_reviews_customer(customer_id), KEY idx_reviews_asset(asset_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"],
      ['service_price_history',"CREATE TABLE IF NOT EXISTS service_price_history (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, service_request_id BIGINT UNSIGNED NOT NULL, asset_id BIGINT UNSIGNED NULL, previous_price DECIMAL(12,2) NULL, new_price DECIMAL(12,2) NOT NULL, changed_by BIGINT UNSIGNED NULL, changed_by_email VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL, reason VARCHAR(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id), KEY idx_price_history_request(service_request_id), KEY idx_price_history_asset(asset_id), KEY idx_price_history_changed_by(changed_by)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"],
      ['technician_promotions',"CREATE TABLE IF NOT EXISTS technician_promotions (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, technician_id BIGINT UNSIGNED NOT NULL, previous_position VARCHAR(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL, new_position VARCHAR(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, promotion_date DATE NOT NULL, reason VARCHAR(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL, approved_by BIGINT UNSIGNED NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id), KEY idx_promotions_technician(technician_id), KEY idx_promotions_date(promotion_date)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"]
    ];
    foreach($tables as [$name,$sql]) $pdo->exec($sql);

    $alter=[
      ['service_requests','asset_ref_id','BIGINT UNSIGNED NULL'],
      ['service_requests','technician_id','BIGINT UNSIGNED NULL'],
      ['service_requests','original_price','DECIMAL(12,2) NULL'],
      ['service_requests','current_price','DECIMAL(12,2) NULL'],
      ['maintenance_schedules','asset_ref_id','BIGINT UNSIGNED NULL'],
      ['users','position','VARCHAR(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL'],
    ];
    foreach($alter as [$t,$c,$def]) if(!setup_col_exists($pdo,$t,$c)) { $pdo->exec("ALTER TABLE `$t` ADD COLUMN `$c` $def"); $messages[]="Added $t.$c"; }

    foreach([
      ['service_requests','idx_service_requests_asset_ref','asset_ref_id'],
      ['service_requests','idx_service_requests_technician','technician_id'],
      ['maintenance_schedules','idx_maintenance_asset_ref','asset_ref_id']
    ] as [$t,$idx,$c]) if(!setup_index_exists($pdo,$t,$idx)) { $pdo->exec("ALTER TABLE `$t` ADD INDEX `$idx` (`$c`)"); }

    // Normalize text columns used in joins/searches. This fixes general_ci/unicode_ci comparison errors.
    foreach([
      ['assets','customer_email','VARCHAR(255)'], ['users','email','VARCHAR(255)'],
      ['service_requests','client_email','VARCHAR(255)'], ['maintenance_schedules','client_email','VARCHAR(255)']
    ] as [$t,$c,$type]) if(setup_col_exists($pdo,$t,$c)) $pdo->exec("ALTER TABLE `$t` MODIFY `$c` $type CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    // Canonicalize legacy version suffixes without deleting records.
    if(setup_col_exists($pdo,'service_requests','asset_id')) $pdo->exec("UPDATE service_requests SET asset_id=SUBSTRING_INDEX(asset_id,'_C',1) WHERE asset_id LIKE '%_C%'");
    if(setup_col_exists($pdo,'maintenance_schedules','asset_id')) $pdo->exec("UPDATE maintenance_schedules SET asset_id=SUBSTRING_INDEX(asset_id,'_C',1) WHERE asset_id LIKE '%_C%'");

    // Backfill canonical assets from existing service requests.
    $legacy=$pdo->query("SELECT client_email, asset_type, MAX(asset_brand) AS asset_brand, asset_id FROM service_requests WHERE asset_id IS NOT NULL AND TRIM(asset_id)<>'' GROUP BY client_email, asset_type, asset_id")->fetchAll(PDO::FETCH_ASSOC);
    $find=$pdo->prepare('SELECT id FROM assets WHERE customer_email=? AND asset_id=? LIMIT 1');
    $getUser=$pdo->prepare('SELECT id FROM users WHERE email=? LIMIT 1');
    $ins=$pdo->prepare('INSERT INTO assets(asset_id,customer_id,customer_email,asset_type,asset_brand,asset_details) VALUES(?,?,?,?,?,?)');
    $seqMax=$pdo->prepare("SELECT COALESCE(MAX(CAST(SUBSTRING_INDEX(asset_id,'-',-1) AS UNSIGNED)),0) FROM assets WHERE asset_id LIKE ?");
    $seqSet=$pdo->prepare('UPDATE asset_sequences SET next_number=GREATEST(next_number,?) WHERE prefix=?');
    foreach($legacy as $r){
      $find->execute([$r['client_email'],$r['asset_id']]);
      if($find->fetchColumn()) continue;
      $getUser->execute([$r['client_email']]); $uid=$getUser->fetchColumn() ?: null;
      $type=arn_asset_type_label($r['asset_type']);
      $assetCode=strtoupper(trim($r['asset_id']));
      if(!preg_match('/^(AC|ELV|GEN)-\d{6}$/',$assetCode)) {
        $prefix=arn_asset_prefix($type); $seqMax->execute([$prefix.'-%']); $n=(int)$seqMax->fetchColumn()+1; $assetCode=sprintf('%s-%06d',$prefix,$n);
        $pdo->prepare("UPDATE service_requests SET asset_id=? WHERE client_email=? AND asset_id=? AND asset_type=?")->execute([$assetCode,$r['client_email'],$r['asset_id'],$r['asset_type']]);
      }
      $ins->execute([$assetCode,$uid,$r['client_email'],$type,$r['asset_brand']??'','Migrated from existing service request']);
    }

    foreach(['AC','ELV','GEN'] as $prefix){ $seqMax->execute([$prefix.'-%']); $n=(int)$seqMax->fetchColumn(); $seqSet->execute([$n,$prefix]); }
    $pdo->exec("UPDATE service_requests sr JOIN assets a ON a.customer_email=sr.client_email COLLATE utf8mb4_unicode_ci AND a.asset_id=sr.asset_id COLLATE utf8mb4_unicode_ci SET sr.asset_ref_id=a.id WHERE sr.asset_ref_id IS NULL");
    $pdo->exec("UPDATE maintenance_schedules ms JOIN assets a ON a.customer_email=ms.client_email COLLATE utf8mb4_unicode_ci AND a.asset_id=ms.asset_id COLLATE utf8mb4_unicode_ci SET ms.asset_ref_id=a.id WHERE ms.asset_ref_id IS NULL");
    if(setup_col_exists($pdo,'service_requests','amount')) $pdo->exec("UPDATE service_requests SET original_price=amount,current_price=amount WHERE amount IS NOT NULL AND amount>0 AND original_price IS NULL");

    $pdo->commit();
    $assetCount=(int)$pdo->query('SELECT COUNT(*) FROM assets')->fetchColumn();
    $messages[]="Asset registry is ready. Total registered assets: $assetCount";
} catch(Throwable $e){ if($pdo->inTransaction())$pdo->rollBack(); $errors[]=$e->getMessage(); }
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>ARNQuickFix Setup</title><link href="css/bootstrap.min.css" rel="stylesheet"></head><body style="background:#F8FAFC"><div class="container py-5" style="max-width:850px"><div class="bg-white border rounded-4 p-4 shadow-sm"><h2 class="fw-bold">ARNQuickFix Database Setup</h2><p class="text-secondary">This one-time repair safely adds missing feature columns and backfills existing customer assets. It does not delete customer/service data.</p><?php foreach($messages as $m):?><div class="alert alert-success"><?=htmlspecialchars($m)?></div><?php endforeach;?><?php foreach($errors as $e):?><div class="alert alert-danger"><strong>Setup failed:</strong> <?=htmlspecialchars($e)?></div><?php endforeach;?><div class="mt-4"><a href="manager-dashboard.php" class="btn btn-dark rounded-pill">Back to Manager Dashboard</a></div><hr><small class="text-danger fw-semibold">After successful setup, delete or rename setup_arn_features.php for security.</small></div></div></body></html>
