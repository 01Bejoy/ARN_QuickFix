<?php
require_once "config.php";
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'client') {
  header("Location: login.php");
  exit;
}
$user_id = (int)$_SESSION['user_id'];

function status_badge(string $status): string {
  $map = [
    'submitted'=>'secondary','assigned'=>'info','processing'=>'warning',
    'pending'=>'dark','completed'=>'success','closed'=>'primary'
  ];
  $key = strtolower(trim($status));
  $color = $map[$key] ?? 'secondary';
  return "<span class='badge rounded-pill text-bg-{$color}'>".ucfirst($key)."</span>";
}

$requests = [];
$stmt = $pdo->prepare("SELECT * FROM client_requests WHERE user_id=? ORDER BY id DESC");
$stmt->execute([$user_id]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>My Requests</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background:#f7fbfc">

<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="fw-bold">My Requests</h2>
      <p class="text-muted mb-0">All service requests you submitted</p>
    </div>
    <a href="client-dashboard.php" class="btn btn-outline-dark rounded-pill">← Back</a>
  </div>

  <div class="bg-white p-4 rounded-4 shadow-sm">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Asset ID</th>
            <th>Category</th>
            <th>Status</th>
            <th>Created</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$requests): ?>
            <tr><td colspan="5" class="text-center text-muted">No requests yet.</td></tr>
          <?php else: ?>
            <?php foreach($requests as $r): ?>
              <tr>
                <td class="fw-semibold">#<?= (int)$r['id'] ?></td>
                <td><?= htmlspecialchars($r['elevator_id']) ?></td>
                <td><?= htmlspecialchars($r['category']) ?></td>
                <td><?= status_badge($r['status']) ?></td>
                <td><?= htmlspecialchars($r['created_at']) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

</body>
</html>