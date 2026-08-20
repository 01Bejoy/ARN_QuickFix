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

// ====================================================================
// ✅ DYNAMIC ACTION FORM HANDLER: DUAL ENGINE ROUTING LAYER
// ====================================================================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action_type'])) {
    
    // ACTION INTERLOCK A: MARK ALL PENDING UNREAD MESSAGES AS ARCHIVED
    if ($_POST['action_type'] === 'mark_all_tech_read') {
        $conn->query("UPDATE technician_notes SET is_read = 1 WHERE is_read = 0");
        header("Location: manager-tech-updates.php");
        exit();
    }
    
    // ACTION INTERLOCK B: EMERGENCY OVERRIDE RE-OPEN TICKET LOOP
    if ($_POST['action_type'] === 'reopen_blocked_ticket') {
        $target_ticket_id = (int)$_POST['ticket_id'];
        $note_row_id = (int)$_POST['note_id'];

        if ($target_ticket_id > 0) {
            // 1. Kick the ticket back to 'pending' state and completely strip old assignment tags from the location column
            $conn->query("UPDATE service_requests SET status = 'pending', location = REGEXP_REPLACE(location, ' \\(Assigned to:[^)]+\\)', '') WHERE id = $target_ticket_id");
            
            // 2. Mark this specific update note as read automatically to clear it from the active timeline view feed
            $conn->query("UPDATE technician_notes SET is_read = 1 WHERE id = $note_row_id");

            echo "<script>alert('Task safely re-opened! Ticket returned to New Requests queue for re-assignment.'); window.location.href='manager-tech-updates.php';</script>";
            exit();
        }
    }
}

// ====================================================================
// ✅ DYNAMIC STREAM FILTER: ONLY FETCH UNREAD TEXT NOTES (is_read = 0)
// ====================================================================
// Exactly like your client dashboard notification engine, this filters out read data rows!
$techFeed = $conn->query("SELECT id, ticket_id, tech_name, tech_email, update_message, submitted_at AS updated_at FROM technician_notes WHERE is_read = 0 ORDER BY id DESC");
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Technician Updates | ARN QuickFix Ltd.</title>
  
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
    
    .feed-container-card {
      background: #FFFFFF;
      border: 1px solid var(--border-light);
      border-radius: 20px;
      padding: 35px;
      box-shadow: 0 10px 30px rgba(15, 23, 42, 0.015);
    }
    .log-row-card {
      border: 1px solid var(--border-light);
      border-radius: 12px;
      transition: all 0.2s ease;
    }
    .log-row-card:hover {
      transform: translateX(4px);
      box-shadow: 0 4px 12px rgba(15, 23, 42, 0.015);
    }
    .pulse-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      display: inline-block;
    }
    .pulse-active { background-color: #2563EB; animation: pulse-blink 1.5s infinite; }
    .pulse-complete { background-color: #10B981; }
    @keyframes pulse-blink {
      50% { opacity: 0.4; }
    }
  </style>
</head>
<body>

  <!-- ================= TOP NAVIGATION BAR ================= -->
  <nav class="navbar manager-navbar d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3">
      <a href="manager-dashboard.php" class="brand-accent d-flex align-items-center gap-2">
        <img src="img/logo.svg.svg" alt="Logo" style="height: 55px; width: auto;" onerror="this.style.display='none';">
        <span>  ARN QuickFix Ltd. </span>
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

  <!-- ================= MASTER TELEMETRY FEED CANVAS ================= -->
  <div class="container py-5" style="max-width: 900px;">
    
    <!-- Header Content Row (FIXED: Cleaned duplicate nested blocks) -->
    <div class="d-flex justify-content-between align-items-center mb-4 text-start">
      <div>
        <h2 class="fw-bold m-0" style="font-size: 26px; letter-spacing: -0.5px;">Live Technician Field Stream</h2>
        <p class="text-muted m-0 small fw-medium mt-1">Real-time tactical audit of chat updates, delay notices, and logistical alerts directly from field crews.</p>
      </div>
      
      <!-- ================= CLEAN DEDICATED MARK ALL READ ACTION BUTTON ================= -->
      <form action="manager-tech-updates.php" method="POST" class="m-0">
        <input type="hidden" name="action_type" value="mark_all_tech_read">
        <button type="submit" class="btn btn-sm px-3 fw-bold rounded-pill text-uppercase d-flex align-items-center gap-1.5" 
                style="font-size: 11px; height: 34px; background-color: #ECFEFF; color: #0891B2; border: 1px solid #CFFAFE; transition: all 0.2s;">
          <i class="fa-solid fa-check-double"></i> Mark All Read
        </button>
      </form>
    </div>

    <!-- ================= STREAM CARDS TIMELINE REPOSITORIES ================= -->
    <div class="feed-container-card">
      <div class="d-flex flex-column gap-3.5">
        <?php
        if ($techFeed && $techFeed->num_rows > 0):
            while ($tLog = $techFeed->fetch_assoc()):
                // Clean mapping to your exact technician_notes columns!
                $ticketId     = $tLog['ticket_id'];
                $techEngineer = $tLog['tech_name'] ?? 'Field Crew Engineer';
                $techEmailID  = $tLog['tech_email'] ?? '';
                $updateMsgText= $tLog['update_message'] ?? '';
                $timePosted   = !empty($tLog['updated_at']) ? date('d M, h:i A', strtotime($tLog['updated_at'])) : date('d M, h:i A');
        ?>

                          <!-- Individual Feed Row Card (FIXED TAG LAYOUT STRUCTURE FOR TECH NOTES) -->
        <div class="p-4 log-row-card mb-3 text-start" style="background-color: #EFF6FF; border-color: #BFDBFE; border-radius: 12px; border-style: solid; border-width: 1px;">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="d-flex align-items-center gap-2">
              <!-- Active communication stream signal indicator -->
              <span class="pulse-dot pulse-active"></span>
              <h5 class="m-0 fw-bold text-dark" style="font-size: 14.5px;"><?php echo htmlspecialchars($techEngineer); ?></h5>
              <span class="badge text-uppercase text-secondary font-monospace border bg-white" style="font-size: 10px;">Ticket #<?php echo (int)$ticketId; ?></span>
            </div>
            <span class="text-muted small font-monospace fw-semibold" style="font-size: 12px;">
              <i class="fa-regular fa-clock me-1 text-muted"></i><?php echo $timePosted; ?>
            </span>
          </div>
          
          <!-- Dynamic Real-Time Text Description Block (Renders the Technician's Live Message) -->
          <div class="mt-2.5 p-3 rounded-3 font-sans border-start border-3 border-primary" style="background-color: #F8FAFC; border-color: #3B82F6 !important; line-height: 1.5; font-size: 13.5px; font-weight: 500; color: #334155;">
            <strong class="text-dark small d-block mb-1 text-uppercase font-monospace text-secondary" style="font-size: 10px; letter-spacing: 0.3px;"><i class="fa-solid fa-comment-dots"></i> Message Log:</strong>
            <?php echo htmlspecialchars($updateMsgText); ?>
          </div>

          <span class="text-muted small font-monospace d-block mt-2" style="font-size: 11px; opacity: 0.7;">
            <i class="fa-solid fa-envelope me-1"></i> Tech Node: <?php echo htmlspecialchars($techEmailID); ?>
          </span>

          <!-- ================= EMERGENCY MANAGER OVERRIDE RE-DISPATCH CONTROL TOOL ================= -->
          <div class="mt-3 pt-2 d-flex justify-content-end border-top" style="border-style: dashed !important; border-color: #CBD5E1 !important;">
            <form action="manager-tech-updates.php" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to cancel current field operations and re-open this ticket for assignment?');">
              <input type="hidden" name="action_type" value="reopen_blocked_ticket">
              <input type="hidden" name="ticket_id" value="<?php echo (int)$ticketId; ?>">
              <input type="hidden" name="note_id" value="<?php echo (int)$tLog['id']; ?>">
              
              <button type="submit" class="btn btn-sm fw-bold rounded-pill text-uppercase d-flex align-items-center gap-1" 
                      style="font-size: 10.5px; height: 32px; background-color: #FEF2F2; color: #EF4444; border: 1px solid #FEE2E2; transition: all 0.2s;">
                <i class="fa-solid fa-rotate-left"></i> Re-Open & Re-Assign Task
              </button>
            </form>
          </div>
        </div>



                      <?php 
            endwhile; 
        else: 
        ?>
          <!-- Clean Fallback UI state displayed if the technician_notes table is completely empty -->
          <div class="text-center py-5 px-4 border rounded-4 bg-white text-muted font-monospace fw-bold shadow-sm" style="border-style: dashed !important; border-width: 2px !important; border-color: #CBD5E1 !important;">
            <div class="mb-3" style="font-size: 38px; color: #0891B2; opacity: 0.6;"><i class="fa-solid fa-tower-broadcast"></i></div>
            <div style="font-size: 14px; text-transform: uppercase; color: #475569; letter-spacing: 0.5px;">Communication Grid Quiet</div>
            <p class="m-0 small text-secondary font-sans fw-normal mt-1">There are currently no active progress notes or logistical updates transmitted by field crews.</p>
          </div>
        <?php 
        endif; 
        ?>
      </div>
    </div>

  </div> <!-- Close Master Canvas Layout Container Wrapper -->

  <!-- Application Engine Injector Libraries -->
</body>
</html>
<?php 
if (isset($conn)) { 
    $conn->close(); 
} 
?>

