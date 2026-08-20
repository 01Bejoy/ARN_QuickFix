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
    die("Database Connection Error: " . $conn->connect_error);
}

// --------------------------------------------------------------------
// FORM PROCESSING: ADD NEW WAREHOUSE PART WITH PRG REDIRECT
// --------------------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action_type'])) {
    if ($_POST['action_type'] === 'add_part') {
        $part_name = trim($_POST['part_name'] ?? '');
        $part_price = (float)($_POST['part_price'] ?? 0.00);
        $stock_qty = (int)($_POST['stock_qty'] ?? 0);
        $asset_category = $_POST['asset_category'] ?? '';

        // Form Cache Memory Setup to keep entries if validation fails
        $_SESSION['inv_cache'] = [
            'part_name' => $part_name,
            'part_price' => $part_price,
            'stock_qty' => $stock_qty,
            'asset_category' => $asset_category
        ];

        if (!empty($part_name) && $part_price > 0 && !empty($asset_category)) {
            $stmt = $conn->prepare("INSERT INTO inventory (part_name, part_price, stock_qty, asset_category) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sdis", $part_name, $part_price, $stock_qty, $asset_category);
            
            if ($stmt->execute()) {
                $_SESSION['inv_message'] = "Success! New hardware part logged into master warehouse files.";
                unset($_SESSION['inv_cache']); // Erase cache on success
            } else {
                $_SESSION['inv_error'] = "Database Error: Failed writing part specifications.";
            }
            $stmt->close();
        } else {
            $_SESSION['inv_error'] = "Validation Mismatch: Please input a valid Part Name, Price, and Category.";
        }
        header("Location: manager_inventory.php?view=" . urlencode($asset_category));
        exit();
    }
}

// Extract transient alerts and form variables from session
$actionMessage = $_SESSION['inv_message'] ?? ''; unset($_SESSION['inv_message']);
$actionError = $_SESSION['inv_error'] ?? ''; unset($_SESSION['inv_error']);

$cache = $_SESSION['inv_cache'] ?? ['part_name'=>'', 'part_price'=>'', 'stock_qty'=>'', 'asset_category'=>''];

// ====================================================================
// PURE PHP-DRIVEN TAB CONTROLLER MATRIX
// ====================================================================
$activeView = $_GET['view'] ?? 'AC'; // Defaults cleanly to Air Conditioners first

if ($activeView === 'elevator') {
    $categoryFilter = "WHERE asset_category = 'Elevator'";
} elseif ($activeView === 'generator') {
    $categoryFilter = "WHERE asset_category = 'Generator'";
} else {
    $categoryFilter = "WHERE asset_category = 'AC'";
}

// Fetch total counters
$totalItems = 0; $lowStockAlerts = 0;
$qCount = $conn->query("SELECT COUNT(*) as total, SUM(CASE WHEN stock_qty <= 5 THEN 1 ELSE 0 END) as low FROM inventory");
if ($qCount) {
    $cData = $qCount->fetch_assoc();
    $totalItems = $cData['total'] ?? 0;
    $lowStockAlerts = $cData['low'] ?? 0;
}

// Fetch the specific list based on the URL query filter rule
$stockLedger = $conn->query("SELECT * FROM inventory {$categoryFilter} ORDER BY part_name ASC");
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Warehouse Inventory Matrix Center | ARN QuickFix Ltd.</title>
  
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
    
    .figma-card-layout {
      background: #FFFFFF;
      border: 1px solid var(--border-light);
      border-radius: 16px;
      padding: 30px;
      box-shadow: 0 4px 15px rgba(15, 23, 42, 0.01);
    }
    .metric-container-sub {
      border: 1px solid var(--border-light);
      border-radius: 12px;
      background: #FFFFFF;
      padding: 18px 22px;
    }
    .form-label-custom {
      font-size: 11.5px;
      font-weight: 700;
      color: var(--slate-gray);
      margin-bottom: 6px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .form-control-custom, .form-select-custom {
      height: 44px;
      background-color: #F8FAFC;
      border: 1px solid #CBD5E1;
      border-radius: 8px;
      font-size: 13.5px;
    }
    .form-control-custom:focus, .form-select-custom:focus {
      border-color: var(--primary-cyan);
      box-shadow: 0 0 0 3px rgba(0, 194, 203, 0.12);
      background-color: #FFFFFF;
    }
    .btn-save-part {
      background-color: var(--primary-cyan);
      color: #FFFFFF;
      font-weight: 700;
      font-size: 12.5px;
      height: 44px;
      border: none;
      border-radius: 8px;
      transition: all 0.2s;
    }
    .btn-save-part:hover { background-color: #00AEC6; transform: translateY(-1px); }
    
    .table th {
      background-color: #F8FAFC !important;
      color: var(--slate-gray);
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      padding: 14px 10px;
      border-bottom: 2px solid var(--border-light);
    }
    .table td { padding: 14px 10px; font-size: 13px; vertical-align: middle; }
        /* ================= BOOTSTRAP 5 PILLED NAVIGATION INTERLOCKS ================= */
    .nav-pills .nav-link {
      color: var(--slate-gray);
      background-color: #F1F5F9;
      border: 1px solid var(--border-light);
      transition: all 0.2s ease;
      cursor: pointer;
    }
    .nav-pills .nav-link:hover {
      color: var(--primary-cyan);
      background-color: #E2E8F0;
    }
    .nav-pills .nav-link.active, .nav-pills .show > .nav-link {
      color: #FFFFFF !important;
      background-color: var(--deep-navy) !important;
      border-color: var(--deep-navy) !important;
      box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
    }
    .tab-content > .tab-pane {
      display: none;
    }
    .tab-content > .active {
      display: block;
    }
    .fade {
      transition: opacity 0.15s linear;
    }
    @media (prefers-reduced-motion: reduce) {
      .fade { transition: none; }
    }
    .fade:not(.show) {
      opacity: 0;
    }

  </style>
</head>
<body>

  <!-- ================= TOP NAVIGATION BAR ================= -->
  <nav class="navbar manager-navbar d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3">
      <a href="manager-dashboard.php" class="brand-accent d-flex align-items-center gap-2">
        <img src="img/logo.svg.svg" alt="Logo" style="height: 35px; width: auto;" onerror="this.style.display='none';">
        <span>ARN <span>QuickFix Ltd. Logistics</span></span>
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

  <!-- ================= MASTER CANVAS WORKING SHEET CONTAINER ================= -->
  <div class="container py-5" style="max-width: 1160px;">
    
    <!-- Title Headers Row Summary Line -->
    <div class="mb-4">
      <h2 class="fw-bold m-0" style="font-size: 26px; letter-spacing: -0.5px;">Warehouse Stock Inventory</h2>
      <p class="text-muted m-0 small fw-medium mt-1">Monitor real-time infrastructure parts levels, adjust unit costs, and expand mechanical catalog files.</p>
    </div>

    <!-- Interface Operations Alerts Layer Banner -->
    <?php if (!empty($actionMessage)): ?>
      <div class="alert alert-success border-0 shadow-sm rounded-3 p-3 mb-4 fw-bold font-monospace" style="border-left: 5px solid #10B981 !important; color:#065F46; font-size: 13px;">
        🎉 <?php echo $actionMessage; ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($actionError)): ?>
      <div class="alert alert-danger border-0 shadow-sm rounded-3 p-3 mb-4 fw-bold font-monospace" style="border-left: 5px solid #EF4444 !important; color:#991B1B; font-size: 13px;">
        ⚠️ System Block: <?php echo $actionError; ?>
      </div>
    <?php endif; ?>

    <!-- ================= METRIC HIGHLIGHT WIDGET CARDS ROW ================= -->
    <div class="row g-4 mb-5">
      <div class="col-md-6">
        <div class="metric-container-sub">
          <div class="small fw-bold text-secondary text-uppercase mb-1" style="font-size: 10.5px; letter-spacing: 0.5px;">Total Logged Components</div>
          <div class="fw-extrabold font-monospace m-0 text-dark" style="font-size: 32px; font-weight: 800;"><?php echo $totalItems; ?> Parts</div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="metric-container-sub" style="border-left: 3px solid #EF4444 !important;">
          <div class="small fw-bold text-danger text-uppercase mb-1" style="font-size: 10.5px; letter-spacing: 0.5px;">Critical Low Stock Indicators (<=5)</div>
          <div class="fw-extrabold font-monospace m-0 text-danger" style="font-size: 32px; font-weight: 800;"><?php echo $lowStockAlerts; ?> Alerts</div>
        </div>
      </div>
    </div>

    <!-- ================= ADD HARDWARE PART DATA FORM CONTAINER ================= -->
    <div class="figma-card-layout mb-5">
      <h4 class="fw-bold mb-4 text-dark" style="font-size: 15px; text-transform: uppercase; letter-spacing: 0.5px;">Log New Component Item</h4>
      <form action="manager_inventory.php" method="POST">
        <input type="hidden" name="action_type" value="add_part">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label form-label-custom">Part / Hardware Specification Name</label>
            <input type="text" name="part_name" class="form-control form-control-custom" placeholder="e.g., Compressor Coil, Traction Valve Loom" value="<?php echo htmlspecialchars($cache['part_name']); ?>" required>
          </div>
          
          <!-- Input 2: Unit Sale Price Base Rate -->
          <div class="col-md-3">
            <label class="form-label form-label-custom">Unit Sale Price (৳ BDT)</label>
            <input type="number" name="part_price" step="0.01" class="form-control form-control-custom font-monospace" placeholder="Price in BDT Taka" value="<?php echo htmlspecialchars($cache['part_price']); ?>" required>
          </div>
          
          <!-- Input 3: Stock Quantity Counter Box -->
          <div class="col-md-2">
            <label class="form-label form-label-custom">Initial Qty</label>
            <input type="number" name="stock_qty" class="form-control form-control-custom font-monospace" placeholder="Units Count" value="<?php echo htmlspecialchars($cache['stock_qty']); ?>" required>
          </div>
          
          <!-- Input 4: Asset Machinery Classification Compatibility Dropdown -->
          <div class="col-md-3">
            <label class="form-label form-label-custom">Machinery Class Category</label>
            <select name="asset_category" class="form-select form-select-custom w-100" required>
              <option value="" disabled selected hidden>Select Asset Compatibility</option>
              <option value="AC" <?php echo ($cache['asset_category'] === 'AC') ? 'selected' : ''; ?>>Air Conditioner (HVAC Group)</option>
              <option value="Elevator" <?php echo ($cache['asset_category'] === 'Elevator') ? 'selected' : ''; ?>>Elevator System Core</option>
              <option value="Generator" <?php echo ($cache['asset_category'] === 'Generator') ? 'selected' : ''; ?>>Power Generator Sector</option>
            </select>
          </div>
          
          <!-- Form Submission Button Alignment Row -->
          <div class="col-12 d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-save-part px-5 fw-bold text-uppercase">Publish Part Record</button>
          </div>
        </div>
      </form>
    </div>

            <!-- ================= SECTION B: PURE PHP INTERACTIVE HARDWARE REPOSITORIES ================= -->
    <div class="figma-card-layout">
      
      <!-- Nav Tabs Action Header Rail (Driven by clean URL reload routes) -->
      <ul class="nav nav-pills mb-4 gap-2 border-bottom pb-3" style="border-color: #E2E8F0 !important; list-style: none; padding: 0;">
        <li class="nav-item">
          <a href="manager_inventory.php?view=AC" class="btn btn-sm fw-bold px-4 py-2.5 rounded-pill text-decoration-none" 
             style="font-size: 13px; color: <?php echo ($activeView === 'AC') ? '#FFFFFF' : '#475569'; ?>; background-color: <?php echo ($activeView === 'AC') ? 'var(--deep-navy)' : '#F1F5F9'; ?>; border: 1px solid <?php echo ($activeView === 'AC') ? 'var(--deep-navy)' : '#E2E8F0'; ?>; display: inline-block;">
             ❄️ Air Conditioner Stock
          </a>
        </li>
        <li class="nav-item">
          <a href="manager_inventory.php?view=elevator" class="btn btn-sm fw-bold px-4 py-2.5 rounded-pill text-decoration-none" 
             style="font-size: 13px; color: <?php echo ($activeView === 'elevator') ? '#FFFFFF' : '#475569'; ?>; background-color: <?php echo ($activeView === 'elevator') ? 'var(--deep-navy)' : '#F1F5F9'; ?>; border: 1px solid <?php echo ($activeView === 'elevator') ? 'var(--deep-navy)' : '#E2E8F0'; ?>; display: inline-block;">
             🏢 Elevator Spares
          </a>
        </li>
        <li class="nav-item">
          <a href="manager_inventory.php?view=generator" class="btn btn-sm fw-bold px-4 py-2.5 rounded-pill text-decoration-none" 
             style="font-size: 13px; color: <?php echo ($activeView === 'generator') ? '#FFFFFF' : '#475569'; ?>; background-color: <?php echo ($activeView === 'generator') ? 'var(--deep-navy)' : '#F1F5F9'; ?>; border: 1px solid <?php echo ($activeView === 'generator') ? 'var(--deep-navy)' : '#E2E8F0'; ?>; display: inline-block;">
             ⚡ Power Generator Parts
          </a>
        </li>
      </ul>

      <!-- Master Single Adaptive Data Table Container Sheet Grid -->
      <div class="table-responsive">
        <table class="table align-middle m-0" style="font-size: 13.5px;">
          <thead>
            <tr style="background-color: #F8FAFC;">
              <th style="width: 55px; text-align: center; padding: 14px 10px;">SL</th>
              <th style="padding: 14px 10px;">
                <?php 
                  if ($activeView === 'elevator') echo "Elevator";
                  elseif ($activeView === 'generator') echo "Generator";
                  else echo "Air Conditioner";
                ?> Component Specification Model Name
              </th>
              <th style="padding: 14px 10px; width: 140px; text-align: center;">Sector Affinity</th>
              <th style="text-align: right; width: 180px; padding: 14px 10px;">Official Unit Price</th>
              <th style="text-align: center; width: 220px; padding: 14px 10px;">Warehouse Inventory Stock</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            if ($stockLedger && $stockLedger->num_rows > 0): 
              $serialNumberCounter = 1;
              while ($row = $stockLedger->fetch_assoc()):
                $stockCount = (int)$row['stock_qty'];
                
                // Color codes low or missing items automatically to prevent line lockouts
                $stockBadgeStyle = "background-color: #F0FDF4; color: #16A34A; border: 1px solid #DCFCE7;";
                $stockText = $stockCount . " Units Available";
                
                if ($stockCount === 0) {
                    $stockBadgeStyle = "background-color: #FEF2F2; color: #EF4444; border: 1px solid #FEE2E2; font-weight: 800;";
                    $stockText = "🚨 OUT OF STOCK";
                } elseif ($stockCount <= 5) {
                    $stockBadgeStyle = "background-color: #FEF2F2; color: #EF4444; border: 1px solid #FEE2E2; font-weight: bold;";
                } elseif ($stockCount <= 10) {
                    $stockBadgeStyle = "background-color: #FFFBEB; color: #D97706; border: 1px solid #FEF3C7;";
                }
            ?>
              <tr style="border-bottom: 1px solid var(--border-light);">
                <!-- Clean Serial Incremental Counting Line Index Row (SL) counts perfectly from 1 upwards on every page view -->
                <td class="font-monospace fw-bold text-secondary text-center"><?php echo $serialNumberCounter++; ?></td>
                
                <td class="fw-bold text-dark"><?php echo htmlspecialchars($row['part_name']); ?></td>
                
                <td class="text-center">
                  <span class="badge text-secondary bg-light border px-2.5 py-1.5 fw-semibold" style="font-size: 11px;">
                    <?php echo htmlspecialchars($row['asset_category']); ?>
                  </span>
                </td>
                
                <td class="text-end fw-bold font-monospace text-dark">
                  ৳<?php echo number_format($row['part_price'], 2); ?>
                </td>
                
                <td class="text-center">
                  <span class="badge px-3 py-1.5 font-monospace" style="font-size: 11.5px; border-radius: 6px; <?php echo $stockBadgeStyle; ?>">
                    <?php echo $stockText; ?>
                  </span>
                </td>
              </tr>
            <?php 
              endwhile; 
            else: 
            ?>
              <tr>
                <td colspan="5" class="text-center py-5 text-muted font-monospace fw-bold bg-white">
                  📦 Empty Stock Room! No items logged under this category yet.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div> <!-- Close Card Layout Wrapper -->

  </div> <!-- Close Master Canvas Layout Container Wrapper Container -->
</body>
</html>
<?php 
if (isset($conn)) { 
    $conn->close(); 
} 
?>
