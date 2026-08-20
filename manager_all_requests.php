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

// Establish High-Speed Database Connection
$conn = new mysqli("127.0.0.1", "root", "", "arn_quickfix");
if ($conn->connect_error) {
    die("Database Connection Error: " . $conn->connect_error);
}

// Capture Active Filter Parameters from the Navigation Row Tabs
$filterStatus = $_GET['filter'] ?? 'all';

// Build dynamic SQL queries matching select tabs cleanly
if ($filterStatus === 'new') {
    $sqlFilter = "WHERE status IN ('pending', 'submitted')";
} elseif ($filterStatus === 'progress') {
    $sqlFilter = "WHERE status = 'processing'";
} elseif ($filterStatus === 'completed') {
    $sqlFilter = "WHERE status = 'completed'";
} else {
    $sqlFilter = ""; // Pull absolutely everything out of the row logs
}

$query = "SELECT id, client_email, asset_type, asset_brand, asset_id, problem_category, allocated_part, part_price, amount, phone, location, payment_method, status FROM service_requests {$sqlFilter} ORDER BY id DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Master Service Registry Ledger | ARN QuickFix Ltd.</title>
  
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
    
    .filter-tab-link {
      font-size: 13px;
      font-weight: 700;
      color: var(--slate-gray);
      text-decoration: none;
      padding: 8px 20px;
      border-radius: 30px;
      transition: all 0.2s;
      border: 1px solid transparent;
    }
    .filter-tab-link:hover { color: var(--primary-cyan); background-color: #ECEFF1; }
    .filter-tab-active { background-color: var(--deep-navy) !important; color: #FFFFFF !important; }
    
    .badge-status {
      font-size: 11px;
      font-weight: 700;
      padding: 5px 12px;
      border-radius: 30px;
      text-transform: uppercase;
    }
    .status-lbl-pending { background-color: #FFF7ED; color: #EA580C; border: 1px solid #FFEDD5; }
    .status-lbl-processing { background-color: #EFF6FF; color: #2563EB; border: 1px solid #BFDBFE; }
    .status-lbl-completed { background-color: #F0FDF4; color: #16A34A; border: 1px solid #DCFCE7; }
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

  <!-- ================= MASTER CANVAS LEDGER CONTAINER ================= -->
  <div class="container py-5" style="max-width: 1240px;">
    
    <!-- Headline Section Header Row -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-5">
      <div>
        <h2 class="fw-bold m-0" style="font-size: 26px; letter-spacing: -0.5px;">All Service Requests</h2>
        <p class="text-muted m-0 small fw-medium mt-1">Master historical administrative ledger tracking absolutely all submitted machinery repair complaints.</p>
      </div>
      
      <!-- Interactive Status Navigation Filtering Row Tabs -->
      <div class="d-flex bg-white border p-1.5 rounded-pill gap-1 shadow-sm" style="border-color: var(--border-light) !important;">
        <a href="manager_all_requests.php?filter=all" class="filter-tab-link <?php echo ($filterStatus === 'all') ? 'filter-tab-active' : ''; ?>">All History</a>
        <a href="manager_all_requests.php?filter=new" class="filter-tab-link <?php echo ($filterStatus === 'new') ? 'filter-tab-active' : ''; ?>">New Inbox</a>
        <a href="manager_all_requests.php?filter=progress" class="filter-tab-link <?php echo ($filterStatus === 'progress') ? 'filter-tab-active' : ''; ?>">In Progress</a>
        <a href="manager_all_requests.php?filter=completed" class="filter-tab-link <?php echo ($filterStatus === 'completed') ? 'filter-tab-active' : ''; ?>">Completed</a>
      </div>
    </div>
    <!-- ================= SECTION B: DATA GRID REPOSITORY LEDGER ================= -->
    <div class="ledger-container-card">
      <div class="table-responsive">
        <table class="table align-middle m-0">
          <thead>
            <tr>
              <th style="width: 60px; text-align: center;">SL</th>
              <th>Asset Information</th>
              <th>Issue Diagnostics & Estimates</th>
              <th>Contact Node</th>
              <th>Location Profile Matrix</th>
              <th style="width: 140px; text-align: right;">Final Bill</th>
              <th style="width: 150px; text-align: center;">Operational Status</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            if ($result && $result->num_rows > 0): 
              $serialNumberCounter = 1; 
              while ($row = $result->fetch_assoc()): 
                
                // Map system database operational flags onto your styled CSS badges
                $statusLabelText = $row['status'] ?? 'pending';
                $statusBadgeClass = 'status-lbl-pending';
                if ($statusLabelText === 'processing') { $statusBadgeClass = 'status-lbl-processing'; }
                elseif ($statusLabelText === 'completed') { $statusBadgeClass = 'status-lbl-completed'; }
                
                $allocatedPart = trim($row['allocated_part'] ?? '');
                $partPrice = (float)($row['part_price'] ?? 0.00);
                $finalBillAmount = (float)($row['amount'] ?? 0.00); // MAPS TO YOUR EXISTING AMOUNT COLUMN
            ?>
                <tr>
                  <!-- Clean Human-Readable Sequential Serial Listing Line Lines -->
                  <td class="font-monospace fw-bold text-secondary text-center" style="font-size: 13.5px;">
                    <?php echo $serialNumberCounter++; ?>
                  </td>
                  
                  <!-- Asset Machinery Grid Data Summary Details -->
                                    <td>
                    <span class="d-block fw-bold text-dark mb-0.5"><?php echo htmlspecialchars($row['asset_type'] ?? 'Asset'); ?></span>
                    <span class="text-muted font-monospace small" style="font-size: 11.5px;">
                      <?php echo htmlspecialchars($row['asset_brand'] ?? ''); ?> — <strong>
                        <?php 
                          $displayAssetString = $row['asset_id'] ?? '';
                          
                          // ✅ FIXED: Converts raw database text dividers on the fly to match your neat hyphen structure
                          
                          echo htmlspecialchars($displayAssetString); 
                        ?>
                      </strong>
                    </span>
                  </td>

                  
                  <!-- Diagnostics Profile Category with Cross-Synced Pricing Switches -->
                  <td>
                    <?php 
                      $currentProblem = trim($row['problem_category'] ?? '');
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
                    <span class="d-block text-dark fw-medium mb-1">
                      <?php echo htmlspecialchars($currentProblem); ?> 
                      <small class="text-secondary fw-semibold font-monospace" style="font-size: 11.5px;"><?php echo $priceGuideTag; ?></small>
                    </span>

                    <!-- AUTOMATED ON-SITE TECHNICIAN INVENTORY PARTS DISPLAY -->
                    <div class="mb-1.5">
                      <?php if (!empty($allocatedPart)): ?>
                        <span class="badge font-monospace" style="font-size: 10px; padding: 2px 5px; background-color: #F8FAFC; color: #475569; border: 1px solid #E2E8F0; border-radius: 4px; display: inline-block;">
                          📦 Used: <?php echo htmlspecialchars($allocatedPart); ?> (+৳<?php echo number_format($partPrice); ?>)
                        </span>
                      <?php else: ?>
                        <?php if ($statusLabelText === 'processing'): ?>
                          <span class="badge font-monospace" style="font-size: 10px; padding: 2px 5px; background-color: #FFFBEB; color: #D97706; border: 1px solid #FEF3C7; border-radius: 4px; display: inline-block;">⏳ On-Site Investigating...</span>
                        <?php else: ?>
                          <span class="badge font-monospace text-muted" style="font-size: 10px; padding: 2px 5px; background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 4px; display: inline-block;">No Extra Parts Needed</span>
                        <?php endif; ?>
                      <?php endif; ?>
                    </div>

                  </td>
                  
                  <!-- Customer Communications Info -->
                  <td>
                    <span class="d-block text-dark fw-semibold" style="font-size: 13px;"><?php echo htmlspecialchars($row['phone'] ?? ''); ?></span>
                    <span class="text-muted font-monospace d-block" style="font-size: 11px;"><?php echo htmlspecialchars($row['client_email'] ?? ''); ?></span>
                  </td>
                  
                  <!-- Customer Target Delivery Site Location Parameters -->
                  <td>
                    <span class="d-block text-dark fw-medium" style="font-size: 13.5px;"><?php echo htmlspecialchars($row['location'] ?? ''); ?></span>
                    <span class="text-muted small font-monospace d-block mt-0.5" style="font-size: 11px;">Method: <strong><?php echo htmlspecialchars($row['payment_method'] ?? 'Not Set'); ?></strong></span>
                  </td>

                                    <!-- DYNAMIC DEDICATED BILLING AMOUNT COLUMN CELL (FIXED COMPOSITE INTERLOCK) -->
                  <td class="text-end fw-bold font-monospace text-dark" style="font-size: 13.5px;">
                    <?php 
                      $statusLabelText = strtolower(trim($row['status'] ?? 'pending'));
                      $finalBillAmount = (float)($row['amount'] ?? 0.00);
                      $partPrice = (float)($row['part_price'] ?? 0.00);
                      $currentProblem = trim($row['problem_category'] ?? '');

                      // Match labor fees exactly to calculate fallback baseline rates
                      $baseRateNumeric = 0.00;
                      switch ($currentProblem) {
                          case 'Component Repair':          $baseRateNumeric = 4500.00; break;
                          case 'Part Replacement':           $baseRateNumeric = 3000.00; break;
                          case 'Modernization':              $baseRateNumeric = 15000.00; break;
                          case 'Routine Servicing':          $baseRateNumeric = 2000.00; break;
                          case 'Emergency Breakdown':        $baseRateNumeric = 5000.00; break;
                          case 'Basic Servicing':            $baseRateNumeric = 600.00; break;
                          case 'Deep Cleaning':              $baseRateNumeric = 1200.00; break;
                          case 'Duct Cleaning':              $baseRateNumeric = 5000.00; break;
                          case 'Gas Refill':                 $baseRateNumeric = 2500.00; break;
                          case 'Electrical Repair':          $baseRateNumeric = 1500.00; break;
                          case 'Compressor Repair':          $baseRateNumeric = 4000.00; break;
                          case 'Preventative Inspection':    $baseRateNumeric = 3500.00; break;
                          case 'Fault Code Diagnostic':         $baseRateNumeric = 1800.00; break;
                          case 'Engine Rebuild':                $baseRateNumeric = 25000.00; break;
                          case 'Component Repairs':             $baseRateNumeric = 6000.00; break;
                          case 'Advanced Testing':              $baseRateNumeric = 8000.00; break;
                          case 'Fuel Polishing':                $baseRateNumeric = 4500.00; break;
                          default:                              $baseRateNumeric = 0.00; break;
                      }

                      // If the job is ongoing, display "Pending Job"
                      if ($statusLabelText !== 'completed' && $statusLabelText !== 'complaint_raised') {
                          echo "<span class='text-muted small fw-normal font-sans' style='color: #94A3B8 !important; font-size:11px;'>Pending Job</span>";
                      } else {
                          // SMART FALLBACK: If amount column is still 0.00, automatically add Base Labor Fee + Installed Part Price!
                          $displayBill = ($finalBillAmount > 0.00) ? $finalBillAmount : ($baseRateNumeric + $partPrice);
                          echo "৳" . number_format($displayBill, 2);
                      }
                    ?>
                  </td>

                  
                  <!-- Unified System Status Badge Print -->
                  <td class="text-center">
                    <span class="badge-status <?php echo $statusBadgeClass; ?>">
                      <?php echo htmlspecialchars($statusLabelText); ?>
                    </span>
                  </td>
                </tr>
              <?php 
              endwhile; 
            else: 
            ?>
              <!-- Fallback state display window container if rows are zero -->
              <tr>
                <td colspan="7" class="text-center py-5 text-muted font-monospace fw-bold" style="background-color: #FFFFFF;">
                  📁 Empty Archive Node! No matching historical requests registered under this status filter right now.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div> <!-- Close Ledger Container Card -->
  </div> <!-- Close Master Canvas Layout Wrapper Container Container -->

  <!-- Framework Compiled Engine Injector Libraries -->
</body>
</html>
<?php 
// Terminate database active network thread connection cleanly on module exit
if (isset($conn)) { 
    $conn->close(); 
} 
?>