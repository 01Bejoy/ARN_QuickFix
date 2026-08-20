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

// Connect to Local Database
$conn = new mysqli("127.0.0.1", "root", "", "arn_quickfix");
if ($conn->connect_error) {
    die("Database Connection Failure: " . $conn->connect_error);
}

// ====================================================================
// ANALYTICS CALCULATIONS ENGINE (LIVE ANALYSIS MATRIX)
// ====================================================================

// 1. Core Counts Summary Parameters
$totalRequests = 0; $completedRequests = 0; $pendingRequests = 0; $totalRevenue = 0.00;

$qSummary = $conn->query("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as finished,
    SUM(CASE WHEN status IN ('pending', 'submitted') THEN 1 ELSE 0 END) as unassigned,
    COALESCE(SUM(CASE WHEN status = 'completed' THEN COALESCE(amount, 0) ELSE 0 END), 0) as revenue 
    FROM service_requests");

if ($qSummary) {
    $sData = $qSummary->fetch_assoc();
    $totalRequests = $sData['total'] ?? 0;
    $completedRequests = $sData['finished'] ?? 0;
    $pendingRequests = $sData['unassigned'] ?? 0;
    $totalRevenue = $sData['revenue'] ?? 0.00;
}

// Calculate precise completion percentage rates
$completionRate = ($totalRequests > 0) ? round(($completedRequests / $totalRequests) * 100, 1) : 0;

// 2. Machine Categorization Metrics Grouping
$acCount = 0; $elvCount = 0; $genCount = 0;
$qCategory = $conn->query("SELECT asset_type, COUNT(*) as count FROM service_requests GROUP BY asset_type");
if ($qCategory) {
    while ($cRow = $qCategory->fetch_assoc()) {
        $type = strtolower(trim($cRow['asset_type']));
        if (strpos($type, 'ac') !== false || strpos($type, 'air') !== false) { $acCount = $cRow['count']; }
        elseif (strpos($type, 'elv') !== false || strpos($type, 'elevator') !== false) { $elvCount = $cRow['count']; }
        elseif (strpos($type, 'gen') !== false || strpos($type, 'generator') !== false) { $genCount = $cRow['count']; }
    }
}

// 3. Recent Financial Revenue Audit Stream
// The report template expects this result, but older versions never initialized
// $recentFinances. Read finalized service requests directly from the live ledger.
$recentFinances = false;
$qRecentFinances = $conn->query("SELECT client_email, asset_type, problem_category, amount, created_at
    FROM service_requests
    WHERE LOWER(TRIM(status)) = 'completed'
      AND amount IS NOT NULL
      AND amount > 0
    ORDER BY COALESCE(updated_at, created_at) DESC, id DESC
    LIMIT 20");
if ($qRecentFinances) {
    $recentFinances = $qRecentFinances;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reports & Analytical Metrics | ARN QuickFix Ltd.</title>
  
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
    .manager-navbar { background-color: #FFFFFF; border-bottom: 1px solid var(--border-light); padding: 16px 45px; }
    .brand-accent { font-weight: 800; font-size: 24px; color: var(--deep-navy); text-decoration: none; }
    .brand-accent span { color: var(--primary-cyan); }
    
    .metrics-card-layout {
      background: #FFFFFF;
      border: 1px solid var(--border-light);
      border-radius: 16px;
      padding: 24px;
      box-shadow: 0 4px 15px rgba(15, 23, 42, 0.008);
      height: 100%;
    }
    .metric-value-huge { font-size: 34px; font-weight: 800; color: var(--deep-navy); line-height: 1.1; }
    .progress-bar-custom { height: 8px; border-radius: 20px; background-color: #E2E8F0; overflow: hidden; }
    .progress-fill-cyan { background-color: var(--primary-cyan); }
    
    .chart-pill-indicator {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 12px 16px;
      background-color: #F8FAFC;
      border: 1px solid var(--border-light);
      border-radius: 8px;
    }
    .table th {
      background-color: #F8FAFC !important;
      color: var(--slate-gray);
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      padding: 12px 10px;
    }
    .table td { padding: 12px 10px; font-size: 13px; vertical-align: middle; }
  </style>
</head>
<body>

  <!-- ================= TOP NAVIGATION BAR ================= -->
  <nav class="navbar manager-navbar d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3">
      <a href="manager-dashboard.php" class="brand-accent d-flex align-items-center gap-2">
        <img src="img/logo.svg.svg" alt="Logo" style="height: 55px; width: auto;" onerror="this.style.display='none';">
        <span>ARN <span>QuickFix Ltd. Analytics Core</span></span>
      </a>
    </div>
    <div class="d-flex align-items-center gap-3">
      <div class="d-flex align-items-center gap-2 bg-light px-3 py-1.5 rounded-pill border" style="border-color: #E2E8F0 !important;">
        <div style="width: 8px; height: 8px; background-color: #10B981;" class="rounded-circle"></div>
        <span class="small fw-semibold text-secondary" style="font-size: 13px;">
          Auditor: <strong class="text-dark fw-bold"><?php echo htmlspecialchars($managerName); ?></strong>
        </span>
      </div>
      <a href="manager-dashboard.php" class="btn btn-sm btn-outline-secondary rounded-pill px-4 fw-bold" style="font-size: 12.5px; height: 34px; display: flex; align-items: center;">Back to Hub</a>
    </div>
  </nav>

  <!-- ================= MASTER ANALYTICS FRAMEWORK WRAPPER ================= -->
  <div class="container py-5" style="max-width: 1200px;">
    
    <div class="mb-5">
      <h2 class="fw-bold m-0" style="font-size: 26px; letter-spacing: -0.5px;">Operations Reports & Metrics</h2>
      <p class="text-muted m-0 small fw-medium mt-1">Real-time database intelligence parsing gross billing volumes, job completion curves, and machinery servicing diagnostics logs.</p>
    </div>

    <!-- ================= ROW 1: CORE REVENUE & OVERVIEW STATS GRID ================= -->
    <div class="row g-4 mb-4">
      <!-- Card 1: Gross Revenue Collections Volume -->
      <div class="col-lg-4 col-md-6">
        <div class="metrics-card-layout d-flex flex-column justify-content-between">
          <div>
            <div class="d-flex align-items-center justify-content-center rounded-3 mb-3" style="width: 42px; height: 42px; background-color: #ECFEFF; font-size: 20px;">💰</div>
            <div class="small fw-bold text-secondary text-uppercase mb-1" style="font-size: 11px; letter-spacing: 0.5px;">Gross Billing Volume</div>
            <div class="metric-value-huge font-monospace">৳<?php echo number_format($totalRevenue, 2); ?></div>
          </div>
          <p class="text-muted small m-0 mt-3" style="font-size: 12px; line-height: 1.4;">Cumulative aggregate billing charges collected dynamically across finalized maintenance task records [1.1].</p>
        </div>
      </div>

      <!-- Card 2: Operations Completion Success Curves -->
      <div class="col-lg-4 col-md-6">
        <div class="metrics-card-layout d-flex flex-column justify-content-between">
          <div>
            <div class="d-flex align-items-center justify-content-center rounded-3 mb-3" style="width: 42px; height: 42px; background-color: #F0FDF4; font-size: 20px;">📈</div>
            <div class="small fw-bold text-success text-uppercase mb-1" style="font-size: 11px; letter-spacing: 0.5px;">Job Resolution Rate</div>
            <div class="metric-value-huge text-success font-monospace"><?php echo $completionRate; ?>%</div>
          </div>
          <div class="mt-3">
            <div class="progress-bar-custom mb-1"><div class="progress-fill-cyan h-100" style="width: <?php echo $completionRate; ?>%; background-color: #10B981 !important;"></div></div>
            <span class="text-muted font-monospace" style="font-size: 11.5px;"><?php echo $completedRequests; ?> of <?php echo $totalRequests; ?> Complaints Closed Successfully</span>
          </div>
        </div>
      </div>

      <!-- Card 3: Operational Load Split Indicators -->
      <div class="col-lg-4 col-md-12">
        <div class="metrics-card-layout d-flex flex-column justify-content-between">
          <div>
            <div class="d-flex align-items-center justify-content-center rounded-3 mb-3" style="width: 42px; height: 42px; background-color: #FFFBEB; font-size: 20px;">⚙️</div>
            <div class="small fw-bold text-secondary text-uppercase mb-1" style="font-size: 11px; letter-spacing: 0.5px;">Active Queue Density</div>
            <div class="metric-value-huge text-warning font-monospace"><?php echo $pendingRequests; ?> Open</div>
          </div>
          <p class="text-muted small m-0 mt-3" style="font-size: 12px; line-height: 1.4;">Unassigned incoming customer ticket units currently queued inside the primary manager input buffers awaiting diagnostic routing [1.1].</p>
        </div>
      </div>
     <!-- Section-B -->
    <!-- ================= ROW 2: DETAILED CHARTS SEGMENTATION MATRIX ================= -->
    <div class="row g-4 mb-4">
      <!-- Card 4: Equipment Domain Allocation Ratios -->
      <div class="col-md-6">
        <div class="metrics-card-layout">
          <h4 class="fw-bold mb-4 text-dark" style="font-size: 15px; text-transform: uppercase; letter-spacing: 0.5px;">Machinery Allocation Densities</h4>
          <div class="d-flex flex-column gap-3">
            <div class="chart-pill-indicator">
              <span class="fw-semibold" style="font-size: 13.5px;">❄️ Air Conditioning Units</span>
              <span class="badge bg-dark rounded-pill font-monospace px-3 py-1.5"><?php echo $acCount; ?> Tickets</span>
            </div>
            <div class="chart-pill-indicator">
              <span class="fw-semibold" style="font-size: 13.5px;">🏢 Elevator Systems</span>
              <span class="badge bg-dark rounded-pill font-monospace px-3 py-1.5"><?php echo $elvCount; ?> Tickets</span>
            </div>
            <div class="chart-pill-indicator">
              <span class="fw-semibold" style="font-size: 13.5px;">⚡ Power Generators</span>
              <span class="badge bg-dark rounded-pill font-monospace px-3 py-1.5"><?php echo $genCount; ?> Tickets</span>
            </div>
          </div>
        </div>
      </div></div></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ================= ROW 3: DETAILED COLLECTIONS AUDIT LEDGER ================= -->
    <div class="metrics-card-layout">
      <h4 class="fw-bold mb-4 text-dark" style="font-size: 15px; text-transform: uppercase; letter-spacing: 0.5px;">Recent Financial Revenue Audit Stream</h4>
      <div class="table-responsive">
        <table class="table align-middle m-0">
          <thead>
            <tr>
              <th style="width: 55px; text-align: center;">SL</th>
              <th>Customer Target Profile</th>
              <th>Asset Category</th>
              <th>Operational Diagnosis Closed</th>
              <th style="text-align: right; width: 180px;">Settled Volume Amount</th>
              <th style="text-align: center; width: 160px;">Invoice Date</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($recentFinances && $recentFinances->num_rows > 0): 
              $serialCounter = 1;
              while ($rRow = $recentFinances->fetch_assoc()):
            ?>
              <tr style="border-bottom: 1px solid var(--border-light);">
                <!-- Clean Serial Human Numbering List Line Indicator (SL) -->
                <td class="font-monospace fw-bold text-secondary text-center"><?php echo $serialCounter++; ?></td>
                <td class="fw-semibold text-dark"><?php echo htmlspecialchars($rRow['client_email']); ?></td>
                <td class="fw-bold text-secondary" style="font-size: 12.5px;"><?php echo htmlspecialchars($rRow['asset_type']); ?></td>
                <td class="fw-medium text-dark"><?php echo htmlspecialchars($rRow['problem_category']); ?></td>
                <td class="text-end fw-bold font-monospace text-dark" style="font-size: 14px; color: #16A34A !important;">
                  +৳<?php echo number_format($rRow['amount'], 2); ?>
                </td>
                <td class="text-center text-muted font-monospace small" style="font-size: 12px;">
                  <?php echo date('d-m-Y', strtotime($rRow['created_at'])); ?>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="text-center py-5 text-muted font-monospace fw-bold bg-white">
                🍃 Ledger Clear! No finalized revenue transactions captured inside the database stream logs yet.
              </td>
            </tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div> <!-- Close Master Canvas Layout Container Wrapper -->
</body>
</html>
<?php if (isset($conn)) { $conn->close(); } ?>