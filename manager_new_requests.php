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

// 2. High-Performance Database Integration Network Connection
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "arn_quickfix";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database Connection Error: " . $conn->connect_error);
}

// 3. Form Handling: Process Assignment & Status Update Dispatches
$actionMessage = $_SESSION['manager_success'] ?? "";
$actionError = $_SESSION['manager_error'] ?? "";
unset($_SESSION['manager_success'], $_SESSION['manager_error']);

// ====================================================================
// UPDATED DISPATCH PROCESSOR ENGINE (COMPOSITE TEXT INJECTION LAYER)
// ====================================================================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action_type']) && $_POST['action_type'] === 'assign_technician') {
    $ticket_id = (int)$_POST['ticket_id'];
    $selected_tech_name = trim($_POST['technician_name']);
        $selected_tech_id = (int)($_POST['technician_id'] ?? 0); // JS sends the selected technician id; server also verifies it below.

    // Fallback: if the hidden field was not populated by the browser, resolve the id from the selected technician name.
    if ($selected_tech_id <= 0 && $selected_tech_name !== '') {
        $techByName = $conn->prepare("SELECT id FROM users WHERE name = ? AND role LIKE 'tech_%' ORDER BY id ASC LIMIT 1");
        $techByName->bind_param('s', $selected_tech_name);
        $techByName->execute();
        $techByNameRow = $techByName->get_result()->fetch_assoc();
        $techByName->close();
        $selected_tech_id = (int)($techByNameRow['id'] ?? 0);
    }

    if ($ticket_id > 0 && !empty($selected_tech_name) && $selected_tech_id > 0) {
        $techVerify = $conn->prepare("SELECT id, name FROM users WHERE id = ? AND name = ? AND role LIKE 'tech_%' LIMIT 1");
        $techVerify->bind_param('is', $selected_tech_id, $selected_tech_name);
        $techVerify->execute();
        $verifiedTech = $techVerify->get_result()->fetch_assoc();
        $techVerify->close();
        if (!$verifiedTech) {
            $_SESSION['manager_error'] = 'Invalid technician selection.';
            header('Location: manager_new_requests.php');
            exit();
        }
        
        // A. FIRST: Pull your original base address string from your cell records safely
        $addrCheck = $conn->prepare("SELECT location FROM service_requests WHERE id = ? AND status IN ('pending','submitted') LIMIT 1");
        $addrCheck->bind_param("i", $ticket_id);
        $addrCheck->execute();
        $ticketRow = $addrCheck->get_result()->fetch_assoc();
        $addrCheck->close();

        if ($ticketRow) {
            $rawBaseAddress = trim($ticketRow['location']);
            
            // Clean out old assignment tags if the manager is re-dispatching a ticket to a new tech
            if (strpos($rawBaseAddress, ' (Assigned to:') !== false) {
                $parts = explode(' (Assigned to:', $rawBaseAddress);
                $rawBaseAddress = trim($parts[0]);
            }

            // B. SECOND: Formulate the exact structural composite string format used by your system!
            $newCompositeLocationString = $rawBaseAddress . " (Assigned to: " . $selected_tech_name . ")";

            // C. THIRD: Update your row cells. Changes status cleanly to 'processing' and appends data!
            $updateStmt = $conn->prepare("UPDATE service_requests SET status = 'processing', location = ?, technician_id = ? WHERE id = ? AND status IN ('pending','submitted')");
            $updateStmt->bind_param("sii", $newCompositeLocationString, $selected_tech_id, $ticket_id);
            
            if ($updateStmt->execute() && $updateStmt->affected_rows === 1) {
                $_SESSION['manager_success'] = 'Field crew allocated successfully. Ticket dispatched.';
            } else {
                $_SESSION['manager_error'] = 'This ticket is no longer available for dispatch, or the update failed.';
            }
            $updateStmt->close();
            header('Location: manager_new_requests.php');
            exit();
        }
    }
}


// 4. Pull Incoming Queue Dataset Rows (Only pending or submitted statuses)
$query = "SELECT id, client_email, asset_type, asset_brand, asset_id, problem_category, phone, location, payment_method, status FROM service_requests WHERE status IN ('pending', 'submitted') ORDER BY id DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Process New Requests Queue | ARN QuickFix Ltd.</title>
  
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
    
    .btn-dispatch {
      background-color: var(--primary-cyan);
      color: #FFFFFF;
      font-weight: 700;
      font-size: 12px;
      border: none;
      padding: 6px 16px;
      border-radius: 6px;
      transition: all 0.2s;
    }
    .btn-dispatch:hover {
      background-color: #00AEC6;
      transform: translateY(-1px);
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

  <!-- ================= MASTER LEDGER CANVAS WRAPPER ================= -->
  <div class="container py-5" style="max-width: 1200px;">
    
    <!-- Header Summary Block -->
    <div class="d-flex justify-content-between align-items-end mb-4">
      <div>
        <h2 class="fw-bold m-0" style="font-size: 26px; letter-spacing: -0.5px;">Process New Requests</h2>
        <p class="text-muted m-0 small fw-medium mt-1">Review unassigned customer maintenance queries and allocate engineering resources.</p>
      </div>
      <span class="badge bg-dark rounded-pill font-monospace px-3 py-2" style="font-size: 11px;">
        TOTAL PENDING: <?php echo $result ? $result->num_rows : 0; ?> TICKETS
      </span>
    </div>

    <!-- Alert Messaging Notifications Matrix Row -->
    <?php if (!empty($actionMessage)): ?>
      <div class="alert alert-success border-0 shadow-sm rounded-3 p-3 mb-4 fw-bold font-monospace" style="border-left: 5px solid #10B981 !important; font-size:13.5px; color:#065F46;">
        🎉 <?php echo $actionMessage; ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($actionError)): ?>
      <div class="alert alert-danger border-0 shadow-sm rounded-3 p-3 mb-4 fw-bold font-monospace" style="border-left: 5px solid #EF4444 !important; font-size:13.5px; color:#991B1B;">
        ⚠️ Operational Mismatch: <?php echo $actionError; ?>
      </div>
    <?php endif; ?>

    <!-- ================= DATA GRID SHEET LEDGER CONTAINER ================= -->
    <div class="ledger-container-card">
      <div class="table-responsive">
        <table class="table align-middle m-0">
                    <thead>
            <tr>
              <th style="width: 65px; text-align: center;">SL</th>
              <th>Asset Information</th>
              <th>Issue Diagnostics</th>
              <th>Contact Node</th>
              <th>Location Profile</th>
              <th style="width: 250px;">Assign Field Crew</th>
            </tr>
          </thead>
                    <tbody>
            <?php 
            if ($result && $result->num_rows > 0): 
              // Initialize a sequential screen counter tracking row list lines
              $serialNumberCounter = 1; 
              
              while ($row = $result->fetch_assoc()):
            ?>
                <tr>
                  <!-- Fixed: Display your clean sequential sequence, then increment it forward by 1 -->
                  <td class="font-monospace fw-bold text-secondary text-center" style="font-size: 13.5px;">
                    <?php echo $serialNumberCounter++; ?>
                  </td>
                  
                  <!-- Asset Meta Parameters Grid -->
                  <td>
                    <span class="d-block fw-bold text-dark mb-0.5"><?php echo htmlspecialchars($row['asset_type'] ?? ''); ?></span>
                    <span class="text-muted font-monospace small" style="font-size: 11.5px;"><?php echo htmlspecialchars($row['asset_brand'] ?? ''); ?> — <strong><?php echo htmlspecialchars($row['asset_id'] ?? ''); ?></strong></span>
                  </td>
                  
                                    <!-- Diagnostics Profile Category and Urgency Badges Row (WITH INVENTORY INTEGRITY) -->
                  <td>
                    <?php 
                      $currentProblem = $row['problem_category'] ?? '';
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

                    <!-- NEW INVENTORY INTERLOCK BADGE -->
                    <div class="mb-1.5">
                      <span class="badge font-monospace" style="font-size: 10px; padding: 2px 6px; background-color: #ECFEFF; color: #0891B2; border: 1px solid #CFFAFE; border-radius: 4px; display: inline-block;">
                        ⏳ Awaiting Field Inspection
                      </span>
                    </div>
                  </td>

                  
                  <!-- Customer Communications Info -->
                  <td>
                    <span class="d-block text-dark fw-semibold" style="font-size: 13px;"><?php echo htmlspecialchars($row['phone'] ?? ''); ?></span>
                    <span class="text-muted font-monospace d-block" style="font-size: 11px;"><?php echo htmlspecialchars($row['client_email'] ?? ''); ?></span>
                  </td>
                  
                  <!-- Customer Physical Location Nodes -->
                  <td>
                    <span class="d-block text-dark fw-medium" style="font-size: 13.5px;"><?php echo htmlspecialchars($row['location'] ?? ''); ?></span>
                    <span class="text-muted small font-monospace d-block mt-0.5" style="font-size: 11px;">Method: <strong><?php echo htmlspecialchars($row['payment_method'] ?? 'Not Set'); ?></strong></span>
                  </td>
                  
                                    <!-- Interactive Field Dispatch Action Selection Menus -->
                  <td>
                    <form action="manager_new_requests.php" method="POST" class="d-flex gap-2 dispatch-form">
                      <input type="hidden" name="action_type" value="assign_technician">
                      <input type="hidden" name="ticket_id" value="<?php echo $row['id']; ?>">
                      
                      <input type="hidden" name="technician_id" class="technician-id" value="">
                        <select name="technician_name" class="form-select form-select-sm technician-name" style="font-size: 12.5px; height: 34px; background-color: #F8FAFC; border-radius: 6px;" required>
                        <option value="" disabled selected hidden>Select Crew Engineer</option>
                        <?php
                        // ====================================================================
                        // LIVE FIELD CREW PARSER MATRIX
                        // ====================================================================
                        // Fetches all authentic technicians logged in your primary database users table
                        $techResult = $conn->query("SELECT id, name, specialization FROM users WHERE role LIKE 'tech_%' ORDER BY name ASC");
                        
                        if ($techResult && $techResult->num_rows > 0) {
                            while ($tech = $techResult->fetch_assoc()) {
                                $specText = !empty($tech['specialization']) ? $tech['specialization'] : 'Field Engineer';
                                
                                // Emits clean options using their exact database recorded strings
                                echo "<option value='" . htmlspecialchars($tech['name']) . "' data-tech-id='" . (int)$tech['id'] . "'>" . htmlspecialchars($tech['name']) . " (" . htmlspecialchars($specText) . ")</option>";
                            }
                        } else {
                            echo "<option value='' disabled>No Active Technicians Registered</option>";
                        }
                        ?>
                      </select>
                      
                      <button type="submit" class="btn btn-dispatch d-flex align-items-center justify-content-center fw-bold" style="height: 34px; padding: 0 16px; font-size: 12px; background-color: var(--primary-cyan); color: #FFFFFF; border: none; border-radius: 6px; transition: all 0.2s;">
                        Dispatch
                      </button>
                    </form>
                  </td>

                </tr>
              <?php 
              endwhile; // Closes the while loop cleanly
            else: // Matches the structural if block conditional rule
            ?>
              <!-- Fallback state if database has zero pending rows -->
              <tr>
                <td colspan="6" class="text-center py-5 text-muted font-monospace fw-bold" style="background-color: #FFFFFF;">
                  🎉 Pristine Slate! There are zero unassigned incoming customer tickets inside your queue right now.
                </td>
              </tr>
            <?php endif; // Closes the structural if block condition completely ?>
          </tbody>
        </table>
      </div>
    </div> <!-- Close Ledger Container Card -->
  </div> <!-- Close Master Canvas Layout Wrapper Container -->

  <!-- Dispatch interlock: copy the selected technician's database id into the form before submit. -->
  <script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.dispatch-form').forEach(function (form) {
      const techSelect = form.querySelector('.technician-name');
      const techId = form.querySelector('.technician-id');
      if (!techSelect || !techId) return;

      function syncTechId() {
        const option = techSelect.options[techSelect.selectedIndex];
        techId.value = option ? (option.getAttribute('data-tech-id') || '') : '';
      }

      techSelect.addEventListener('change', syncTechId);
      form.addEventListener('submit', function (event) {
        syncTechId();
        if (!techSelect.value || !techId.value) {
          event.preventDefault();
          alert('Please select a Field Crew Engineer before Dispatch.');
        }
      });
    });
  });
  </script>
</body>
</html>
<?php 
// Terminate your database integration connection thread cleanly on page exit
if (isset($conn)) { 
    $conn->close(); 
} 
?>