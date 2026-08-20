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
  <title>My Maintenance Overview | ARN QuickFix Ltd.</title>
  
  <!-- Local high-performance framework Bootstrap stylesheet engine mapping -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">

  <style>
    :root {
      --primary-cyan: #00C2CB;
      --bg-slate: #F4F7F9;
      --text-dark: #333333;
      --border-gray: #E2E8F0;
    }
    body {
      background-color: var(--bg-slate);
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      color: var(--text-dark);
    }
    .maintenance-navbar {
      background-color: #FFFFFF;
      border-bottom: 1px solid var(--border-gray);
      padding: 15px 40px;
    }
    .brand-accent {
      color: var(--primary-cyan);
      font-weight: 700;
      font-size: 24px;
      text-decoration: none;
    }
    .schedule-panel {
      background: #FFFFFF;
      border: 1px solid var(--border-gray);
      border-radius: 8px;
      padding: 30px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.01);
    }
    .btn-back-figma {
      border: 1px solid var(--border-gray);
      background-color: #FFFFFF;
      color: #64748B;
      font-size: 12px;
      font-weight: 600;
      padding: 5px 20px;
      border-radius: 4px;
      text-decoration: none;
      transition: all 0.2s;
    }
    .btn-back-figma:hover {
      border-color: var(--primary-cyan);
      color: var(--primary-cyan);
    }
    .status-badge-overdue {
      background-color: #EF4444;
      color: #FFFFFF;
      font-size: 11px;
      font-weight: 700;
      padding: 4px 14px;
      border-radius: 20px;
      letter-spacing: 0.3px;
    }
  </style>
</head>
<body>

  <!-- ================= TOP NAVIGATION BAR (MATCHES FIGMA LAYOUT) ================= -->
  <nav class="navbar maintenance-navbar d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3">
      <a href="client-dashboard.php" class="brand-accent d-flex align-items-center gap-2">
        <img src="img/logo.svg.svg" alt="Logo" style="height: 38px; width: auto;" onerror="this.style.display='none';">
        <span>ARN QuickFix Ltd.</span>
      </a>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="text-secondary small fw-medium">Client: <strong class="text-dark"><?php echo htmlspecialchars($clientName, ENT_QUOTES, 'UTF-8'); ?></strong></span>
      <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold" style="font-size:12px;">Logout</a>
    </div>
  </nav>

  <!-- ================= MAIN LAYOUT SCHEDULE CONTAINER ================= -->
  <div class="container py-5">
    
    <!-- Title Row and Back Navigation Shortcut -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold m-0 text-dark" style="font-size: 24px; letter-spacing: -0.5px;">My Maintenance Schedule</h2>
      <a href="client-dashboard.php" class="btn-back-figma">Back</a>
    </div>

    <!-- Master Data Layout Sheet Matrix Panel -->
    <div class="schedule-panel">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
          <thead>
            <tr class="text-secondary" style="border-bottom: 2px solid var(--border-gray); font-weight: 700;">
              <th class="py-3" style="font-weight: 700 !important; width: 80px;">SL</th>
              <th class="py-3" style="font-weight: 700 !important;">Asset Type</th>
              <th class="py-3" style="font-weight: 700 !important;">Asset ID</th>
              <th class="py-3" style="font-weight: 700 !important;">Last Service</th>
              <th class="py-3" style="font-weight: 700 !important;">Next Due</th>
              <th class="py-3" style="font-weight: 700 !important;">Maintenance Type</th>
              <th class="py-3" style="font-weight: 700 !important;">Maintenance Status</th>
            </tr>
          </thead>
          <tbody>
            <?php
            // Pulls maintenance rows linked to this logged-in account email
            $stmt = $conn->prepare("SELECT asset_type, asset_id, last_service, next_due, maintenance_type, status FROM maintenance_schedules WHERE client_email = ? ORDER BY id DESC");
            if ($stmt) {
                $stmt->bind_param("s", $clientEmail);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $sl = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr style='border-bottom: 1px solid #F1F5F9;'>";
                        echo "<td class='text-secondary fw-semibold'>#" . $sl++ . ".</td>";
                        echo "<td class='text-dark fw-medium'>" . htmlspecialchars($row['asset_type']) . "</td>";
                        echo "<td class='fw-bold text-dark'>" . htmlspecialchars($row['asset_id']) . "</td>";
                        echo "<td class='text-secondary font-monospace'>" . htmlspecialchars($row['last_service']) . "</td>";
                        echo "<td class='text-secondary font-monospace'>" . htmlspecialchars($row['next_due']) . "</td>";
                        echo "<td class='text-dark fw-medium'>" . htmlspecialchars($row['maintenance_type']) . "</td>";
                        
                        // Overdue badge formatting matching Figma button look
                                                // ====================================================================
                        // FIXED THREE-COLOR STATUS BADGE GRID (CLEAN INLINE MERGE)
                        // ====================================================================
                        $dbStatus = strtolower(trim($row['status'] ?? 'active'));
                        $nextDueTarget = $row['next_due'] ?? '';
                        $currentCalendarDay = date('Y-m-d'); // Today: 2026-08-03

                        // 1. Compile exact style tokens based on database lifecycle steps
                        if ($dbStatus === 'overdue' || ($nextDueTarget < $currentCalendarDay && $dbStatus !== 'completed')) {
                            $badgeBgColor = '#EF4444'; // Red
                            $badgeLabelText = 'Overdue';
                        } elseif ($dbStatus === 'completed') {
                            $badgeBgColor = '#10B981'; // Green
                            $badgeLabelText = 'Completed';
                        } else {
                            $badgeBgColor = '#3B82F6'; // Blue
                            $badgeLabelText = 'Active';
                        }

                        // 2. Output the table cell data cleanly using a single echo stream
                        echo "<td>
                                <span class='badge px-3 py-1.5 fw-bold text-uppercase text-white text-center d-inline-block' 
                                      style='background-color: {$badgeBgColor} !important; font-size: 10px; border-radius: 50rem; min-width: 85px; letter-spacing: 0.3px;'>
                                  {$badgeLabelText}
                                </span>
                              </td>";
                        
                        echo "</tr>";

                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center text-muted py-4 fw-semibold font-monospace'>No scheduled maintenance lifecycles found for your assets.</td></tr>";
                }
                $stmt->close();
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>

  <!-- -->
</body>
</html>
<?php $conn->close(); ?>
