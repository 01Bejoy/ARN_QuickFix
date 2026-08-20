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

// 2. Establish High-Performance Local Database Integration Network Link
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "arn_quickfix";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database Connectivity Failure Trace: " . $conn->connect_error);
}
// FIXED: Added status to the field list so your badge color conditions compute accurately!
$logQuery = $conn->prepare("SELECT asset_id, asset_type, asset_brand, problem_category, allocated_part, part_price, status, location, payment_method, amount, created_at FROM service_requests WHERE client_email = ? ORDER BY id DESC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>All Client Requests | ARN QuickFix Ltd.</title>
  
  <!-- High performance local file framework loaders -->
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
    .all-requests-navbar {
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
    .action-icon-link {
      background: none;
      border: 1px solid var(--border-gray);
      color: #64748B;
      font-size: 13px;
      font-weight: 500;
      padding: 6px 18px;
      border-radius: 20px;
      text-decoration: none;
      transition: all 0.2s;
    }
    .action-icon-link:hover {
      border-color: var(--primary-cyan);
      color: var(--primary-cyan);
    }
    .ledger-panel {
      background: #FFFFFF;
      border: 1px solid var(--border-gray);
      border-radius: 12px;
      padding: 35px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.01);
    }
    /* Explicit status color tag styling matching your Figma visual columns */
    .status-badge-submitted {
      background-color: var(--primary-cyan);
      color: #FFFFFF;
      font-size: 11px;
      font-weight: 700;
      padding: 5px 12px;
      border-radius: 4px;
      text-transform: capitalize;
    }
  </style>
</head>
<body>

  <!-- ================= MASTER NAVIGATION BAR (MATCHES FIGMA MINIMALIST RAIL) ================= -->
  <nav class="navbar all-requests-navbar d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3">
      <a href="client-dashboard.php" class="brand-accent d-flex align-items-center gap-2">
        <img src="img/logo.svg.svg" alt="Logo" style="height: 38px; width: auto;" onerror="this.style.display='none';">
        <span>ARN QuickFix Ltd.</span>
      </a>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="text-secondary small fw-medium"><i class="fa fa-user-circle me-1"></i> Client: <strong class="text-dark"><?php echo $clientName; ?></strong></span>
      <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold" style="font-size:12px;">Logout</a>
    </div>
  </nav>

  <!-- ================= ALL REQUESTS MATRIX LEDGER CONTAINER ================= -->
  <div class="container py-5">
    
    <!-- Title Section Header containing structural navigation back links -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold m-0 text-dark" style="font-size: 26px; letter-spacing: -0.5px;">All Requests</h2>
      <a href="client-dashboard.php" class="action-icon-link"><i class="fa fa-arrow-left me-1"></i> Back</a>
    </div>

    <!-- Master Data Layout Grid Panel Block -->
    <div class="ledger-panel">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 13.5px;">
          <!-- Custom High Contrast Headers Row directly mapping your Figma specs -->
          <thead>
            <tr class="text-secondary" style="border-bottom: 2px solid var(--border-gray); font-weight: 700;">
              <th class="py-3" style="font-weight: 700 !important; width: 60px;">SL</th>
              <th class="py-3" style="font-weight: 700 !important;">Asset ID</th>
              <th class="py-3" style="font-weight: 700 !important;">Asset Type</th>
              <th class="py-3" style="font-weight: 700 !important;">Asset Brand</th>
              <th class="py-3" style="font-weight: 700 !important;">Category</th>
              <th class="py-3" style="font-weight: 700 !important;">Status</th>
              <th class="py-3" style="font-weight: 700 !important;">Phone</th>
              <th class="py-3" style="font-weight: 700 !important;">Location</th>
              <th class="py-3" style="font-weight: 700 !important;">Payment</th>
              <th class="py-3" style="font-weight: 700 !important;">Amount</th>
              <th class="py-3" style="font-weight: 700 !important;">Current Price</th>
              <th class="py-3" style="font-weight: 700 !important;">Previous Price</th>
              <th class="py-3" style="font-weight: 700 !important;">Review</th>
              <th class="py-3" style="font-weight: 700 !important;">Created</th>
            </tr>
          </thead>
                   <tbody>
  <?php
  // ====================================================================
  // ✅ FIXED DYNAMIC SQL QUERY: FETCHING WAREHOUSE COMPONENT COLUMNS
  // ====================================================================
  // Added 'allocated_part' and 'part_price' right into the SELECT parameters array matrix
  $stmt = $conn->prepare("SELECT id, asset_id, asset_type, asset_brand, problem_category, allocated_part, part_price, original_price, current_price, status, phone, location, payment_method, amount, created_at FROM service_requests WHERE client_email = ? ORDER BY id DESC");
  
  if ($stmt) {
      $stmt->bind_param("s", $clientEmail);
      $stmt->execute();
      $result = $stmt->get_result();
      
      if ($result->num_rows > 0) {
          $sl = 1;
          while ($row = $result->fetch_assoc()) {
              $currentProblem = trim($row['problem_category'] ?? '');
              $allocatedPart  = trim($row['allocated_part'] ?? '');
              $partPrice      = (float)($row['part_price'] ?? 0.00);
              $finalAmount    = (float)($row['amount'] ?? 0.00); 

              // Fetch baseline price guides matching category metrics
              $baseRateNumeric = 0.00;
              $priceGuideTag = "";
              
              switch ($currentProblem) {
                  case 'Component Repair':    $priceGuideTag = "৳4,500"; $baseRateNumeric = 4500.00; break;
                  case 'Part Replacement':     $priceGuideTag = "৳3,000"; $baseRateNumeric = 3000.00; break;
                  case 'Modernization':        $priceGuideTag = "৳15,000"; $baseRateNumeric = 15000.00; break;
                  case 'Routine Servicing':    $priceGuideTag = "৳2,000"; $baseRateNumeric = 2000.00; break;
                  case 'Emergency Breakdown':  $priceGuideTag = "৳5,000"; $baseRateNumeric = 5000.00; break;
                  case 'Basic Servicing':      $priceGuideTag = "৳600";   $baseRateNumeric = 600.00; break;
                  case 'Deep Cleaning':        $priceGuideTag = "৳1,200"; $baseRateNumeric = 1200.00; break;
                  case 'Duct Cleaning':        $priceGuideTag = "৳5,000"; $baseRateNumeric = 5000.00; break;
                  case 'Gas Refill':           $priceGuideTag = "৳2,500"; $baseRateNumeric = 2500.00; break;
                  case 'Electrical Repair':    $priceGuideTag = "৳1,500"; $baseRateNumeric = 1500.00; break;
                  case 'Compressor Repair':    $priceGuideTag = "৳4,000"; $baseRateNumeric = 4000.00; break;
                  case 'Preventative Inspection': $priceGuideTag = "৳3,500"; $baseRateNumeric = 3500.00; break;
                  case 'Fault Code Diagnostic':   $priceGuideTag = "৳1,800"; $baseRateNumeric = 1800.00; break;
                  case 'Engine Rebuild':          $priceGuideTag = "৳25,000"; $baseRateNumeric = 25000.00; break;
                  case 'Component Repairs':       $priceGuideTag = "৳6,000"; $baseRateNumeric = 6000.00; break;
                  case 'Advanced Testing':        $priceGuideTag = "৳8,000"; $baseRateNumeric = 8000.00; break;
                  case 'Fuel Polishing':          $priceGuideTag = "৳4,500"; $baseRateNumeric = 4500.00; break;
                  default: $priceGuideTag = ""; $baseRateNumeric = 0.00; break;
              }

              // Generate the inventory badge container variable code string layout
              $inventoryBadgeHtml = "";
              if (!empty($allocatedPart)) {
                  $inventoryBadgeHtml = "<div class='mt-1 font-sans'><span class='badge' style='font-size: 10px; padding: 3px 6px; background-color: #F1F5F9; color: #475569; border: 1px solid #E2E8F0; border-radius: 4px; display: inline-block; font-weight: 500;'>📦 Part: " . htmlspecialchars($allocatedPart) . " (+৳" . number_format($partPrice) . ")</span></div>";
              } else {
                  if (!empty($priceGuideTag)) {
                      $inventoryBadgeHtml = "<div class='mt-1 font-sans'><span class='badge' style='font-size: 10px; padding: 3px 6px; background-color: #ECFEFF; color: #0891B2; border: 1px solid #CFFAFE; border-radius: 4px; display: inline-block; font-weight: 500;'>Base Rate: " . $priceGuideTag . "</span></div>";
                  }
              }


              $ticketStatus = strtolower(trim($row['status'] ?? 'pending'));
              $statusBadgeBg = '#F1F5F9'; $statusBadgeColor = '#475569';
              if ($ticketStatus === 'completed') { $statusBadgeBg = '#D1FAE5'; $statusBadgeColor = '#065F46'; }
              elseif ($ticketStatus === 'processing') { $statusBadgeBg = '#E0F2FE'; $statusBadgeColor = '#0369A1'; }
              elseif ($ticketStatus === 'pending') { $statusBadgeBg = '#FEF3C7'; $statusBadgeColor = '#92400E'; }
              ?>
              
              <!-- ================= HTML ROW PRINT LAYOUT MATRIX ================= -->
              <tr style="border-bottom: 1px solid #F1F5F9;">
                <td class="align-middle py-3 text-secondary font-monospace"><?php echo $sl++; ?></td>
<!-- ✅ FIXED: Converts raw database version text dividers into clean, hyphenated UI structures -->
<td class="align-middle fw-bold text-dark font-monospace">
  <?php 
    $displayAssetString = $row['asset_id'] ?? '';
    
    
    echo htmlspecialchars($displayAssetString); 
  ?>
</td>
                <td class="align-middle text-capitalize"><?php echo htmlspecialchars($row['asset_type']); ?></td>
                <td class="align-middle text-muted"><?php echo htmlspecialchars($row['asset_brand']); ?></td>
                
                <td class="align-middle text-start">
                  <div class="fw-semibold text-dark"><?php echo htmlspecialchars($currentProblem); ?></div>
                  <div class="text-muted small font-monospace" style="font-size: 11px;">Base: <?php echo !empty($priceGuideTag) ? $priceGuideTag : '৳2,500'; ?></div>
                </td>
                
                <td class="align-middle">
                  <span class="badge text-uppercase" style="font-size: 10px; padding: 4px 8px; background-color: <?php echo $statusBadgeBg; ?>; color: <?php echo $statusBadgeColor; ?>;"><?php echo $ticketStatus; ?></span>
                </td>
                
                <td class="align-middle font-monospace text-muted" style="font-size: 12.5px;"><?php echo htmlspecialchars($row['phone'] ?? 'N/A'); ?></td>
                <td class="align-middle text-secondary small" style="font-weight: 500; max-width: 180px;"><?php echo htmlspecialchars($row['location'] ?? 'Site Location'); ?></td>
                <td class="align-middle text-capitalize text-muted small" style="font-weight: 600;"><?php echo htmlspecialchars($row['payment_method'] ?? 'Nagad'); ?></td>
                
                <!-- ================= ✅ MOVED DYNAMIC AMOUNT COLUMNS DATA OUTS ================= -->
                                <!-- ================= DYNAMIC AMOUNT BREAKDOWN COLUMN (FIXED ZERO BASE FALLBACK) ================= -->
                <td class="align-middle font-monospace text-start" style="font-size: 13.5px; font-weight: 600;">
                  <?php if ($ticketStatus === 'pending' || $ticketStatus === 'processing'): ?>
                    <span class="text-muted font-sans small d-block" style="font-style: italic; font-size: 11px;">Pending Work</span>
                  <?php else: ?>
                    
                    <?php 
                      // ✅ SMART FALLBACK MATRIX: If the database amount is 0 or blank, automatically fall back to the true category base rate calculation!
                      $displayAmountValue = ($finalAmount > 0) ? $finalAmount : $baseRateNumeric;
                    ?>

                    <div class="fw-bold text-dark">৳<?php echo number_format($displayAmountValue, 2); ?></div>
                    <!-- Prints out your formatted parts label box seamlessly right below the total price cell -->
                    <?php echo $inventoryBadgeHtml; ?>
                  <?php endif; ?>
                </td>

                <td class="align-middle font-monospace fw-bold">
                  <?php $currentPriceDisplay = ($row['current_price'] !== null) ? (float)$row['current_price'] : (($finalAmount > 0) ? $finalAmount : $baseRateNumeric); ?>
                  ৳<?php echo number_format($currentPriceDisplay, 2); ?>
                </td>
                <td class="align-middle font-monospace text-secondary">
                  <?php $previousPriceDisplay = ($row['original_price'] !== null) ? (float)$row['original_price'] : 0; ?>
                  <?php echo $previousPriceDisplay > 0 ? '৳'.number_format($previousPriceDisplay,2) : '—'; ?>
                </td>
                <td class="align-middle">
                  <?php if ($ticketStatus === 'completed'): ?>
                    <a href="review-service.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-sm btn-outline-primary rounded-pill">Rate / Review</a>
                  <?php else: ?><span class="text-muted small">Available after completion</span><?php endif; ?>
                </td>
                <td class="align-middle font-monospace text-muted small" style="font-size: 11.5px;"><?php echo !empty($row['created_at']) ? date('d-m-Y | h:i A', strtotime($row['created_at'])) : 'N/A'; ?></td>
              </tr>
              
              <?php
          }
      } else {
          echo "<tr><td colspan='12' class='text-center py-5 text-muted font-monospace'>No asset request ledgers logged under this profile link node yet.</td></tr>";
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
