<?php
require_once "config.php";
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'technician') {
  header("Location: login.php");
  exit;
}

$tech_id = (int)$_SESSION['user_id'];
$id = (int)($_GET['id'] ?? 0);

$error = "";
$success = "";
$row = null;

if ($id <= 0) {
  die("Invalid request id.");
}

/* ================================
   Helpers: check columns exist
   ================================ */
function col_exists(PDO $pdo, string $table, string $col): bool {
  $st = $pdo->prepare("
    SELECT COUNT(*)
    FROM information_schema.columns
    WHERE table_schema = DATABASE()
      AND table_name = ?
      AND column_name = ?
  ");
  $st->execute([$table, $col]);
  return (int)$st->fetchColumn() > 0;
}

/* ================================
   Technician sends update to manager
   ================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_note'])) {
  $note = trim($_POST['note'] ?? "");
  $suggested_status = trim($_POST['suggested_status'] ?? "");

  if ($note === "") {
    $error = "Work note is required.";
  } else {
    try {
      // ✅ Make sure request belongs to this technician
      $chk = $pdo->prepare("SELECT id FROM client_requests WHERE id=? AND technician_id=? LIMIT 1");
      $chk->execute([$id, $tech_id]);

      if (!$chk->fetch()) {
        $error = "❌ You cannot send updates for this request (not assigned to you).";
      } else {

        $has_is_read = col_exists($pdo, "technician_notes", "is_read");
        $has_suggest = col_exists($pdo, "technician_notes", "suggested_status");

        // ✅ Insert note (plus suggested_status if column exists)
        if ($has_is_read && $has_suggest) {
          $stmt = $pdo->prepare("
            INSERT INTO technician_notes (request_id, technician_id, note, suggested_status, is_read)
            VALUES (?, ?, ?, ?, 0)
          ");
          $stmt->execute([$id, $tech_id, $note, $suggested_status]);

        } elseif ($has_is_read && !$has_suggest) {
          // fallback: store suggested status inside note text
          $final_note = $note . "\n\nSuggested Status: " . $suggested_status;

          $stmt = $pdo->prepare("
            INSERT INTO technician_notes (request_id, technician_id, note, is_read)
            VALUES (?, ?, ?, 0)
          ");
          $stmt->execute([$id, $tech_id, $final_note]);

        } else {
          // fallback: if no is_read column exists
          $final_note = $note . "\n\nSuggested Status: " . $suggested_status;

          $stmt = $pdo->prepare("
            INSERT INTO technician_notes (request_id, technician_id, note)
            VALUES (?, ?, ?)
          ");
          $stmt->execute([$id, $tech_id, $final_note]);
        }

        $success = "✅ Work update sent to manager successfully.";
      }
    } catch (Throwable $e) {
      $error = "❌ Failed to submit work update.";
    }
  }
}

/* ================================
   Load assigned request
   ================================ */
try {
  $st = $pdo->prepare("
    SELECT *
    FROM client_requests
    WHERE id = ? AND technician_id = ?
    LIMIT 1
  ");
  $st->execute([$id, $tech_id]);
  $row = $st->fetch(PDO::FETCH_ASSOC);

  if (!$row) {
    die("Request not found or not assigned to you.");
  }
} catch (Throwable $e) {
  die("Error loading request.");
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Request Details | Technician</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
</head>

<body class="p-4" style="background:#f7fbfc;">

<div class="container">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="fw-bold mb-0">Request #<?php echo (int)$row['id']; ?></h3>
    <a href="technician-dashboard.php" class="btn btn-outline-dark rounded-pill">Back</a>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
  <?php endif; ?>

  <!-- Request Info -->
  <div class="card p-4 mb-3">
    <div class="row g-3">
      <div class="col-md-6"><b>Asset ID:</b> <?php echo htmlspecialchars($row['elevator_id']); ?></div>
      <div class="col-md-6"><b>Category:</b> <?php echo htmlspecialchars($row['category']); ?></div>
      <div class="col-md-6"><b>Contact:</b> <?php echo htmlspecialchars($row['contact_method']); ?></div>
      <div class="col-12">
        <b>Description:</b><br>
        <?php echo nl2br(htmlspecialchars($row['description'])); ?>
      </div>
      <div class="col-12"><b>Current Status (Client sees):</b> <?php echo htmlspecialchars($row['status']); ?></div>
      <div class="col-12 text-secondary"><b>Created:</b> <?php echo htmlspecialchars($row['created_at']); ?></div>
    </div>
  </div>

  <!-- Technician Work Update -->
  <div class="card p-4">
    <h5 class="fw-bold mb-3">Send Work Update to Manager</h5>

    <form method="POST" class="row g-3">

      <div class="col-12">
        <label class="form-label fw-semibold">Work Performed *</label>
        <textarea
          name="note"
          class="form-control"
          rows="4"
          placeholder="Describe work done, parts replaced, observations..."
          required></textarea>
      </div>

      <div class="col-md-6">
        <label class="form-label fw-semibold">Suggested Status</label>
        <select name="suggested_status" class="form-select">
          <option value="Processing">Processing</option>
          <option value="Pending">Pending</option>
          <option value="Completed">Completed</option>
        </select>
        <small class="text-muted">Manager will approve final status (client does not see your change)</small>
      </div>

      <div class="col-md-6 d-grid">
        <button class="btn btn-dark" type="submit" name="submit_note">
          <i class="fa-solid fa-paper-plane me-1"></i> Send to Manager
        </button>
      </div>

    </form>
  </div>

</div>

</body>
</html>