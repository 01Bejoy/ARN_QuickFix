<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. GATEKEEPER SECURITY SHIELD
if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || strpos($_SESSION['role'], 'tech_') !== 0) {
    header("Location: login.php");
    exit();
}

$techName = $_SESSION['name'] ?? 'Field Engineering Specialist';
$techEmail = $_SESSION['email'] ?? '';
$userRole = $_SESSION['role'] ?? 'tech_ac';

// Connect to Database
$conn = new mysqli("127.0.0.1", "root", "", "arn_quickfix");
if ($conn->connect_error) {
    die("Database Connection Node Failed: " . $conn->connect_error);
}

// ====================================================================
// ✅ FIXED: DYNAMIC TECHNICIAN SIDE-DRAWER PROFILE CONTROLLER ENGINE
// ====================================================================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action_type']) && $_POST['action_type'] === 'update_tech_profile') {
    
    $imagePathValue = null;

    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_pic']['tmp_name'];
        $fileName = $_FILES['profile_pic']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileExtension, $allowedExtensions)) {
            if (!is_dir('img/uploads')) {
                mkdir('img/uploads', 0777, true);
            }
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $dest_path = 'img/uploads/' . $newFileName;
            
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $imagePathValue = $dest_path;
            }
        }
    }

    $formFirstName  = trim($_POST['first_name'] ?? '');
    $formSecondName = trim($_POST['second_name'] ?? '');
    $formPhone      = trim($_POST['phone'] ?? '');
    $formGender     = $_POST['gender'] ?? 'Male';
    $formCountry    = $_POST['country'] ?? 'Bangladesh';
    $formLanguage   = trim($_POST['language'] ?? 'English');
    $formPassword   = $_POST['new_password'] ?? '';

    $fullNameCombined = trim($formFirstName . ' ' . $formSecondName);

    if (!empty($formPassword)) {
        $newHash = password_hash($formPassword, PASSWORD_BCRYPT);
        if ($imagePathValue !== null) {
            $stmt = $conn->prepare("UPDATE users SET name = ?, phone = ?, gender = ?, country = ?, language = ?, password_hash = ?, image_path = ? WHERE email = ?");
            $stmt->bind_param("ssssssss", $fullNameCombined, $formPhone, $formGender, $formCountry, $formLanguage, $newHash, $imagePathValue, $techEmail);
        } else {
            $stmt = $conn->prepare("UPDATE users SET name = ?, phone = ?, gender = ?, country = ?, language = ?, password_hash = ? WHERE email = ?");
            $stmt->bind_param("sssssss", $fullNameCombined, $formPhone, $formGender, $formCountry, $formLanguage, $newHash, $techEmail);
        }
    } else {
        if ($imagePathValue !== null) {
            $stmt = $conn->prepare("UPDATE users SET name = ?, phone = ?, gender = ?, country = ?, language = ?, image_path = ? WHERE email = ?");
            $stmt->bind_param("sssssss", $fullNameCombined, $formPhone, $formGender, $formCountry, $formLanguage, $imagePathValue, $techEmail);
        } else {
            $stmt = $conn->prepare("UPDATE users SET name = ?, phone = ?, gender = ?, country = ?, language = ? WHERE email = ?");
            $stmt->bind_param("ssssss", $fullNameCombined, $formPhone, $formGender, $formCountry, $formLanguage, $techEmail);
        }
    }

    if ($stmt->execute()) {
        $_SESSION['name'] = $fullNameCombined;
        $_SESSION['prof_msg'] = "🎉 Profile credentials and parameters updated successfully!";
    } else {
        $_SESSION['prof_err'] = "❌ Database Error: Failed writing profile updates.";
    }
    $stmt->close();

    header("Location: technician_dashboard.php");
    exit();
}

// ====================================================================
// Complete a field job without changing the immutable Asset ID.
// ====================================================================
$actionSuccess = ""; $actionError = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action_type']) && $_POST['action_type'] === 'complete_field_job') {
    $ticket_id=(int)($_POST['ticket_id']??0); $inventory_id=(int)($_POST['inventory_id']??0); $labor_fee=(float)($_POST['labor_fee']??0);
    if($ticket_id<1 || $labor_fee<0){$_SESSION['tech_err']='Invalid completion data.';header('Location: technician_dashboard.php');exit();}
    $q=$conn->prepare('SELECT * FROM service_requests WHERE id=? LIMIT 1');$q->bind_param('i',$ticket_id);$q->execute();$jobRow=$q->get_result()->fetch_assoc();$q->close();
    if(!$jobRow){$_SESSION['tech_err']='Job not found.';header('Location: technician_dashboard.php');exit();}
    $q=$conn->prepare("SELECT id FROM users WHERE email=? AND role LIKE 'tech_%' LIMIT 1");$q->bind_param('s',$techEmail);$q->execute();$currentTechId=(int)($q->get_result()->fetch_assoc()['id']??0);$q->close();
    if(!empty($jobRow['technician_id']) && (int)$jobRow['technician_id']!==$currentTechId){$_SESSION['tech_err']='Authorization denied: this job is assigned to another technician.';header('Location: technician_dashboard.php');exit();}
    $part_name='';$part_price=0.00;
    if($inventory_id>0){$q=$conn->prepare('SELECT part_name,part_price,stock_qty FROM inventory WHERE id=? LIMIT 1');$q->bind_param('i',$inventory_id);$q->execute();$item=$q->get_result()->fetch_assoc();$q->close();if(!$item || (int)$item['stock_qty']<1){$_SESSION['tech_err']='Selected part is unavailable.';header('Location: technician_dashboard.php');exit();}$part_name=$item['part_name'];$part_price=(float)$item['part_price'];}
    $finalTotalAmount=$labor_fee+$part_price;$conn->begin_transaction();
    try{
      if($inventory_id>0){$q=$conn->prepare('UPDATE inventory SET stock_qty=stock_qty-1 WHERE id=? AND stock_qty>0');$q->bind_param('i',$inventory_id);$q->execute();if($q->affected_rows!==1)throw new RuntimeException('Inventory stock could not be reserved.');$q->close();}
      $q=$conn->prepare("UPDATE service_requests SET status='completed', allocated_part=?, part_price=?, amount=?, original_price=COALESCE(original_price,?), current_price=?, is_read=0 WHERE id=?");$q->bind_param('sddddi',$part_name,$part_price,$finalTotalAmount,$labor_fee,$labor_fee,$ticket_id);if(!$q->execute())throw new RuntimeException($q->error);$q->close();
      if($jobRow['current_price']===null){$assetRef=!empty($jobRow['asset_ref_id'])?(int)$jobRow['asset_ref_id']:null;$prev=null;$reason='Initial service price recorded on job completion';$q=$conn->prepare('INSERT INTO service_price_history (service_request_id,asset_id,previous_price,new_price,changed_by,changed_by_email,reason) VALUES (?,?,?,?,?,?,?)');$q->bind_param('iiddiss',$ticket_id,$assetRef,$prev,$labor_fee,$currentTechId,$techEmail,$reason);$q->execute();$q->close();}
      $conn->commit();$_SESSION['tech_msg']='Job completed successfully. Asset ID remains unchanged.';
    }catch(Throwable $e){$conn->rollback();$_SESSION['tech_err']='Completion failed: '.$e->getMessage();}
    header('Location: technician_dashboard.php');exit();
}







// ====================================================================
// ✅ FORM PROCESSING: TRANSMIT ON-SITE LOGISTICAL NOTES TO MANAGER
// ====================================================================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action_type']) && $_POST['action_type'] === 'send_tech_note') {
    $note_ticket_id = (int)$_POST['ticket_id'];
    $note_message = trim($_POST['tech_message'] ?? '');

    if ($note_ticket_id > 0 && !empty($note_message)) {
        $noteStmt = $conn->prepare("INSERT INTO technician_notes (ticket_id, tech_email, tech_name, update_message) VALUES (?, ?, ?, ?)");
        $noteStmt->bind_param("isss", $note_ticket_id, $techEmail, $techName, $note_message);
        
        if ($noteStmt->execute()) {
            $_SESSION['tech_msg'] = "🚀 Note has been dispatched seamlessly! The manager has been alerted.";
        } else {
            $_SESSION['tech_err'] = "Database Fault: Failed transmitting logistical update.";
        }
        $noteStmt->close();
    }
    header("Location: technician_dashboard.php"); exit();
}

if (isset($_SESSION['tech_msg'])) { $actionSuccess = $_SESSION['tech_msg']; unset($_SESSION['tech_msg']); }
if (isset($_SESSION['tech_err'])) { $actionError = $_SESSION['tech_err']; unset($_SESSION['tech_err']); }

if (isset($_SESSION['prof_msg'])) { $profileSuccess = $_SESSION['prof_msg']; unset($_SESSION['prof_msg']); }
if (isset($_SESSION['prof_err'])) { $profileError = $_SESSION['prof_err']; unset($_SESSION['prof_err']); }

// --------------------------------------------------------------------
// FETCH EXISTING PROFILE ATTRIBUTES (DYNAMIC LIVE SYNCHRONIZATION)
// --------------------------------------------------------------------
$profileStmt = $conn->prepare("SELECT name, phone, gender, country, language, image_path, created_at FROM users WHERE email = ? LIMIT 1");
$profileStmt->bind_param('s', $techEmail);
$profileStmt->execute();
$dbUser = $profileStmt->get_result()->fetch_assoc() ?: [];
$profileStmt->close();

// Automatically parsing your single full name column value string out for dual input fields safely!
$splitNames = explode(" ", ($dbUser['name'] ?? $techName), 2);
$currentFirst = $splitNames[0] ?? '';
$currentSecond = $splitNames[1] ?? '';

$currentPhone    = $dbUser['phone'] ?? '';
$currentGender   = $dbUser['gender'] ?? '';
$currentCountry  = $dbUser['country'] ?? 'Bangladesh';
$currentLanguage = $dbUser['language'] ?? 'English';
$currentImagePath = trim($dbUser['image_path'] ?? '');


// ====================================================================
// MASTER ADAPTIVE COCKPIT REPOSITORY CONFIGURATOR
// ====================================================================
$themePrimaryColor = '#06B6D4'; // AC Default
$themeHoverColor   = '#0891B2';
$themeBgBadge      = '#ECFEFF';
$themeBadgeBorder  = '#CFFAFE';
$dashboardTitle    = "Air Conditioner (HVAC)";
$dashboardDesc     = "Dedicated crew terminal filtering cooling loops, compressor calibrations, and duct overhauls.";
$sqlFilterKeyword  = 'ac';
$sqlInvCategory    = 'ac';
$iconHeaderToken   = 'fa-snowflake';
$displaySpecialtyLabel = "Air Conditioner (HVAC)";

if ($userRole === 'tech_generator') {
    $themePrimaryColor = '#D97706'; // Generator Amber
    $themeHoverColor   = '#B45309';
    $themeBgBadge      = '#FFFBEB';
    $themeBadgeBorder  = '#FEF3C7';
    $dashboardTitle    = "Power Generator Terminal";
    $dashboardDesc     = "Dedicated crew terminal filtering alternator rebuilds, fuel polishing, and governor tuning.";
    $sqlFilterKeyword  = 'generator';
    $sqlInvCategory    = 'generator';
    $iconHeaderToken   = 'fa-bolt';
    $displaySpecialtyLabel = "Power Generators & Heavy Engines";
} elseif ($userRole === 'tech_elevator') {
    $themePrimaryColor = '#64748B'; // Elevator Slate Gray
    $themeHoverColor   = '#475569';
    $themeBgBadge      = '#F8FAFC';
    $themeBadgeBorder  = '#E2E8F0';
    $dashboardTitle    = "Elevator (Vertical Mobility) Terminal";
    $dashboardDesc     = "Dedicated crew terminal filtering hoistway alignments, hoist motor repairs, and safety interlocks.";
    $sqlFilterKeyword  = 'elevator';
    $sqlInvCategory    = 'elevator';
    $iconHeaderToken   = 'fa-arrows-up-down';
    $displaySpecialtyLabel = "Elevators, Lifts & Escalators";
}


// ====================================================================
// ✅ FIXED: ISOLATED WORKLOAD QUERY LAYER (ACTIVE TASKS ONLY)
// ====================================================================
// This ensures old completed suffix versions stay safely inside your logs and NEVER clog the queue!
$techIdLookup=$conn->prepare("SELECT id FROM users WHERE email=? AND role LIKE 'tech_%' LIMIT 1");$techIdLookup->bind_param('s',$techEmail);$techIdLookup->execute();$currentTechId=(int)($techIdLookup->get_result()->fetch_assoc()['id']??0);$techIdLookup->close();
$assignedJobs=$conn->query("SELECT * FROM service_requests WHERE status='processing' AND (LOWER(asset_type) LIKE '%".$conn->real_escape_string($sqlFilterKeyword)."%' OR LOWER(asset_type) LIKE '%conditioner%') AND (technician_id=".$currentTechId." OR location LIKE '%(Assigned to: ".$conn->real_escape_string($techName).")%') ORDER BY id DESC");

// ====================================================================
// REAL-TIME PERSONAL PERFORMANCE METRICS LEDGER ENGINE (CORRECTED)
// ====================================================================
$techEscapedName = $conn->real_escape_string($techName);

// 1. Calculate Active Assignments in the Queue
$activeCount = 0;
$qActive = $conn->query("SELECT COUNT(*) as total FROM service_requests WHERE status='processing' AND (technician_id={$currentTechId} OR location LIKE '%(Assigned to: $techEscapedName)%')");
if ($qActive) { $activeCount = (int)$qActive->fetch_assoc()['total']; }

// 2. Calculate Completed Jobs History
$completedCount = 0;
$qCompleted = $conn->query("SELECT COUNT(*) as total FROM service_requests WHERE status='completed' AND (technician_id={$currentTechId} OR location LIKE '%(Assigned to: $techEscapedName)%')");
if ($qCompleted) { $completedCount = (int)$qCompleted->fetch_assoc()['total']; }

// 3. Grouped message filters enforce strict technician name constraints
$reopenedCount = 0;
$qReopened = $conn->query("SELECT COUNT(*) as total FROM technician_notes 
                           WHERE tech_name = '$techEscapedName' 
                           AND is_read = 1 
                           AND (update_message LIKE '%site%' OR update_message LIKE '%busy%')");
if ($qReopened) { $reopenedCount = (int)$qReopened->fetch_assoc()['total']; }

// 4. Calculate Operational Success Percentage Rate
$totalLoggedTasks = $completedCount + $reopenedCount;
$successPercentageRate = 100; // Perfect score default if no tasks are logged yet
if ($totalLoggedTasks > 0) {
    $successPercentageRate = round(($completedCount / $totalLoggedTasks) * 100);
}

$partsInventory = $conn->query("SELECT id, part_name, part_price, stock_qty FROM inventory WHERE LOWER(asset_category) LIKE '%" . strtolower($sqlInvCategory) . "%' OR LOWER(asset_category) LIKE '%hvac%' OR LOWER(asset_category) LIKE '%engine%' ORDER BY part_name ASC");
?>
<!-- ================= PHP CONFIGURATOR SECTION ENDS SAFELY HERE ================= -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $dashboardTitle; ?> | Operational Desk</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root { --tech-accent: <?php echo $themePrimaryColor; ?>; --tech-hover: <?php echo $themeHoverColor; ?>; --deep-navy: #0F172A; --slate-border: #E2E8F0; --bg-canvas: #F8FAFC; }
    body { background-color: var(--bg-canvas); font-family: system-ui, -apple-system, sans-serif; color: var(--deep-navy); overflow-x: hidden; }
    .tech-navbar { background-color: var(--deep-navy); padding: 18px 45px; border-bottom: 3px solid var(--tech-accent); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .tech-navbar .brand-text { color: #FFFFFF; font-weight: 800; font-size: 19px; text-decoration: none; text-transform: uppercase; }
    .tech-navbar .brand-text span { color: var(--tech-accent); }
    .profile-nav-btn { background-color: rgba(255, 255, 255, 0.05) !important; border: 1px solid rgba(255, 255, 255, 0.15) !important; color: #FFFFFF !important; font-size: 11px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 6px 16px; border-radius: 50rem; cursor: pointer; transition: all 0.25s ease-in-out; display: flex; align-items: center; gap: 6px; }
    .profile-nav-btn:hover { background-color: var(--tech-accent) !important; border-color: var(--tech-accent) !important; transform: translateY(-1px); }
    .logout-link { color: #F1F5F9; font-weight: 700; font-size: 11px; letter-spacing: 0.5px; border: 1px solid #334155; padding: 6px 14px; border-radius: 50rem; transition: all 0.2s; text-decoration: none; }
    .logout-link:hover { background-color: #EF4444; color: #FFFFFF; border-color: #EF4444; }
    .tech-card-layout { background: #FFFFFF; border: 1px solid var(--slate-border); border-radius: 14px; padding: 25px; transition: transform 0.25s, box-shadow 0.25s; box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.01); text-align: left; }
    .form-select-custom, .form-control-custom { height: 40px; background-color: #F8FAFC; border: 1px solid #CBD5E1; border-radius: 8px; font-size: 13.5px; padding: 0 12px; outline: none; transition: all 0.2s; width: 100%; box-sizing: border-box; }
    .form-select-custom:focus, .form-control-custom:focus { border-color: var(--tech-accent); background-color: #FFFFFF; }
    .btn-complete-task { background-color: var(--tech-accent); color: #FFFFFF; font-weight: 700; font-size: 12px; height: 42px; border: none; border-radius: 8px; transition: all 0.2s; width: 100%; }
    .btn-complete-task:hover { background-color: var(--tech-hover); }
    
    /* ================= PREMIUM INTERACTIVE PROFILE CONSOLE OVERLAYS ================= */
    .profile-drawer-backdrop { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.3); backdrop-filter: blur(4px); z-index: 99999; display: none; opacity: 0; transition: opacity 0.3s ease; }
    .profile-drawer-backdrop.active { display: block; opacity: 1; }
    .profile-drawer-panel { position: fixed; top: 0; right: -460px; width: 100%; max-width: 450px; height: 100%; background: #FFFFFF; box-shadow: -10px 0 35px rgba(15, 23, 42, 0.08); z-index: 100000; transition: right 0.35s cubic-bezier(0.16, 1, 0.3, 1); padding: 35px 30px; box-sizing: border-box; overflow-y: auto; text-align: left; }
    .profile-drawer-panel.active { right: 0; }
    .drawer-close-btn { background: none; border: none; font-size: 20px; color: #94A3B8; cursor: pointer; position: absolute; top: 30px; right: 30px; z-index: 10; }
    .drawer-close-btn:hover { color: #0F172A; }
    
    /* INTERACTIVE PROFILE AVATAR CONTAINER */
    .avatar-upload-frame { width: 90px; height: 90px; border-radius: 50%; background: #F1F5F9; border: 2px dashed var(--tech-accent); display: flex; align-items: center; justify-content: center; position: relative; margin: 0 auto 15px auto; cursor: pointer; overflow: hidden; transition: background 0.2s; }
    .avatar-upload-frame:hover { background: #E2E8F0; }
    .avatar-preview-img { width: 100%; height: 100%; object-fit: cover; display: none; }
    .avatar-placeholder-text { font-size: 10px; color: var(--tech-accent); font-weight: 700; text-transform: uppercase; text-align: center; line-height: 1.2; padding: 0 4px; }
    
    .panel-group-box { margin-bottom: 16px; }
    .panel-group-box label { display: block; font-weight: 700; color: #64748B; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
  </style>
</head>
<body>

    <!-- ================= MASTER DYNAMIC ADAPTIVE NAVBAR ================= -->
  <nav class="navbar tech-navbar d-flex align-items-center justify-content-between">
    
    <!-- BRAND ALIGNMENT WRAPPER: Centers graphic vectors and typography headers on a single horizontal axis line -->
    <a href="technician_dashboard.php" class="brand-text d-flex align-items-center gap-2.5" style="text-decoration: none;">
      
      <!-- RE-ALIGNED LOGO: Clamped scale height, matching border radius, and translucent ambient blend container -->
      <img src="img/logo.svg.svg" alt="Logo" style="height: 50px; width: auto; object-fit: contain; border-radius: 6px; padding: 3px; background-color: rgba(255, 255, 255, 0.08); vertical-align: middle;" onerror="this.style.display='none';">

      <!-- TYPOGRAPHY LAYER: Keeps your operational font tokens flowing cleanly adjacent to the logo icon graphic -->
      <span style="display: flex; align-items: center; font-weight: 800; font-size: 19px; color: #FFFFFF; text-transform: uppercase; letter-spacing: -0.3px;">
        <i class="fa-solid <?php echo $iconHeaderToken; ?> me-2 ms-1" style="color: var(--tech-accent);"></i> ARN QuickFix Ltd. 
        <span style="color: var(--tech-accent); margin-left: 6px;"><?php echo $dashboardTitle; ?></span>
      </span>
      
    </a>

    <div class="d-flex align-items-center gap-3">

    <div class="d-flex align-items-center gap-3">
      <button type="button" class="profile-nav-btn" onclick="openProfileDrawer()">
        <i class="fa-solid fa-circle-user text-white" style="font-size:14px;"></i> My Profile
      </button>
            <!-- ✅ FIXED NAVBAR CREW SPEC CARD WITH SOLID WHITE TEXT PARAMETERS -->
      <div class="text-white small fw-bold px-3 py-1.5 rounded-pill d-none d-md-block" style="background-color: rgba(255,255,255,0.04); border: 1px solid #334155;">
        Crew Specialist: <span style="color: #FFFFFF !important; font-weight: 700;"><?php echo htmlspecialchars($techName); ?></span>
      </div>

      <a href="logout.php" class="logout-link text-uppercase"><i class="fa-solid fa-power-off me-1"></i> Logout</a>
    </div>
  </nav>

  <!-- ================= DYNAMIC SIDE PROFILE DRAW INTERFACE ================= -->
  <div id="profileBackdrop" class="profile-drawer-backdrop" onclick="closeProfileDrawer()"></div>
  <div id="profileDrawer" class="profile-drawer-panel">
    <button type="button" class="drawer-close-btn" onclick="closeProfileDrawer()"><i class="fa-solid fa-xmark"></i></button>
    
    <h4 class="fw-bold text-dark mb-1" style="font-size: 20px; letter-spacing: -0.5px;">Account Management</h4>
    <p class="text-muted small mb-4">View credentials, update records, and adjust security preferences.</p>

    <!-- Handoff Success Banners Inside the Drawer Console Grid -->
    <?php if (!empty($profileSuccess)): ?>
      <div class="alert border-0 small py-2 px-3 mb-3 font-monospace" style="background-color:#F0FDF4; color:#16A34A; border:1px solid #DCFCE7; font-size:12px;"><?php echo $profileSuccess; ?></div>
    <?php endif; ?>
    <?php if (!empty($profileError)): ?>
      <div class="alert border-0 small py-2 px-3 mb-3 font-monospace" style="background-color:#FEF2F2; color:#EF4444; border:1px solid #FEE2E2; font-size:12px;"><?php echo $profileError; ?></div>
    <?php endif; ?>

        <!-- ✅ FIXED: Added enctype attribute to allow image binaries to safely upload to your server disk -->
    <form action="technician_dashboard.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="action_type" value="update_tech_profile">


                  <!-- Interactive Picture Frame Element Slot -->
      <div class="avatar-upload-frame d-flex align-items-center justify-content-center mx-auto mb-3" onclick="document.getElementById('avatarFileInput').click()" style="cursor: pointer; width: 100px; height: 100px; border: 2px dashed #CBD5E1; border-radius: 50%; overflow: hidden; position: relative; background-color: #F8FAFC;">
        
        <?php if (!empty($currentImagePath) && file_exists($currentImagePath)): ?>
          <!-- ✅ FIXED: Inline dimensional constraints lock the photo inside the 100px circular bounds flawlessly -->
          <img id="avatarImgElement" src="<?php echo htmlspecialchars($currentImagePath); ?>?v=<?php echo time(); ?>" alt="Profile Preview" style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;">
        <?php else: ?>
          <!-- Fallback layout when no image path pointer string exists in your database table cell -->
          <img id="avatarImgElement" class="d-none" alt="Profile Preview" style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;">
          <div id="avatarTextElement" class="avatar-placeholder-text text-center text-muted" style="font-size: 11px; font-weight: 600;">
            <i class="fa-solid fa-camera mb-1 d-block" style="font-size:14px;"></i>Insert Pic
          </div>
        <?php endif; ?>

      </div>
      <input type="file" id="avatarFileInput" name="profile_pic" accept="image/*" style="display:none;" onchange="previewProfileImageFiles(event)">


      <div class="row g-2">
        <div class="col-6 panel-group-box">
          <label>First Name</label>
          <input type="text" name="first_name" class="form-control-custom" value="<?php echo htmlspecialchars($currentFirst); ?>" required>
        </div>
        <div class="col-6 panel-group-box">
          <label>Second Name</label>
          <input type="text" name="second_name" class="form-control-custom" value="<?php echo htmlspecialchars($currentSecond); ?>" required>
        </div>
      </div>

      <div class="panel-group-box">
        <label>Email ID (Static Clearance Key)</label>
        <input type="text" class="form-control-custom text-muted bg-light" value="<?php echo htmlspecialchars($techEmail); ?>" readonly style="cursor:not-allowed;">
      </div>


            <!-- Contact Phone Number Field (UPDATED WITH AIRTIGHT 11-DIGIT CONSTRAINTS) -->
      <div class="panel-group-box">
        <label>Contact Phone Number (11 Digits)</label>
        
        <!-- ✅ LOCKED: Enforces digits-only typing, max/min bounds of 11 characters, and an interactive pop-up browser tip -->
        <input type="text" 
               name="phone" 
               class="form-control-custom font-monospace" 
               value="<?php echo htmlspecialchars($currentPhone); ?>" 
               placeholder="e.g. 017XXXXXXXX"
               maxlength="11"
               minlength="11"
               pattern="[0-9]{11}"
               title="Please enter exactly 11 numeric digits (numbers only)."
               oninput="this.value = this.value.replace(/[^0-9]/g, '');"
               required>
      </div>


      <div class="panel-group-box">
        <label>Gender Profile</label>
        <select name="gender" class="form-select form-select-custom">
          <option value="Male" <?php echo ($currentGender === 'Male') ? 'selected' : ''; ?>>Male</option>
          <option value="Female" <?php echo ($currentGender === 'Female') ? 'selected' : ''; ?>>Female</option>
          <option value="Other" <?php echo ($currentGender === 'Other') ? 'selected' : ''; ?>>Other / Secret</option>
        </select>
      </div>

      <div class="row g-2">
        <div class="col-6 panel-group-box">
          <label>Country Operations</label>
          <select name="country" class="form-select form-select-custom">
            <option value="Bangladesh" <?php echo ($currentCountry === 'Bangladesh') ? 'selected' : ''; ?>>Bangladesh</option>
            <option value="United States" <?php echo ($currentCountry === 'United States') ? 'selected' : ''; ?>>United States</option>
            <option value="United Kingdom" <?php echo ($currentCountry === 'United Kingdom') ? 'selected' : ''; ?>>United Kingdom</option>
            <option value="Germany" <?php echo ($currentCountry === 'Germany') ? 'selected' : ''; ?>>Germany</option>
          </select>
        </div>
        <div class="col-6 panel-group-box">
          <label>Language Medium</label>
          <input type="text" name="language" class="form-control-custom" value="<?php echo htmlspecialchars($currentLanguage); ?>" placeholder="e.g. English, Bangla">
        </div>
      </div>

      <div class="panel-group-box">
        <label>Change Account Password</label>
        <div style="position:relative; width:100%;">
          <input type="password" id="drawerPasswordField" name="new_password" class="form-control-custom" placeholder="Leave blank to retain current pass">
          <button type="button" id="drawerPasswordToggleBtn" style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; padding:0; cursor:pointer; display:flex; align-items:center; justify-content:center;">
            <svg id="drawerEyeSvg" xmlns="http://w3.org" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
          </button>
        </div>
      </div>

      <div class="mt-4 pt-1 d-flex gap-2">
        <button type="submit" class="btn text-white fw-bold w-100" style="background-color: var(--tech-accent); height:42px; font-size:13px; border-radius:8px;">Save Profile Changes</button>
        <button type="button" onclick="closeProfileDrawer()" class="btn btn-light fw-bold w-50" style="height:42px; font-size:13px; border:1px solid #CBD5E1; border-radius:8px;">Dismiss</button>
      </div>
    </form>
  </div>

  <!-- ================= CANVAS APP WINDOW CONTAINER ================= -->
    <!-- ================= CANVAS APP WINDOW CONTAINER ================= -->
  <!-- ✅ UPGRADED: Expanded to max-width 1340px to support responsive multi-column statistics panels without crowding -->
  <div class="container py-5" style="max-width: 1340px;">
    
    <!-- Desk Presentation Title Section Box Header Layout -->
    <div class="mb-4 d-flex align-items-center justify-content-between bg-white p-4 border rounded-4 shadow-sm text-start">
      <div>
        <h2 class="fw-bold m-0 text-dark" style="font-size: 24px; letter-spacing: -0.5px;"><?php echo $dashboardTitle; ?> Queue</h2>
        <p class="text-muted m-0 small fw-medium mt-1.5"><?php echo $dashboardDesc; ?></p>
      </div>
      <div class="text-end font-monospace d-none d-sm-block">
        <div class="small fw-bold text-uppercase text-muted" style="font-size:10px; letter-spacing:0.5px;">Live Dispatches</div>
        <div class="fw-extrabold text-dark m-0" style="font-size:32px; font-weight:800; line-height:1;"><?php echo $assignedJobs ? $assignedJobs->num_rows : 0; ?></div>
      </div>
    </div>

    <!-- Active Job Processing Notifications Layer -->
    <?php if (!empty($actionSuccess)): ?>
      <div class="alert border-0 small py-3 px-4 font-monospace shadow-sm mb-4 text-start" style="background-color:#F0FDF4; color:#16A34A; border-left:4px solid #10B981 !important; font-size:13.5px;"><?php echo $actionSuccess; ?></div>
    <?php endif; ?>
    <?php if (!empty($actionError)): ?>
      <div class="alert border-0 small py-3 px-4 font-monospace shadow-sm mb-4 text-start" style="background-color:#FEF2F2; color:#EF4444; border-left:4px solid #EF4444 !important; font-size:13.5px;"><?php echo $actionError; ?></div>
    <?php endif; ?>

    <!-- DYNAMIC RESPONSIVE SIDEBAR GRID INFRASTRUCTURE MATRIX -->
    <div class="row g-4">
      
          <!-- DYNAMIC RESPONSIVE SIDEBAR GRID INFRASTRUCTURE MATRIX -->
    <div class="row g-4">
      
      <!-- ================= 1. LEFT SIDEBAR COLUMN: TECHNICIAN PROFILE SUMMARY ================= -->
      <div class="col-xl-3 col-lg-4 text-start">
        <div class="bg-white border rounded-4 p-4 shadow-sm h-100 d-flex flex-column gap-3">
          <h6 class="fw-bold text-dark text-uppercase font-monospace m-0" style="font-size: 11px; letter-spacing: 0.6px; color: var(--tech-accent) !important;">
            <i class="fa-solid fa-user-shield me-1"></i> Crew Authorization
          </h6>
          
          <div class="p-3 rounded-3 bg-light border-0" style="background-color: #F8FAFC !important;">
            <span class="text-muted d-block font-monospace text-uppercase" style="font-size: 9px; letter-spacing: 0.3px;">Terminal Duty Clearance</span>
            <strong class="text-dark d-block mt-0.5" style="font-size: 14px;">Senior Lead Engineer</strong>
          </div>

          <div class="p-3 rounded-3 bg-light border-0" style="background-color: #F8FAFC !important;">
            <span class="text-muted d-block font-monospace text-uppercase" style="font-size: 9px; letter-spacing: 0.3px;">Classification Access Token</span>
            <strong class="text-secondary font-monospace d-block small mt-0.5" style="font-size: 12px; letter-spacing: -0.2px;"><?php echo htmlspecialchars($userRole); ?></strong>
          </div>

          <div class="p-3 rounded-3 bg-light border-0" style="background-color: #F8FAFC !important;">
            <span class="text-muted d-block font-monospace text-uppercase" style="font-size: 9px; letter-spacing: 0.3px;">Operational Domain</span>
            <div class="mt-1">
              <span class="badge font-sans" style="font-size: 10px; font-weight: 600; padding: 4px 8px; background-color: <?php echo $themeBgBadge; ?>; color: var(--tech-accent); border: 1px solid <?php echo $themeBadgeBorder; ?>;">
                <?php echo $displaySpecialtyLabel; ?>
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- ================= 2. MIDDLE MAIN COLUMN: JOB CARDS LOOP OR EMPTY STATE ================= -->
      <!-- ✅ FIXED: This column is properly isolated as col-xl-6 so it frames perfectly in the center rail -->
      <div class="col-xl-6 col-lg-8">
        <div class="d-flex flex-column gap-4">
          
          <?php if ($assignedJobs && $assignedJobs->num_rows > 0): ?>
            <?php while ($job = $assignedJobs->fetch_assoc()):
            ?>
              <!-- Ticket Node Container Card Wrapper Frame -->
              <div class="tech-card-layout mb-3">
                <div class="row g-4 align-items-center">
                  
                  <!-- Section A: Live Ticket Context Specs -->
                  <div class="col-12 text-start">
                    <div class="d-flex align-items-center gap-2 mb-2">
                      <span class="badge font-monospace" style="font-size:9.5px; font-weight:700; background-color: <?php echo $themeBgBadge; ?>; color: var(--tech-accent); border:1px solid <?php echo $themeBadgeBorder; ?>;">Ticket #<?php echo $job['id']; ?></span>
                    </div>
                    <h5 class="fw-bold text-dark m-0" style="font-size:16px; letter-spacing:-0.2pxিলেন্স;"><?php echo htmlspecialchars($job['asset_brand'] . ' ' . $job['asset_type']); ?></h5>
                    
                    <div class="text-secondary font-monospace mt-1 fw-medium" style="font-size:12px;"><i class="fa-solid fa-hashtag text-muted me-1"></i> ID: <span class="text-dark font-sans fw-bold"><?php echo htmlspecialchars($job['asset_id']); ?></span></div>
                    <div class="text-muted font-monospace mt-0.5" style="font-size:11.5px;"><i class="fa-solid fa-envelope me-1"></i> <?php echo htmlspecialchars($job['client_email']); ?></div>
                    
                    <div class="text-dark font-monospace mt-0.5" style="font-size:11.5px; font-weight:600;"><i class="fa-solid fa-phone text-muted me-1"></i> <?php echo htmlspecialchars($job['phone'] ?? 'No Phone Logged'); ?></div>
                    <div class="text-secondary small mt-0.5" style="font-size:12px; font-weight:500;"><i class="fa-solid fa-location-dot text-danger me-1"></i> <?php echo htmlspecialchars($job['location'] ?? 'No Site Address'); ?></div>
                    
                    <div class="mt-2.5 pt-2 border-top small text-secondary" style="font-size:12px;"><strong>Issue Details:</strong> <span class="text-dark"><?php echo htmlspecialchars($job['problem_category']); ?></span></div>
                  </div>

                  <!-- Section B: Live Interface Action Input Forms -->
                  <div class="col-12">
                    <form action="technician_dashboard.php" method="POST" class="row g-3 align-items-end text-start">
                      <input type="hidden" name="action_type" value="complete_field_job">
                      <input type="hidden" name="ticket_id" value="<?php echo $job['id']; ?>">
                      
                      <div class="col-md-5">
                        <label class="small fw-bold text-secondary text-uppercase mb-2" style="font-size:10px; letter-spacing:0.5px;"><i class="fa-solid fa-box-open me-1"></i> Warehouse Component</label>
                        <select name="inventory_id" class="form-select form-select-custom" required>
                          <option value="0">No Extra Parts (Standard Consumables)</option>
                          <?php 
                            if ($partsInventory && $partsInventory->num_rows > 0):
                                $partsInventory->data_seek(0);
                                while ($part = $partsInventory->fetch_assoc()):
                                    $qty = (int)$part['stock_qty'];
                          ?>
                            <option value="<?php echo $part['id']; ?>" <?php echo ($qty === 0) ? 'disabled' : ''; ?>>
                              <?php echo htmlspecialchars($part['part_name']); ?> (৳<?php echo number_format($part['part_price']); ?>) — Stock: <?php echo $qty; ?>
                            </option>
                          <?php 
                                endwhile;
                            endif;
                          ?>
                        </select>
                      </div>

                      <div class="col-md-4">
                        <label class="small fw-bold text-secondary text-uppercase mb-2" style="font-size:10px; letter-spacing:0.5px;"><i class="fa-solid fa-calculator me-1"></i> Labor Fee (৳)</label>
                        <?php 
                          $rawCategoryText = trim($job['problem_category'] ?? '');
                          $calculatedBaseRate = 2500;

                          switch ($rawCategoryText) {
                              case 'Component Repair':    $calculatedBaseRate = 4500; break;
                              case 'Part Replacement':     $calculatedBaseRate = 3000; break;
                              case 'Modernization':        $calculatedBaseRate = 15000; break;
                              case 'Routine Servicing':    $calculatedBaseRate = 2000; break;
                              case 'Emergency Breakdown':  $calculatedBaseRate = 5000; break;
                              case 'Basic Servicing':      $calculatedBaseRate = 600; break;
                              case 'Deep Cleaning':        $calculatedBaseRate = 1200; break;
                              case 'Duct Cleaning':        $calculatedBaseRate = 5000; break;
                              case 'Gas Refill':           $calculatedBaseRate = 2500; break;
                              case 'Electrical Repair':    $calculatedBaseRate = 1500; break;
                              case 'Compressor Repair':    $calculatedBaseRate = 4000; break;
                          }
                        ?>
                        <?php if (isset($job['current_price']) && $job['current_price'] !== null && (float)$job['current_price'] > 0) { $calculatedBaseRate = (float)$job['current_price']; } ?>
                        <input type="number" name="labor_fee" class="form-control form-control-custom font-monospace text-secondary bg-light" value="<?php echo $calculatedBaseRate; ?>" readonly style="cursor:not-allowed;" required>
                      </div>

                      <div class="col-md-3">
                        <button type="submit" class="btn btn-complete-task text-uppercase fw-bold w-100" style="height: 40px;">
                          <i class="fa-solid fa-circle-check me-1"></i> Finish Job
                        </button>
                      </div>
                    </form>

                    <div class="mt-3 pt-2 border-top" style="border-style: dashed !important; border-color: var(--slate-border) !important;">
                      <form action="technician_dashboard.php" method="POST" class="d-flex gap-2 align-items-center">
                        <input type="hidden" name="action_type" value="send_tech_note">
                        <input type="hidden" name="ticket_id" value="<?php echo $job['id']; ?>">
                        
                        <div class="position-relative flex-grow-1">
                          <input type="text" name="tech_message" class="form-control form-control-custom" style="height: 36px; font-size:12.5px; padding-left: 32px;" placeholder="Send progress update, delay notice, or site notes to manager..." required>
                          <i class="fa-solid fa-comment-dots text-muted position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); font-size: 13px;"></i>
                        </div>
                        
                        <button type="submit" class="btn btn-sm btn-light fw-bold border text-uppercase" style="height: 36px; font-size: 11px; letter-spacing: 0.3px; color: #475569; white-space: nowrap;">
                          <i class="fa-solid fa-paper-plane me-1" style="color: var(--tech-accent);"></i> Send Note
                        </button>
                      </form>
                    </div>
                  </div>

                </div>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <!-- Clean Fallback View State when workload queue is completely checked out -->
            <div class="text-center py-5 px-4 border rounded-4 bg-white text-muted font-monospace fw-bold shadow-sm d-flex flex-column align-items-center justify-content-center" style="border-style: dashed !important; border-width: 2px !important; border-color: #CBD5E1 !important; min-height: 290px;">
              <div class="mb-3" style="font-size: 38px; color: var(--tech-accent); opacity: 0.6;"><i class="fa-solid fa-circle-check"></i></div>
              <div style="font-size: 14px; text-transform: uppercase; color: #475569; letter-spacing: 0.5px;">Workload Operations Clear</div>
              <p class="m-0 small text-secondary font-sans fw-normal mt-1" style="max-width: 380px;">Excellent job! There are currently no active repair dispatches assigned under this technician classification profile.</p>
            </div>
          <?php endif; ?>

        </div>
      </div> <!-- ✅ CLOSES MIDDLE COLUMN CLEANLY -->

      <!-- ================= 3. RIGHT COLUMN: DYNAMIC PERSONAL METRICS LEDGER ================= -->
      <div class="col-xl-3 col-lg-4 text-start">
        <div class="bg-white border rounded-4 p-4 shadow-sm h-100 d-flex flex-column gap-3">
          <h6 class="fw-bold text-dark text-uppercase font-monospace m-0" style="font-size: 11px; letter-spacing: 0.6px; color: var(--tech-accent) !important;">
            <i class="fa-solid fa-square-poll-vertical me-1"></i> Personal Analytics
          </h6>

          <!-- Metric Row 1: Active Queue -->
          <div class="d-flex align-items-center justify-content-between p-2.5 rounded bg-light border-0" style="background-color: #F8FAFC !important;">
            <span class="small text-secondary fw-medium" style="font-size: 12px; font-weight: 500;"><i class="fa-solid fa-clock-rotate-left text-primary me-1"></i> Active Queue</span>
            <span class="badge rounded-pill font-monospace fw-bold" style="font-size: 11px; background-color: <?php echo $themeBgBadge; ?>; color: var(--tech-accent); padding: 4px 8px;">
              <?php echo $activeCount; ?> Tasks
            </span>
          </div>

          <!-- Metric Row 2: Completed Jobs -->
          <div class="d-flex align-items-center justify-content-between p-2.5 rounded bg-light border-0" style="background-color: #F8FAFC !important;">
            <span class="small text-secondary fw-medium" style="font-size: 12px; font-weight: 500;"><i class="fa-solid fa-circle-check text-success me-1"></i> Completed Jobs</span>
            <span class="badge rounded-pill font-monospace fw-bold" style="font-size: 11px; background-color: #E6F4EA; color: #137333; padding: 4px 8px;">
              <?php echo $completedCount; ?> Done
            </span>
          </div>

          <!-- Metric Row 3: Re-Opened / Cancelled -->
          <div class="d-flex align-items-center justify-content-between p-2.5 rounded bg-light border-0" style="background-color: #F8FAFC !important;">
            <span class="small text-secondary fw-medium" style="font-size: 12px; font-weight: 500;"><i class="fa-solid fa-rotate-left text-danger me-1"></i> Re-Opened Tasks</span>
            <span class="badge rounded-pill font-monospace fw-bold" style="font-size: 11px; background-color: #FCE8E6; color: #C5221F; padding: 4px 8px;">
              <?php echo $reopenedCount; ?> Blocked
            </span>
          </div>

          <!-- ✅ NEW ADDITION: TOTAL WORK DISPATCHES LEDGER COUNTER -->
          <div class="d-flex align-items-center justify-content-between p-2.5 rounded bg-light border-0" style="background-color: #F8FAFC !important;">
            <span class="small text-secondary fw-medium" style="font-size: 12px; font-weight: 500;"><i class="fa-solid fa-folder-open text-secondary me-1"></i> Total Assignments</span>
            <span class="badge rounded-pill font-monospace fw-bold" style="font-size: 11px; background-color: #F1F5F9; color: #475569; padding: 4px 8px;">
              <?php echo $totalLoggedTasks; ?> Allotted
            </span>
          </div>


          <!-- Metric Row 4: Success Performance Percentage -->
          <div class="p-3 rounded-3 border-0 mt-1" style="background-color: #F8FAFC;">
            <div class="d-flex align-items-center justify-content-between mb-1.5">
              <span class="small text-dark fw-bold" style="font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.3px;"><i class="fa-solid fa-gauge-high me-1 text-warning"></i> Success Rating</span>
              <span class="font-monospace fw-extrabold text-dark" style="font-size: 14px; font-weight: 800;"><?php echo $successPercentageRate; ?>%</span>
            </div>
            <div class="progress" style="height: 6px; background-color: #E2E8F0; border-radius: 50rem; overflow: hidden;">
              <div class="progress-bar" role="progressbar" 
                   style="width: <?php echo $successPercentageRate; ?>%; background-color: <?php echo ($successPercentageRate >= 80) ? '#10B981' : (($successPercentageRate >= 50) ? '#F59E0B' : '#EF4444'); ?>; border-radius: 50rem;" 
                   aria-valuenow="<?php echo $successPercentageRate; ?>" aria-valuemin="0" aria-valuemax="100">
              </div>
            </div>
          </div>
          
          <div class="mt-2 pt-2.5 border-top d-flex align-items-center justify-content-between font-monospace" style="border-style: dashed !important; border-color: #E2E8F0 !important; font-size: 10px; opacity: 0.7;">
            <span><i class="fa-solid fa-shield-halved text-info me-1"></i> Security: Encrypted</span>
            <span>Session: Live</span>
          </div>
        </div>
      </div>

    </div> <!-- ✅ CLOSES YOUR MASTER GRID CLASS ROW CORRECTLY -->
  </div> <!-- ✅ CLOSES YOUR CONTAINER ACCESS CANVAS BOX CORRECTLY -->

    <!-- ==================================================================== -->
  <!-- ✅ NEW COMPONENT: COMPLETED OPERATIONS PERFORMANCE LEDGER -->
  <!-- ==================================================================== -->
  <div class="row mt-4 m-0 p-0 w-100">
    <div class="col-12 p-0">
      <div class="card border-0 shadow-sm rounded-4 p-4 bg-white" style="border: 1px solid #E2E8F0 !important;">
        
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
          <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background-color: var(--themeBgBadge, <?php echo $themeBgBadge; ?>);">
              <i class="fa-solid fa-clock-rotate-left" style="color: <?php echo $themePrimaryColor; ?>; font-size: 14px;"></i>
            </div>
            <div>
              <h5 class="fw-bold m-0 text-dark" style="font-size: 16px; letter-spacing: -0.3px;">Completed Operations Ledger</h5>
              <p class="text-muted m-0 p-0" style="font-size: 11px;">Archived operational records closed by Specialist: <?php echo htmlspecialchars($techName); ?></p>
            </div>
          </div>
          <span class="badge font-monospace border px-2 py-1 bg-light text-dark" style="font-size: 11px; font-weight: 600;">Secure Node Identity Locked</span>
        </div>

        <?php
          // Fetch historical records closed strictly by this logged-in technician name
          $techEscapedSearch = $conn->real_escape_string($techName);
          $historyLogQuery = $conn->query("SELECT * FROM service_requests 
                                           WHERE status = 'completed' 
                                           AND location LIKE '%(Assigned to: $techEscapedSearch)%' 
                                           ORDER BY id DESC");
        ?>

        <?php if ($historyLogQuery && $historyLogQuery->num_rows > 0): ?>
          <div class="table-responsive w-100" style="max-height: 300px; overflow-y: auto;">
            <table class="table table-hover align-middle m-0" style="font-size: 12.5px;">
              <thead class="table-light sticky-top" style="z-index: 5; background-color: #F8FAFC;">
                <tr class="text-secondary border-bottom" style="font-size: 11px; text-transform: uppercase; font-weight: 700;">
                  <th scope="col" class="ps-3 py-2" style="width: 80px;">Ticket</th>
                  <th scope="col" style="width: 140px;">Client Node</th>
                  <th scope="col" style="width: 110px;">Asset ID</th>
                  <th scope="col">Operational Fault Category & Logistical Notes</th>
                  <th scope="col" style="width: 130px;">Allocated Component</th>
                  <th scope="col" class="pe-3 text-end" style="width: 110px;">Settled Fee</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($logRow = $historyLogQuery->fetch_assoc()): ?>
                  <tr class="border-bottom" style="transition: background-color 0.2s ease;">
                    <td class="ps-3 font-monospace fw-bold text-dark">#<?php echo $logRow['id']; ?></td>
                    <td>
                      <div class="fw-bold text-dark"><?php echo htmlspecialchars($logRow['client_email']); ?></div>
                      <div class="text-muted font-monospace" style="font-size: 11px;"><i class="fa fa-phone me-1"></i><?php echo htmlspecialchars($logRow['phone']); ?></div>
                    </td>
                    <td>
                      <span class="badge border font-monospace text-dark px-2 py-1 bg-white shadow-sm" style="border-radius: 5px;">
                        <?php echo htmlspecialchars($logRow['asset_id']); ?>
                      </span>
                    </td>
                    <td>
                      <div class="fw-bold" style="color: <?php echo $themePrimaryColor; ?>;"><?php echo htmlspecialchars($logRow['problem_category']); ?></div>
                      <div class="text-secondary mt-0.5 pe-3" style="font-size: 11.5px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                        <i class="fa-solid fa-location-dot me-1 text-muted" style="font-size: 10px;"></i><?php echo htmlspecialchars($logRow['location']); ?>
                      </div>
                    </td>
                    <td>
                      <?php if (!empty($logRow['allocated_part'])): ?>
                        <div class="fw-bold text-dark" style="font-size: 12px;"><i class="fa fa-gears me-1 text-muted"></i><?php echo htmlspecialchars($logRow['allocated_part']); ?></div>
                        <div class="text-muted font-monospace" style="font-size: 11px;">Part: BDT <?php echo number_format($logRow['part_price'], 2); ?></div>
                      <?php else: ?>
                        <span class="text-muted font-sans italic" style="font-size: 11.5px;">No Component Replaced</span>
                      <?php endif; ?>
                    </td>
                    <td class="pe-3 text-end font-monospace fw-bold text-success" style="font-size: 13px;">
                      BDT <?php echo number_format($logRow['amount'] ?? 0.00, 2); ?>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <!-- Clean empty placeholder if they haven't compiled a job history yet -->
          <div class="text-center py-4 border rounded-3 bg-light" style="border-style: dashed !important; background-color: #F8FAFC !important;">
            <div class="text-muted mb-1" style="font-size: 24px;">📁</div>
            <div class="small fw-bold text-secondary">Historical Ledger Dormant</div>
            <div class="text-muted" style="font-size: 11px;">No completed operational tickets archived under this specialist profile track.</div>
          </div>
        <?php endif; ?>

      </div>
    </div>
  </div>



  <!-- ================= MASTER USER INTERACTION JAVASCRIPT ================= -->
  <script>
    function openProfileDrawer() {
      const backdrop = document.getElementById('profileBackdrop');
      const drawer = document.getElementById('profileDrawer');
      backdrop.style.display = 'block';
      setTimeout(() => { backdrop.classList.add('active'); drawer.classList.add('active'); }, 10);
    }

    function closeProfileDrawer() {
      const backdrop = document.getElementById('profileBackdrop');
      const drawer = document.getElementById('profileDrawer');
      backdrop.classList.remove('active'); drawer.classList.remove('active');
      setTimeout(() => { backdrop.style.display = 'none'; }, 350);
    }

    // Interactive Image Upload Rendering Preview Engine
    function previewProfileImageFiles(event) {
      const input = event.target;
      const previewImg = document.getElementById('avatarImgElement');
      const placeholderText = document.getElementById('avatarTextElement');
      
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          previewImg.src = e.target.result;
          previewImg.style.display = 'block';
          placeholderText.style.display = 'none';
        }
        reader.readAsDataURL(input.files[0]);
      }
    }

    // Eye Icon Input Password Toggle Layer
    document.getElementById('drawerPasswordToggleBtn').addEventListener('click', function () {
      const passwordField = document.getElementById('drawerPasswordField');
      const eyeSvg = document.getElementById('drawerEyeSvg');
      const isPassword = passwordField.getAttribute('type') === 'password';
      
      passwordField.setAttribute('type', isPassword ? 'text' : 'password');
      
      if (isPassword) {
        eyeSvg.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
      } else {
        eyeSvg.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
      }
    });
  </script>
</body>
</html>
<?php if (isset($conn)) { $conn->close(); } ?>