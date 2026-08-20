<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Force authorization checks for managers strictly
if (!isset($_SESSION['email']) || (isset($_SESSION['role']) && strtolower($_SESSION['role']) !== 'manager')) {
    header("Location: login.php");
    exit();
}

$managerEmail = $_SESSION['email'];
$managerName = $_SESSION['name'] ?? 'Operations Manager';

// Connect to Database
$conn = new mysqli("127.0.0.1", "root", "", "arn_quickfix");
if ($conn->connect_error) {
    die("Database Connection Failure: " . $conn->connect_error);
}

// --------------------------------------------------------------------
// PROCESS ACTION: CLOSE OUT AND RESOLVE CUSTOMER ESCALATION
// --------------------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action_type'])) {
    if ($_POST['action_type'] === 'resolve_complaint') {
        $ticket_id = (int)($_POST['ticket_id'] ?? 0);
        $resolution_status = $_POST['resolution_status'] ?? 'completed'; // Falls back to completed/resolved

        if ($ticket_id > 0) {
            // Update request row: appends resolution text note and restores structural integrity
            $stmt = $conn->prepare("UPDATE service_requests SET status = ?, location = CONCAT(location, ' [Escalation Resolved by Management]') WHERE id = ?");
            $stmt->bind_param("si", $resolution_status, $ticket_id);
            
            if ($stmt->execute()) {
                $_SESSION['comp_alert_msg'] = "Success! Dispute Ticket #{$ticket_id} has been audit-cleared and closed.";
            } else {
                $_SESSION['comp_alert_err'] = "Database Error: Failed updating row metrics.";
            }
            $stmt->close();
        }
        header("Location: manager_complaints.php");
        exit();
    }
}

$actionMessage = $_SESSION['comp_alert_msg'] ?? ''; unset($_SESSION['comp_alert_msg']);
$actionError = $_SESSION['comp_alert_err'] ?? ''; unset($_SESSION['comp_alert_err']);

// Count ongoing unresolved complaints rows
$activeCount = 0;
$qCount = $conn->query("SELECT COUNT(*) as total FROM service_requests WHERE status = 'complaint_raised'");
if ($qCount) {
    $cData = $qCount->fetch_assoc();
    $activeCount = $cData['total'] ?? 0;
}

// Fetch all tickets flagged with complaint escalation logs
$complaintsLedger = $conn->query("SELECT id, client_email, phone, asset_type, asset_brand, asset_id, problem_category, allocated_part, part_price, amount, complaint_text, created_at FROM service_requests WHERE status = 'complaint_raised' ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Complaints | ARN QuickFix Ltd.</title>
  
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">

  <style>
    :root {
      --primary-cyan: #00C2CB;
      --deep-navy: #0F172A;
      --slate-gray: #475569;
      --bg-canvas: #F8FAFC;
      --border-light: #E2E8F0;
    }
    body {
      background-color: var(--bg-canvas);
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      color: var(--deep-navy);
      -webkit-font-smoothing: antialiased;
    }
    .manager-navbar { background-color: #FFFFFF; border-bottom: 1px solid var(--border-light); padding: 16px 45px; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02); }
    .brand-accent { font-weight: 800; font-size: 24px; color: var(--deep-navy); text-decoration: none; letter-spacing: -0.5px; }
    .brand-accent span { color: var(--primary-cyan); }
    
    .complaint-card {
      background: #FFFFFF;
      border: 1px solid var(--border-light);
      border-radius: 16px;
      padding: 26px;
      box-shadow: 0 4px 20px rgba(15, 23, 42, 0.01);
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .complaint-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 25px rgba(15, 23, 42, 0.015);
    }
    .btn-resolve-action {
      background-color: #EF4444;
      color: #FFFFFF;
      font-weight: 700;
      font-size: 12px;
      border: none;
      border-radius: 6px;
      height: 38px;
      transition: all 0.2s;
    }
    .btn-resolve-action:hover { background-color: #DC2626; }
    
    .notes-box-layout {
      background-color: #FFF7ED;
      border: 1px solid #FFEDD5;
      color: #9A3412;
      border-radius: 8px;
      padding: 16px;
      font-size: 13.5px;
      line-height: 1.5;
    }
  </style>
</head>
<body>

  <!-- ================= TOP NAVIGATION BAR ================= -->
  <nav class="navbar manager-navbar d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3">
      <a href="manager-dashboard.php" class="brand-accent d-flex align-items-center gap-2">
        <img src="img/logo.svg.svg" alt="Logo" style="height: 35px; width: auto;" onerror="this.style.display='none';">
        <span>ARN QuickFix Ltd.</span>
      </a>
    </div>
    <div class="d-flex align-items-center gap-3">
      <div class="d-flex align-items-center gap-2 bg-light px-3 py-1.5 rounded-pill border" style="border-color: #E2E8F0 !important;">
        <div style="width: 8px; height: 8px; background-color: #EF4444;" class="rounded-circle"></div>
        <span class="small fw-semibold text-secondary" style="font-size: 13px;">
          Unresolved Claims: <strong class="text-danger fw-bold"><?php echo $activeCount; ?></strong>
        </span>
      </div>
      <a href="manager-dashboard.php" class="btn btn-sm btn-outline-secondary rounded-pill px-4 fw-bold" style="font-size: 12.5px; height: 34px; display: flex; align-items: center;">Back to Hub</a>
    </div>
  </nav>

  <!-- ================= MASTER CANVAS WORKING TERMINAL ================= -->
  <div class="container py-5" style="max-width: 960px;">
    
    <div class="mb-5">
      <h2 class="fw-bold m-0" style="font-size: 26px; letter-spacing: -0.5px;">Customer Complaint Desk</h2>
      <p class="text-muted m-0 small fw-medium mt-1">Review live escalations submitted regarding performance failures, billing discrepancies, or field technician conduct.</p>
    </div>

    <!-- System Interface Operations Alerts Layer Banners -->
    <?php if (!empty($actionMessage)): ?>
      <div class="alert alert-success border-0 shadow-sm rounded-3 p-3 mb-4 fw-bold font-monospace" style="border-left: 5px solid #10B981 !important; color:#065F46; font-size:13px;">
        🎉 <?php echo $actionMessage; ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($actionError)): ?>
      <div class="alert alert-danger border-0 shadow-sm rounded-3 p-3 mb-4 fw-bold font-monospace" style="border-left: 5px solid #EF4444 !important; color:#991B1B; font-size:13px;">
        ⚠️ System Alert: <?php echo $actionError; ?>
      </div>
    <?php endif; ?>

    <!-- ================= DYNAMIC LIST LOOP CANVAS ROWS ================= -->
    <div class="d-flex flex-column gap-4">
      <?php 
      if ($complaintsLedger && $complaintsLedger->num_rows > 0): 
        while ($row = $complaintsLedger->fetch_assoc()):
          $allocatedPart = trim($row['allocated_part'] ?? '');
          $partPrice = (float)($row['part_price'] ?? 0.00);
          $finalBill = (float)($row['amount'] ?? 0.00);
      ?>
        <div class="complaint-card">
          <!-- Row 1: Header Meta Details String Layout -->
          <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 border-bottom pb-3 mb-3" style="border-color: #F1F5F9 !important;">
            <div>
              <span class="badge bg-danger text-uppercase px-2.5 py-1 mb-1.5 font-monospace" style="font-size: 10px; font-weight:700;">Ticket #<?php echo $row['id']; ?></span>
              <h5 class="fw-bold text-dark m-0" style="font-size: 16px;"><?php echo htmlspecialchars($row['asset_brand'] . ' ' . $row['asset_type'] . " (" . $row['asset_id'] . ")"); ?></h5>
            </div>
            <div class="text-md-end">
              <span class="text-muted small font-monospace d-block fw-semibold" style="font-size:12px;">Filed: <?php echo date('d-m-Y | h:i A', strtotime($row['created_at'])); ?></span>
              <span class="text-secondary small font-monospace d-block mt-0.5" style="font-size:11.5px;">Client: <strong><?php echo htmlspecialchars($row['client_email']); ?></strong></span>
            </div>
          </div>

          <!-- Row 2: Customer Text Feedback Notes Container Box -->
          <div class="mb-3.5">
            <div class="small fw-bold text-secondary text-uppercase mb-1.5" style="font-size: 10.5px; letter-spacing: 0.5px;">Escalation Notes & Grievance Context</div>
            <div class="notes-box-layout">
              💬 "<?php echo htmlspecialchars($row['complaint_text']); ?>"
            </div>
          </div>

          <!-- Row 3: Financial Settlement Audit Parameters -->
          <div class="row g-3 bg-light p-3 rounded-3 mb-4 mx-0 border" style="border-color: #E2E8F0 !important;">
            <div class="col-sm-4">
              <div class="text-muted small font-monospace text-uppercase" style="font-size:10px;">Claim Category</div>
              <strong class="text-dark small" style="font-size:13px;"><?php echo htmlspecialchars($row['problem_category']); ?></strong>
            </div>
            <div class="col-sm-4">
              <div class="text-muted small font-monospace text-uppercase" style="font-size:10px;">Warehouse Parts Drawn</div>
              <strong class="text-secondary small" style="font-size:12.5px;">
                <?php echo !empty($allocatedPart) ? htmlspecialchars($allocatedPart) . " (৳" . number_format($partPrice) . ")" : 'None Logged'; ?>
              </strong>
            </div>
            <div class="col-sm-4 text-sm-end">
              <div class="text-muted small font-monospace text-uppercase" style="font-size:10px;">Total Collected Bill</div>
              <strong class="text-dark font-monospace" style="font-size:14.5px;">৳<?php echo number_format($finalBill, 2); ?></strong>
            </div>
          </div>

          <!-- Action Interlock Form Row Form Panel Layout -->
          <form action="manager_complaints.php" method="POST" class="d-flex flex-column flex-sm-row justify-content-end align-items-stretch align-items-sm-center gap-2 border-top pt-3" style="border-color: #F1F5F9 !important;">
            <input type="hidden" name="action_type" value="resolve_complaint">
            <input type="hidden" name="ticket_id" value="<?php echo $row['id']; ?>">
            
            <div style="width: 240px;" class="w-100">
              <select name="resolution_status" class="form-select form-select-custom w-100 font-monospace" style="height:38px; font-size:12.5px; background-color:#F8FAFC;" required>
                <option value="completed" selected>Mark Dispute Resolved</option>
                <option value="processing">Re-assign to Field Technician</option>
                <option value="pending">Move Back to New Inbox Queue</option>
              </select>
            </div>
            <button type="submit" class="btn btn-resolve-action px-4 text-uppercase">Execute Resolution</button>
          </form>

        </div>
      <?php 
        endwhile; 
      else: 
      ?>
        <!-- Fallboard State Display Interface Panel -->
        <div class="text-center py-5 border rounded-4 bg-white text-muted font-monospace fw-bold" style="box-shadow: 0 4px 15px rgba(0,0,0,0.005);">
          🍃 Pristine Pipeline: No customer complaint escalation tickets currently flagged inside the database rows.
        </div>
      <?php endif; ?>
    </div>

  </div> <!-- Close Canvas Container Wrapper Container -->

  <!-- Framework Architecture Core Integration Libraries -->
</body>
</html>
<?php 
// Close network execution streams cleanly on exit
if (isset($conn)) { 
    $conn->close(); 
} 
?>
