<?php
require_once "config.php";
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'technician') {
  header("Location: login.php");
  exit;
}

$tech_id = (int)$_SESSION['user_id'];

function status_badge(string $status): string {
  $s = strtolower(trim($status));
  $map = [
    "submitted"  => "secondary",
    "assigned"   => "info",
    "processing" => "warning",
    "pending"    => "dark",
    "completed"  => "success",
    "closed"     => "primary",
  ];
  $color = $map[$s] ?? "secondary";
  $label = ucfirst($s);
  return "<span class=\"badge rounded-pill text-bg-{$color}\">{$label}</span>";
}

$rows = [];
$error = "";
try {
  $st = $pdo->prepare("
    SELECT id, elevator_id, category, description, status, created_at
    FROM client_requests
    WHERE technician_id = ?
    ORDER BY id DESC
  ");
  $st->execute([$tech_id]);
  $rows = $st->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
  $error = "Failed to load your requests.";
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>My Requests | Technician</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4" style="background:#f7fbfc;">
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="fw-bold mb-0">My Assigned Requests</h3>
    <a href="technician_dashboard.php" class="btn btn-outline-dark rounded-pill">Back</a>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
  <?php endif; ?>

  <div class="card p-3">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead>
          <tr class="text-secondary">
            <th>ID</th><th>Asset ID</th><th>Category</th><th>Status</th><th>Created</th><th></th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($rows)): ?>
            <tr class="text-secondary"><td colspan="6">No assigned requests.</td></tr>
          <?php else: ?>
            <?php foreach ($rows as $r): ?>
              <tr>
                <td class="fw-semibold">#<?php echo (int)$r['id']; ?></td>
                <td class="font-monospace fw-bold"><?php echo htmlspecialchars($r['elevator_id']); ?></td>
                <td><?php echo htmlspecialchars($r['category']); ?></td>
                <td><?php echo status_badge($r['status']); ?></td>
                <td class="text-secondary"><?php echo htmlspecialchars($r['created_at']); ?></td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-dark rounded-pill"
                     href="technician-request-details.php?id=<?php echo (int)$r['id']; ?>">
                    Open
                  </a>
                </td>
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