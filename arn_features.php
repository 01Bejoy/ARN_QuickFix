<?php
/**
 * ARNQuickFix shared feature helpers.
 * Existing pages may continue using their own mysqli connection; this file
 * deliberately accepts that connection so no authentication/database rewrite is required.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function arn_require_role(array $roles): void
{
    $role = strtolower(trim($_SESSION['role'] ?? ''));
    $roles = array_map('strtolower', $roles);
    if (!isset($_SESSION['email']) || !in_array($role, $roles, true)) {
        header('Location: login.php');
        exit();
    }
}

function arn_asset_prefix(string $assetType): string
{
    $type = strtolower(trim($assetType));
    return match ($type) {
        'ac', 'air conditioner', 'air_conditioner' => 'AC',
        'elevator', 'lift', 'elv' => 'ELV',
        'generator', 'gen' => 'GEN',
        default => throw new InvalidArgumentException('Unsupported asset type.')
    };
}

function arn_asset_type_label(string $assetType): string
{
    return match (arn_asset_prefix($assetType)) {
        'AC' => 'AC',
        'ELV' => 'Elevator',
        'GEN' => 'Generator'
    };
}

/** Atomic sequence allocation. Requires asset_sequences(prefix PK, next_number). */
function arn_generate_asset_id(mysqli $conn, string $assetType): string
{
    $prefix = arn_asset_prefix($assetType);
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare(
            "INSERT INTO asset_sequences (prefix, next_number) VALUES (?, 1)
             ON DUPLICATE KEY UPDATE next_number = LAST_INSERT_ID(next_number + 1)"
        );
        $stmt->bind_param('s', $prefix);
        if (!$stmt->execute()) {
            throw new RuntimeException($stmt->error);
        }
        $stmt->close();

        $result = $conn->query('SELECT LAST_INSERT_ID() AS allocated');
        $allocated = (int)($result->fetch_assoc()['allocated'] ?? 0);
        if ($allocated < 1) {
            $result = $conn->query("SELECT next_number AS allocated FROM asset_sequences WHERE prefix = '" . $conn->real_escape_string($prefix) . "'");
            $allocated = (int)($result->fetch_assoc()['allocated'] ?? 0);
        }
        if ($allocated < 1) {
            throw new RuntimeException('Unable to allocate an asset sequence number.');
        }

        $assetId = sprintf('%s-%06d', $prefix, $allocated);
        $conn->commit();
        return $assetId;
    } catch (Throwable $e) {
        $conn->rollback();
        throw $e;
    }
}

function arn_create_asset(mysqli $conn, string $customerEmail, ?int $customerId, string $assetType, string $assetBrand = '', string $details = ''): array
{
    $assetTypeLabel = arn_asset_type_label($assetType);
    $assetId = arn_generate_asset_id($conn, $assetTypeLabel);

    $stmt = $conn->prepare(
        'INSERT INTO assets (asset_id, customer_id, customer_email, asset_type, asset_brand, asset_details) VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->bind_param('sissss', $assetId, $customerId, $customerEmail, $assetTypeLabel, $assetBrand, $details);
    if (!$stmt->execute()) {
        $error = $stmt->error;
        $stmt->close();
        throw new RuntimeException($error);
    }
    $assetDbId = $stmt->insert_id;
    $stmt->close();
    return ['id' => $assetDbId, 'asset_id' => $assetId, 'asset_type' => $assetTypeLabel, 'asset_brand' => $assetBrand];
}

function arn_find_asset_for_customer(mysqli $conn, int $assetDbId, string $customerEmail): ?array
{
    $stmt = $conn->prepare('SELECT * FROM assets WHERE id = ? AND customer_email = ? LIMIT 1');
    $stmt->bind_param('is', $assetDbId, $customerEmail);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc() ?: null;
    $stmt->close();
    return $row;
}

function arn_find_asset_by_code(mysqli $conn, string $assetId): ?array
{
    $stmt = $conn->prepare('SELECT * FROM assets WHERE asset_id = ? LIMIT 1');
    $stmt->bind_param('s', $assetId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc() ?: null;
    $stmt->close();
    return $row;
}

function arn_record_price_change(mysqli $conn, int $requestId, ?int $assetDbId, ?float $previous, float $new, ?int $changedBy, ?string $changedByEmail, ?string $reason = null): void
{
    if ($previous !== null && abs($previous - $new) < 0.00001) {
        return;
    }
    $stmt = $conn->prepare(
        'INSERT INTO service_price_history (service_request_id, asset_id, previous_price, new_price, changed_by, changed_by_email, reason) VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->bind_param('iiddiss', $requestId, $assetDbId, $previous, $new, $changedBy, $changedByEmail, $reason);
    $stmt->execute();
    $stmt->close();
}

function arn_get_technician_id_by_name(mysqli $conn, string $name): ?int
{
    $stmt = $conn->prepare("SELECT id FROM users WHERE name = ? AND role LIKE 'tech_%' LIMIT 1");
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row ? (int)$row['id'] : null;
}

function arn_get_technician_id_for_request(mysqli $conn, array $request): ?int
{
    if (!empty($request['technician_id'])) {
        return (int)$request['technician_id'];
    }
    $location = (string)($request['location'] ?? '');
    if (preg_match('/\(Assigned to:\s*([^)]+)\)/i', $location, $m)) {
        return arn_get_technician_id_by_name($conn, trim($m[1]));
    }
    return null;
}

function arn_is_completed_status(string $status): bool
{
    return strtolower(trim($status)) === 'completed';
}
