<?php
// 1. Initialize Active User Session and Force Authorization Guard Rails
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['email']) || !isset($_SESSION['name'])) {
    header("Location: login.php");
    exit();
}

$clientEmail = $_SESSION['email'];
$clientName = $_SESSION['name'];

// 2. Establish Secure Database Integration Network Link
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "arn_quickfix";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database Connection Error Trace: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Maintenance Schedule Overview Report</title>
  
  <!-- High performance framework Bootstrap stylesheet engine mapping -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  
  <style>
    body {
      background-color: #FFFFFF;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      color: #1E293B;
    }
    .report-header {
      border-bottom: 3px solid #0F172A;
      padding-bottom: 15px;
      margin-bottom: 30px;
    }
    .report-logo {
      height: 45px;
      width: auto;
      object-fit: contain;
      margin-right: 12px;
    }
    .status-text-overdue {
      color: #EF4444 !important;
      font-weight: 700;
    }
    @media print {
      body { padding: 0; margin: 0; }
      .no-print { display: none; }
    }
  </style>
</head>
<body onload="window.print();"> <!-- Opens print / save interface immediately on load -->

  <div class="container-fluid py-4 px-4">
    
    <!-- ================= REPORT TOP HEADER BANNER ================= -->
    <div class="report-header d-flex justify-content-between align-items-end">
      <div class="d-flex align-items-center">
        <!-- COMPANY LOGO EMBEDDING: Standard local img location asset query map -->
        <img src="img/logo.svg.svg" alt="Logo" class="report-logo" onerror="this.src='img/logo.png'; this.onerror=function(){this.style.display='none';};">
        <div>
          <h1 class="fw-bold m-0" style="color: #00C2CB; letter-spacing: -0.5px; font-size: 28px;">ARN QuickFix Ltd.</h1>
          <p class="text-muted m-0 small fw-semibold font-monospace text-uppercase" style="font-size: 11px;">Official Assets Maintenance Lifecycle Schedule Report</p>
        </div>
      </div>
      <div class="text-end">
        <h5 class="fw-bold m-0 text-dark">Master Maintenance Profile</h5>
        <span class="small font-monospace text-secondary" style="font-size: 12px;">Email: <?php echo htmlspecialchars($clientEmail); ?></span>
      </div>
    </div>

    <!-- ================= CLIENT ACCOUNT CONTEXT DETAILS CARD ================= -->
    <div class="mb-4 bg-light p-3 rounded-3 border border-secondary-subtle" style="font-size: 13.5px; background-color: #F8FAFC !important;">
      <div class="row g-2">
        <div class="col-sm-6"><strong>Client Name Profile:</strong> <span class="text-dark fw-bold"><?php echo htmlspecialchars($clientName); ?></span></div>
        <div class="col-sm-6 text-sm-end"><strong>Generated Timestamp Log:</strong> <span class="font-monospace text-secondary fw-bold"><?php echo date('Y-m-d H:i:s'); ?></span></div>
      </div>
    </div>

    <!-- ================= DATA LEDGER TABLE MATRIX ================= -->
    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle" style="font-size: 12.5px;">
        <thead class="table-dark text-uppercase font-monospace" style="font-size: 11.5px;">
          <tr>
            <th class="py-3" style="width: 60px; font-weight: 700 !important;">SL</th>
            <th class="py-3" style="font-weight: 700 !important;">Asset Type</th>
            <th class="py-3" style="font-weight: 700 !important;">Asset ID</th>
            <th class="py-3" style="font-weight: 700 !important;">Last Service Date</th>
            <th class="py-3" style="font-weight: 700 !important;">Next Due Assessment</th>
            <th class="py-3" style="font-weight: 700 !important;">Cycle Frequency Type</th>
            <th class="py-3" style="font-weight: 700 !important;">Maintenance Status</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Pulls maintenance rows linked to this logged-in account email directly
          $stmt = $conn->prepare("SELECT asset_type, asset_id, last_service, next_due, maintenance_type, status FROM maintenance_schedules WHERE client_email = ? ORDER BY id DESC");
          if ($stmt) {
              $stmt->bind_param("s", $clientEmail);
              $stmt->execute();
              $result = $stmt->get_result();
              
              if ($result->num_rows > 0) {
                  $sl = 1;
                  while ($row = $result->fetch_assoc()) {
                      echo "<tr>";
                      echo "<td class='text-secondary font-monospace fw-bold'>#" . $sl++ . ".</td>";
                      echo "<td class='text-dark fw-bold'>" . htmlspecialchars($row['asset_type']) . "</td>";
                      echo "<td class='fw-bold text-dark'>#" . htmlspecialchars($row['asset_id']) . "</td>";
                      echo "<td class='font-monospace text-secondary'>" . htmlspecialchars($row['last_service']) . "</td>";
                      echo "<td class='font-monospace text-danger fw-bold'>" . htmlspecialchars($row['next_due']) . "</td>";
                      echo "<td class='text-dark fw-medium text-capitalize'>" . htmlspecialchars($row['maintenance_type']) . "</td>";
                      
                      // Highlight Overdue records in bold red text for printing clear metrics
                      $statusStyle = (strtolower($row['status']) === 'overdue') ? "class='status-text-overdue text-uppercase'" : "class='fw-bold text-success text-uppercase'";
                      echo "<td " . $statusStyle . ">" . htmlspecialchars($row['status']) . "</td>";
                      echo "</tr>";
                  }
              } else {
                  echo "<tr><td colspan='7' class='text-center text-muted py-4 fw-bold font-monospace'>No active scheduled maintenance lifecycles found for your profile account.</td></tr>";
              }
              $stmt->close();
          }
          ?>
        </tbody>
      </table>
    </div>
    
  </div>

</body>
</html>
<?php 
if (isset($conn)) {
    $conn->close(); 
} 
?>
