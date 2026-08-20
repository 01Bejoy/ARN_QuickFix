<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/arn_features.php';
arn_require_role(['client','user']);

$email = $_SESSION['email'];
$name = $_SESSION['name'] ?? 'Customer';
$message = '';
$error = '';

$stmt = $pdo->prepare('SELECT id, name FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();
$userId = $user ? (int)$user['id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'register_asset') {
    $type = trim($_POST['asset_type'] ?? '');
    $brand = trim($_POST['asset_brand'] ?? '');
    $details = trim($_POST['asset_details'] ?? '');
    try {
        $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
        if ($conn->connect_error) throw new RuntimeException('Database connection failed.');
        $asset = arn_create_asset($conn, $email, $userId, $type, $brand, $details);
        $conn->close();
        $message = 'Asset registered successfully. Asset ID generated automatically: ' . $asset['asset_id'];
    } catch (Throwable $e) {
        $error = 'Unable to register the asset: ' . $e->getMessage();
    }
}

$stmt = $pdo->prepare('SELECT a.*, (SELECT COUNT(*) FROM service_requests sr WHERE sr.asset_ref_id=a.id) AS service_count FROM assets a WHERE a.customer_email=? ORDER BY a.id DESC');
$stmt->execute([$email]);
$assets = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>My Assets | ARN QuickFix</title>
<link href="css/bootstrap.min.css" rel="stylesheet"><link href="css/style.css" rel="stylesheet">
<style>.asset-id{font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-weight:800;letter-spacing:.4px;color:#0891B2}.panel{background:#fff;border:1px solid #E2E8F0;border-radius:18px;box-shadow:0 8px 28px rgba(15,23,42,.04)}</style>
</head>
<body style="background:#F8FAFC">
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div><h2 class="fw-bold mb-1">My Assets</h2><div class="text-secondary small">Register and manage your AC, elevator and generator assets.</div></div>
    <a href="client-dashboard.php" class="btn btn-outline-secondary rounded-pill">Back to Dashboard</a>
  </div>
  <?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <div class="row g-4">
    <div class="col-lg-4"><div class="panel p-4"><h5 class="fw-bold mb-3">Register New Asset</h5>
      <form method="post"><input type="hidden" name="action" value="register_asset">
        <div class="mb-3"><label class="form-label fw-semibold">Asset Type</label><select name="asset_type" class="form-select" required><option value="">Select type</option><option value="AC">AC</option><option value="Elevator">Elevator</option><option value="Generator">Generator</option></select></div>
        <div class="mb-3"><label class="form-label fw-semibold">Brand</label><input name="asset_brand" class="form-control" maxlength="150" required></div>
        <div class="mb-3"><label class="form-label fw-semibold">Asset Details</label><textarea name="asset_details" class="form-control" rows="4" placeholder="Model, capacity, location, serial details, etc."></textarea></div>
        <button class="btn btn-primary w-100 rounded-pill fw-bold">Register Asset & Generate ID</button>
      </form>
    </div></div>
    <div class="col-lg-8"><div class="panel p-4"><h5 class="fw-bold mb-3">Registered Assets</h5>
      <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Asset ID</th><th>Type</th><th>Brand</th><th>Service History</th><th>Registered</th></tr></thead><tbody>
      <?php foreach ($assets as $a): ?><tr><td><span class="asset-id"><?= htmlspecialchars($a['asset_id']) ?></span></td><td><?= htmlspecialchars($a['asset_type']) ?></td><td><?= htmlspecialchars($a['asset_brand'] ?? '') ?></td><td><?= (int)$a['service_count'] ?> request(s)</td><td><?= htmlspecialchars(date('d M Y', strtotime($a['created_at']))) ?></td></tr><?php endforeach; ?>
      <?php if (!$assets): ?><tr><td colspan="5" class="text-center text-secondary py-4">No assets registered yet.</td></tr><?php endif; ?>
      </tbody></table></div>
    </div></div>
  </div>
</div>
</body></html>
