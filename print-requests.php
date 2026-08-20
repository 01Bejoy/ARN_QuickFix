<?php
session_start();
if (!isset($_SESSION['email']) || !isset($_SESSION['name'])) {
    header("Location: login.php");
    exit();
}
$clientEmail = $_SESSION['email'];
$clientName = $_SESSION['name'];

// Establishes your clean high-speed local database network path link
$conn = new mysqli("127.0.0.1", "root", "", "arn_quickfix");
if ($conn->connect_error) { 
    die("Database Connection Error Trace: " . $conn->connect_error); 
}

// ====================================================================
// UNIFIED PRINT SELECTOR FIXED ENGINE (PASTED SAFELY HERE)
// ====================================================================
// This fetches ALL active and past entries (including your 3 completed jobs)
// matching this specific client profile account, ordered chronologically.
$queryStr = "SELECT id, asset_id, asset_type, asset_brand, problem_category, status, phone, location, payment_method, allocated_part, part_price, amount, created_at 
             FROM service_requests 
             WHERE client_email = '$clientEmail' 
             ORDER BY id DESC";

$result = $conn->query($queryStr);
?>
<!-- Your HTML sheet template print layout tags continue exactly right below here -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Complete Service Requests Ledger Statement Summary</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #FFFFFF; font-family: -apple-system, BlinkMacSystemFont, sans-serif; color: #1E293B; }
    .report-header { border-bottom: 3px solid #0F172A; padding-bottom: 15px; margin-bottom: 30px; }
    @media print {
      body { padding: 0; margin: 0; }
      .no-print { display: none; }
    }
  </style>
</head>
<body onload="window.print();"> <!-- Opens print / save interface immediately on load -->

  <div class="container-fluid py-4 px-4">
    
    <!-- Report Top Header Banner -->
    <div class="report-header d-flex justify-content-between align-items-end">
      <div>
         <img src="img/logo.svg.svg" alt="Logo" class="report-logo" onerror="this.src='img/logo.png'; this.onerror=function(){this.style.display='none';};"><h1 class="fw-bold m-0" style="color: #00C2CB; letter-spacing: -0.5px;">ARN QuickFix Ltd.</h1>
        
        <p class="text-muted m-0 small fw-semibold font-monospace text-uppercase" style="font-size: 11px;">Complete Customer Service Requests Master Ledger Statement</p>
      </div>
      <div class="text-end">
        <h5 class="fw-bold m-0 text-dark">Master Report Profile</h5>
        <span class="small font-monospace text-secondary" style="font-size: 12px;">Email: <?php echo htmlspecialchars($clientEmail); ?></span>
      </div>
    </div>

    <!-- Client Account Context Card Box Container -->
    <div class="mb-4 bg-light p-3 rounded-3 border border-secondary-subtle" style="font-size: 13.5px; background-color: #F8FAFC !important;">
      <div class="row g-2">
        <div class="col-sm-6"><strong>Client Name Profile:</strong> <span class="text-dark fw-bold"><?php echo htmlspecialchars($clientName); ?></span></div>
        <div class="col-sm-6 text-sm-end"><strong>Generated Timestamp Log:</strong> <span class="font-monospace text-secondary fw-bold"><?php echo date('Y-m-d H:i:s'); ?></span></div>
      </div>
    </div>

    <!-- Expanded Complete Parameter Data Ledger Table Grid Matrix -->
    <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle" style="font-size: 12.5px;">
        <thead class="table-dark text-uppercase font-monospace" style="font-size: 11.5px;">
          <tr>
            <th class="py-3" style="width: 50px; font-weight: 700 !important;">SL</th>
            <th class="py-3" style="font-weight: 700 !important;">Asset ID</th>
            <th class="py-3" style="font-weight: 700 !important;">Asset Type</th>
            <th class="py-3" style="font-weight: 700 !important;">Brand Name</th>
            <th class="py-3" style="font-weight: 700 !important;">Problem Category</th>
            <th class="py-3" style="font-weight: 700 !important;">Contact Phone</th>
            <th class="py-3" style="font-weight: 700 !important;">Operational Location</th>
            <th class="py-3" style="font-weight: 700 !important;">Billing Method</th>
            <!-- ADDED AMOUNT REPORT COLUMN -->
            <th class="py-3" style="font-weight: 700 !important;">Amount</th>
            <th class="py-3" style="font-weight: 700 !important;">Status</th>
            <th class="py-3" style="font-weight: 700 !important;">Created Date</th>
          </tr>
        </thead>
        <tbody>
          <?php
// ====================================================================
// ✅ FIXED PRINT DATA ENGINE: FETCHING PARTS FOR LEDGER SUMMARY
// ====================================================================
// Pulls warehouse component strings to prevent column values coming up blank
$stmt = $conn->prepare("SELECT id, asset_id, asset_type, asset_brand, problem_category, allocated_part, part_price, status, phone, location, payment_method, amount, created_at FROM service_requests WHERE client_email = ? ORDER BY id DESC");
          if ($stmt) 
              $stmt->bind_param("s", $clientEmail);
              $stmt->execute();
              $result = $stmt->get_result();
              
              if ($result->num_rows > 0) {
    $sl = 1;
    // SINGLE UNIFIED LOOP: Fixes pricing calculation logic variables and removes broken code blocks
        $sl = 1;
    // SINGLE AIRTIGHT LOOP CONTROL: Cleans duplicate rows and eliminates layout leaks completely
    while ($row = $result->fetch_assoc()) {
        $currentProblem   = trim($row['problem_category'] ?? '');
        $allocatedPart    = trim($row['allocated_part'] ?? '');
        $partPrice        = (float)($row['part_price'] ?? 0.00);
        $finalAmount      = (float)($row['amount'] ?? 0.00);
        $ticketStatus     = strtolower(trim($row['status'] ?? 'pending'));

        // 1. COMPUTE SYSTEM BASE RATE MATRIX DEFINITIONS
        $baseRateNumeric = 2500.00; // Standard fallback default
        switch ($currentProblem) {
            case 'Component Repair': case 'Component Repairs': $baseRateNumeric = 4500.00; break;
            case 'Part Replacement':     $baseRateNumeric = 3000.00; break;
            case 'Modernization':        $baseRateNumeric = 15000.00; break;
            case 'Routine Servicing':    $baseRateNumeric = 2000.00; break;
            case 'Emergency Breakdown':  $baseRateNumeric = 5000.00; break;
            case 'Basic Servicing':      $baseRateNumeric = 600.00; break;
            case 'Deep Cleaning':        $baseRateNumeric = 1200.00; break;
            case 'Duct Cleaning':        $baseRateNumeric = 5000.00; break;
            case 'Gas Refill':           $baseRateNumeric = 2500.00; break;
            case 'Electrical Repair':    $baseRateNumeric = 1500.00; break;
            case 'Compressor Repair':    $baseRateNumeric = 4000.00; break;
            case 'Preventative Inspection': $baseRateNumeric = 3500.00; break;
            case 'Fault Code Diagnostic':   $baseRateNumeric = 1800.00; break;
            case 'Engine Rebuild':          $baseRateNumeric = 25000.00; break;
            case 'Advanced Testing':        $baseRateNumeric = 8000.00; break;
            case 'Fuel Polishing':          $baseRateNumeric = 4500.00; break;
        }

        // 2. COMPILE AIRTIGHT ACCOUNTING BALANCES WITH ZERO BASE FALLBACKS
        $displayAmountValue = ($finalAmount > 0) ? $finalAmount : $baseRateNumeric;

        // 3. GENERATE INTEGRATED IMAGE / BADGE COMPOSITE MARKUP TAG STRINGS
        $inventoryBadgeHtml = "";
        if (!empty($allocatedPart)) {
            $inventoryBadgeHtml = "<div style='margin-top: 3px;'><span style='font-size: 10px; padding: 2px 6px; background-color: #F1F5F9; color: #475569; border: 1px solid #E2E8F0; border-radius: 4px; display: inline-block; font-family: system-ui, sans-serif;'>📦 Part: " . htmlspecialchars($allocatedPart) . " (+৳" . number_format($partPrice) . ")</span></div>";
        }

        // 4. SYNCHRONIZE COLOR AND TEXT SPECIFICATION DICTIONARIES PER THE FIGMA TRACKERS
        $statusTextHexColor = '#475569'; // Default Gray
        $printStatusLabel   = 'Pending';

        if ($ticketStatus === 'completed') {
            $statusTextHexColor = '#10B981'; // Emerald Green
            $printStatusLabel   = 'Completed';
        } elseif ($ticketStatus === 'processing') {
            $statusTextHexColor = '#0EA5E9'; // Sky Blue
            $printStatusLabel   = 'Processing';
        } elseif ($ticketStatus === 'pending') {
            $statusTextHexColor = '#F59E0B'; // Amber Orange
            $printStatusLabel   = 'Pending';
        }
        ?>
        
        <!-- ================= PRINT LEDGER ROW MATRIX GENERATOR ================= -->
        <tr style="border-bottom: 1px solid #E2E8F0;">
          <td style="padding: 12px 8px; font-family: monospace; color: #64748B; font-weight: bold;"><?php echo $sl++; ?></td>
          <td style="padding: 12px 8px; font-weight: bold; color: #0F172A; font-family: monospace;">#<?php echo htmlspecialchars($row['asset_id']); ?></td>
          <td style="padding: 12px 8px; text-transform: capitalize; color: #1E293B; font-weight: 500;"><?php echo htmlspecialchars($row['asset_type']); ?></td>
          <td style="padding: 12px 8px; color: #475569; font-weight: 500;"><?php echo htmlspecialchars($row['asset_brand']); ?></td>
          <!-- PROBLEM CATEGORY DATA COLUMN CELL (UPDATED WITH BASE RATE SUBTEXT) -->
          <td style="padding: 12px 8px; text-align: left;">
            <div style="color: #0F172A; font-weight: 600; font-size: 13.5px;"><?php echo htmlspecialchars($currentProblem); ?></div>
            
            <!-- ✅ FIXED: Formats a clean, high-contrast base rate label right underneath your problem text string -->
            <div style="font-size: 11px; color: #64748B; font-family: monospace; margin-top: 2px; font-weight: 500;">
              Base: ৳<?php echo number_format($baseRateNumeric); ?>
            </div>
          </td>
          <td style="padding: 12px 8px; font-family: monospace; color: #334155; font-size:12.5px;"><?php echo htmlspecialchars($row['phone']); ?></td>
          <td style="padding: 12px 8px; color: #475569; font-size: 12px; max-width: 220px; word-wrap: break-word; font-weight: 500;"><?php echo htmlspecialchars($row['location']); ?></td>
          <td style="padding: 12px 8px; color: #64748B; font-weight: 600; font-size:12.5px;"><?php echo htmlspecialchars($row['payment_method']); ?></td>
          
          <!-- AMOUNT BREAKDOWN DATA COLUMN CELL -->
          <td style="padding: 12px 8px; font-family: monospace; text-align: left; font-weight: bold; font-size: 13.5px;">
            <?php if ($ticketStatus === 'pending' || $ticketStatus === 'processing'): ?>
              <span style="color: #64748B; font-family: system-ui, sans-serif; font-style: italic; font-size: 11.5px; font-weight: 500;">Pending Work</span>
            <?php else: ?>
              <span style="color: #0F172A;">৳<?php echo number_format($displayAmountValue, 2); ?></span>
              <?php echo $inventoryBadgeHtml; ?>
            <?php endif; ?>
          </td>
          
          <!-- STATUS ASSIGNMENT CELL CONTAINER -->
          <td style="padding: 12px 8px; text-transform: uppercase; font-weight: 800; font-size: 11.5px; color: <?php echo $statusTextHexColor; ?> !important;">
            <?php echo htmlspecialchars($printStatusLabel); ?>
          </td>
          
          <td style="padding: 12px 8px; font-family: monospace; color: #64748B; font-size: 11px;">
            <?php echo !empty($row['created_at']) ? date('d-m-Y | h:i A', strtotime($row['created_at'])) : 'N/A'; ?>
          </td>
        </tr>
        
        <?php
    }
} else {
    echo "<tr><td colspan='12' style='text-align: center; color: #64748B; padding: 40px 10px; font-family: monospace; font-weight: bold;'>No active service tracking requests history logged under this profile.</td></tr>";
}
$stmt->close();
?>
          </tbody>
        </table>
      </div>
    </div>
  </div> <!-- Close Master Canvas Layout Container Wrapper -->

</body>
</html>
<?php 
if (isset($conn)) { 
    $conn->close(); 
} 
?>
