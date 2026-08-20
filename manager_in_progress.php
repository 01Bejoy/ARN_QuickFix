<?php
// 1. Initialize Active User Session and Force Authorization Guard Rails
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

// 2. Establish High-Speed Database Connection
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "arn_quickfix";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database Connection Error: " . $conn->connect_error);
}

// 3. Pull Live Active Job Dataset Rows (Only 'processing' status)
// FIXED QUERY: Fetches your fresh inventory parameters directly from your data table rows
$query = "SELECT id, client_email, asset_type, asset_brand, asset_id, problem_category, allocated_part, part_price, phone, location, payment_method, status FROM service_requests WHERE status = 'processing' ORDER BY id DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Active In-Progress Repairs | ARN QuickFix Ltd.</title>
  
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
    .manager-navbar {
      background-color: #FFFFFF;
      border-bottom: 1px solid var(--border-light);
      padding: 16px 45px;
      box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02);
    }
    .brand-accent {
      font-weight: 800;
      font-size: 24px;
      color: var(--deep-navy);
      text-decoration: none;
      letter-spacing: -0.5px;
    }
    .brand-accent span { color: var(--primary-cyan); }
    
    .ledger-container-card {
      background: #FFFFFF;
      border: 1px solid var(--border-light);
      border-radius: 20px;
      padding: 35px;
      box-shadow: 0 10px 30px rgba(15, 23, 42, 0.015);
    }
    .table th {
      background-color: #F8FAFC !important;
      color: var(--slate-gray);
      font-size: 11.5px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      padding: 16px 12px;
      border-bottom: 2px solid var(--border-light);
    }
    .table td {
      padding: 16px 12px;
      font-size: 13.5px;
      vertical-align: middle;
      color: #334155;
    }
      font-size: 11px;
      font-weight: 700;
      padding: 4px 10px;
      border-radius: 20px;
      text-transform: uppercase;
    }
    
    .status-pulse-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background-color: #EFF6FF;
      color: #2563EB;
      border: 1px solid #BFDBFE;
      font-size: 11px;
      font-weight: 700;
      padding: 5px 12px;
      border-radius: 20px;
      text-transform: uppercase;
    }
    .pulse-dot {
      width: 6px;
      height: 6px;
      background-color: #2563EB;
      border-radius: 50%;
      animation: blinker 1.5s linear infinite;
    }
    @keyframes blinker {
      50% { opacity: 0; }
    }
  </style>
</head>
<body>

  <!-- ================= TOP NAVIGATION BAR ================= -->
  <nav class="navbar manager-navbar d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3">
      <a href="manager-dashboard.php" class="brand-accent d-flex align-items-center gap-2">
        <img src="img/logo.svg.svg" alt="Logo" style="height: 35px; width: auto;" onerror="this.style.display='none';">
        <span>ARN <span>QuickFix Ltd.</span></span>
      </a>
    </div>
    
    <div class="d-flex align-items-center gap-3">
      <div class="d-flex align-items-center gap-2 me-2 bg-light px-3 py-1.5 rounded-pill border" style="border-color: #E2E8F0 !important;">
        <div style="width: 8px; height: 8px; background-color: #10B981;" class="rounded-circle"></div>
        <span class="small fw-semibold text-secondary" style="font-size: 13px;">
          Manager: <strong class="text-dark fw-bold"><?php echo htmlspecialchars($managerName); ?></strong>
        </span>
      </div>
      <a href="manager-dashboard.php" class="btn btn-sm btn-outline-secondary rounded-pill px-4 fw-bold" style="font-size: 12.5px; height: 34px; display: flex; align-items: center;">Back to Hub</a>
    </div>
  </nav>

  <!-- ================= MASTER MONITOR CANVAS WRAPPER ================= -->
  <div class="container py-5" style="max-width: 1200px;">
    
    <!-- Header Summary Title Layout -->
    <div class="d-flex justify-content-between align-items-end mb-4">
      <div>
        <h2 class="fw-bold m-0" style="font-size: 26px; letter-spacing: -0.5px;">In Progress Tasks</h2>
        <p class="text-muted m-0 small fw-medium mt-1">Real-time status tracking of all repair jobs currently active on technician fields.</p>
      </div>
      <span class="badge bg-primary rounded-pill font-monospace px-3 py-2" style="font-size: 11px; background-color: #2563EB !important;">
        LIVE ACTIVE JOBS: <?php echo $result ? $result->num_rows : 0; ?>
      </span>
    </div>

    <!-- ================= DATA GRID SHEET LEDGER CONTAINER ================= -->
    <div class="ledger-container-card">
      <div class="table-responsive">
        <table class="table align-middle m-0">
          <thead>
            <tr>
              <th style="width: 65px; text-align: center;">SL</th>
              <th>Asset Information</th>
              <th>Issue Diagnostics & Estimates</th>
              <th>Contact Node</th>
              <th>Location & Dispatch Assignment</th>
              <th style="width: 160px; text-align: center;">Live Status</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            if ($result && $result->num_rows > 0): 
              $serialNumberCounter = 1; 
              while ($row = $result->fetch_assoc()): 
            ?>
                <tr>
                  <!-- Clean Human Sequential Serial Order -->
                  <td class="font-monospace fw-bold text-secondary text-center" style="font-size: 13.5px;">
                    <?php echo $serialNumberCounter++; ?>
                  </td>
                  
                  <!-- Asset Parameters Grid -->
                  <td>
                    <span class="d-block fw-bold text-dark mb-0.5"><?php echo htmlspecialchars($row['asset_type'] ?? ''); ?></span>
                    <span class="text-muted font-monospace small" style="font-size: 11.5px;"><?php echo htmlspecialchars($row['asset_brand'] ?? ''); ?> — <strong><?php echo htmlspecialchars($row['asset_id'] ?? ''); ?></strong></span>
                  </td>
                  
                                   <!-- Diagnostics with Exact Client-Matched Baseline Price Switches & Dynamic Inventory Check -->
                  <td>
                    <?php 
                      $currentProblem = $row['problem_category'] ?? '';
                      $allocatedPart = trim($row['allocated_part'] ?? '');
                      $partPrice = (float)($row['part_price'] ?? 0.00);
                      $priceGuideTag = "";

                      switch ($currentProblem) {
                          case 'Component Repair':    $priceGuideTag = " — [৳4,500]"; break;
                          case 'Part Replacement':     $priceGuideTag = " — [৳3,000]"; break;
                          case 'Modernization':        $priceGuideTag = " — [৳15,000]"; break;
                          case 'Routine Servicing':    $priceGuideTag = " — [৳2,000]"; break;
                          case 'Emergency Breakdown':  $priceGuideTag = " — [৳5,000]"; break;
                          case 'Basic Servicing':      $priceGuideTag = " — [৳600]"; break;
                          case 'Deep Cleaning':        $priceGuideTag = " — [৳1,200]"; break;
                          case 'Duct Cleaning':        $priceGuideTag = " — [৳5,000]"; break;
                          case 'Gas Refill':           $priceGuideTag = " — [৳2,500]"; break;
                          case 'Electrical Repair':    $priceGuideTag = " — [৳1,500]"; break;
                          case 'Compressor Repair':    $priceGuideTag = " — [৳4,000]"; break;
                          case 'Preventative Inspection': $priceGuideTag = " — [৳3,500]"; break;
                          case 'Fault Code Diagnostic':   $priceGuideTag = " — [৳1,800]"; break;
                          case 'Engine Rebuild':          $priceGuideTag = " — [৳25,000]"; break;
                          case 'Component Repairs':       $priceGuideTag = " — [৳6,000]"; break;
                          case 'Advanced Testing':        $priceGuideTag = " — [৳8,000]"; break;
                          case 'Fuel Polishing':          $priceGuideTag = " — [৳4,500]"; break;
                          default: $priceGuideTag = ""; break;
                      }
                    ?>
                    <span class="d-block text-dark fw-medium mb-1.5">
                      <?php echo htmlspecialchars($currentProblem); ?> 
                      <small class="text-secondary fw-semibold font-monospace" style="font-size: 11.5px;"><?php echo $priceGuideTag; ?></small>
                    </span>

                    <!-- DYNAMIC ADAPTIVE TECHNICIAN ON-SITE PARTS REVEALER -->
                    <div class="mb-1.5">
                      <?php if (!empty($allocatedPart)): ?>
                        <!-- Renders automatically if the technician logs an inventory part item from the field -->
                        <span class="badge font-monospace" style="font-size: 10px; padding: 2px 6px; background-color: #F8FAFC; color: #475569; border: 1px solid #E2E8F0; border-radius: 4px; display: inline-block;">
                          📦 Allocated: <?php echo htmlspecialchars($allocatedPart); ?> (+৳<?php echo number_format($partPrice); ?>)
                        </span>
                      <?php else: ?>
                        <!-- Standard standby alert showing that the job is currently being investigated -->
                        <span class="badge font-monospace" style="font-size: 10px; padding: 2px 6px; background-color: #FFFBEB; color: #D97706; border: 1px solid #FEF3C7; border-radius: 4px; display: inline-block;">
                          ⏳ Technician on-site diagnosing parts requirements...
                        </span>
                      <?php endif; ?>
                    </div>

                  </td>

                  
                  <!-- Customer Communications Info -->
                  <td>
                    <span class="d-block text-dark fw-semibold" style="font-size: 13px;"><?php echo htmlspecialchars($row['phone'] ?? ''); ?></span>
                    <span class="text-muted font-monospace d-block" style="font-size: 11px;"><?php echo htmlspecialchars($row['client_email'] ?? ''); ?></span>
                  </td>
                  
                  <!-- Customer Location & Real-Time Dispatch Allocation Node -->
                  <td>
                    <span class="d-block text-dark fw-medium" style="font-size: 13.5px;"><?php echo htmlspecialchars($row['location'] ?? ''); ?></span>
                    <span class="text-muted small font-monospace d-block mt-0.5" style="font-size: 11px;">Payment Method: <strong><?php echo htmlspecialchars($row['payment_method'] ?? 'Not Set'); ?></strong></span>
                  </td>
                  
                  <!-- Live Tracking Pulse Badge Column -->
                  <td class="text-center">
                    <div class="status-pulse-badge">
                      <div class="pulse-dot"></div>
                      <span>On Field</span>
                    </div>
                  </td>
                </tr>
              <?php 
              endwhile; 
            else: 
            ?>
              <!-- Fallback state if database has zero active processing rows -->
              <tr>
                <td colspan="6" class="text-center py-5 text-muted font-monospace fw-bold" style="background-color: #FFFFFF;">
                  ⚙️ Standby State: There are zero active repair tickets running on technician fields right now.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div> <!-- Close Ledger Container Card -->
  </div> <!-- Close Master Monitor Canvas Wrapper Container -->

  <!-- Bootstrap 5 JavaScript Bundle Layout Core Engine CDN Injection -->
</body>
</html>
<?php 
// Terminate your database integration connection thread cleanly on page exit
if (isset($conn)) { 
    $conn->close(); 
} 
?>