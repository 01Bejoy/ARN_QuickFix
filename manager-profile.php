<?php
// 1. Initialize Active User Session and Force Authorization Guard Rails
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Force authorization route checks for managers strictly
if (!isset($_SESSION['email']) || (isset($_SESSION['role']) && strtolower($_SESSION['role']) !== 'manager')) {
    header("Location: login.php");
    exit();
}

$managerEmail = $_SESSION['email'];
$managerName = $_SESSION['name'];

// 2. Establish High-Speed Secure Database Network Link
$conn = new mysqli("127.0.0.1", "root", "", "arn_quickfix");
if ($conn->connect_error) {
    die("Database Connection Failure: " . $conn->connect_error);
}

$updateSuccess = false;
$updateError = "";

// ====================================================================
// ✅ POST ACTION SUBMISSION CONTROLLER HANDLING LOOPS
// ====================================================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
            // IMAGE HANDLING: Interactive local file storage pipeline integration
        $imagePath = null;
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['profile_pic']['tmp_name'];
            $fileName = $_FILES['profile_pic']['name'];
            
            // ✅ FIXED: Changed pathinfo_extension to uppercase PATHINFO_EXTENSION
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($fileExtension, $allowedExtensions)) {
                if (!is_dir('img/uploads')) {
                    mkdir('img/uploads', 0777, true);
                }
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $dest_path = 'img/uploads/' . $newFileName;
                
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $imagePath = $dest_path;
                }
            }
        }


    $form_name = trim($_POST['full_name'] ?? '');
    $form_gender = $_POST['gender'] ?? '';
    $form_phone = trim($_POST['phone_number'] ?? '');
    $form_language = $_POST['language'] ?? 'English';
    $form_password = $_POST['change_password'] ?? '';
    
    // Strict Length Check matching your operational system numbers constraints
    if (!empty($form_phone) && !preg_match('/^\d{11}$/', $form_phone)) {
        $updateError = "Phone Number must contain exactly 11 numeric digits (e.g., 01712345678).";
    } else {
        // Dynamic Update Query Matrix branch pathways routing mapping options execution
        if (!empty($form_password)) {
            $newHash = password_hash($form_password, PASSWORD_BCRYPT);
            if ($imagePath !== null) {
                // Scenario A: Password Update AND Image Update
                $updateStmt = $conn->prepare("UPDATE users SET name = ?, gender = ?, phone = ?, language = ?, password_hash = ?, image_path = ? WHERE email = ?");
                $updateStmt->bind_param("sssssss", $form_name, $form_gender, $form_phone, $form_language, $newHash, $imagePath, $managerEmail);
            } else {
                // Scenario B: Password Update Only (Keep old picture intact)
                $updateStmt = $conn->prepare("UPDATE users SET name = ?, gender = ?, phone = ?, language = ?, password_hash = ? WHERE email = ?");
                $updateStmt->bind_param("ssssss", $form_name, $form_gender, $form_phone, $form_language, $newHash, $managerEmail);
            }
        } else {
            if ($imagePath !== null) {
                // Scenario C: Image Update Only (Keep old credentials password intact)
                $updateStmt = $conn->prepare("UPDATE users SET name = ?, gender = ?, phone = ?, language = ?, image_path = ? WHERE email = ?");
                $updateStmt->bind_param("ssssss", $form_name, $form_gender, $form_phone, $form_language, $imagePath, $managerEmail);
            } else {
                // Scenario D: Standard profile texts information data update loops fallback track path
                $updateStmt = $conn->prepare("UPDATE users SET name = ?, gender = ?, phone = ?, language = ? WHERE email = ?");
                $updateStmt->bind_param("sssss", $form_name, $form_gender, $form_phone, $form_language, $managerEmail);
            }
        }
        
        if ($updateStmt->execute()) {
            $_SESSION['name'] = $form_name;
            $managerName = $form_name;
            $updateSuccess = true;
        } else {
            $updateError = "Core Error: Failed writing modifications to connection matrix database.";
        }
        $updateStmt->close();
    }
}

// --------------------------------------------------------------------
// FETCH AND RE-SYNC ACTIVE LIVE CONTEXT ARRAYS DATA TO PREVENT OUTDATED BADGES
// --------------------------------------------------------------------
$stmt = $conn->prepare("SELECT id, name, gender, phone, language, manager_id, image_path FROM users WHERE email = ?");
$stmt->bind_param("s", $managerEmail);
$stmt->execute();
$managerData = $stmt->get_result()->fetch_assoc();
$stmt->close();

$db_row_id = $managerData['id'] ?? 1; 
$db_gender = $managerData['gender'] ?? '';
$db_phone = $managerData['phone'] ?? '';
$db_language = $managerData['language'] ?? 'English';

// ====================================================================
// BULLETPROOF SEGREGATED MANAGER ID GENERATOR (EMAIL LENGTH DRIVEN)
// ====================================================================
if (!empty($managerData['manager_id']) && $managerData['manager_id'] !== 'MGR-101') {
    $db_admin_id = $managerData['manager_id'];
} else {
    $emailCharacterCount = strlen($managerEmail);
    $uniqueMultiplier = (int)($managerData['id'] ?? 1) * 3;
    $finalSequentialCode = 100 + $emailCharacterCount + $uniqueMultiplier;
    
    $db_admin_id = "MGR-" . $finalSequentialCode;
    
    $updateIdStmt = $conn->prepare("UPDATE users SET manager_id = ? WHERE email = ?");
    $updateIdStmt->bind_param("ss", $db_admin_id, $managerEmail);
    $updateIdStmt->execute();
    $updateIdStmt->close();
}

// Map output variable pointer string safely for your avatar thumbnail cards below
$currentImagePath = trim($managerData['image_path'] ?? '');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manager Profile | ARN QuickFix Ltd.</title>
  
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">

  <style>
    :root {
      --primary-cyan: #00C2CB;
      --bg-slate: #F4F7F9;
      --text-dark: #333333;
      --border-gray: #E2E8F0;
    }
    body { background-color: var(--bg-slate); font-family: -apple-system, BlinkMacSystemFont, sans-serif; color: var(--text-dark); }
    .profile-navbar { background-color: #FFFFFF; border-bottom: 1px solid var(--border-gray); padding: 15px 40px; }
    .brand-accent { color: var(--primary-cyan); font-weight: 700; font-size: 24px; text-decoration: none; }
    .settings-panel { background: #FFFFFF; border: 1px solid var(--border-gray); border-radius: 12px; padding: 25px; box-shadow: 0 2px 4px rgba(0,0,0,0.01); }
    .panel-header-title { font-size: 16px; font-weight: 700; color: #1E293B; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid var(--bg-slate); padding-bottom: 10px; }
    .form-control, .form-select { height: 46px; background-color: #F8FAFC; border: 1px solid #CBD5E1; border-radius: 8px; font-size: 14px; }
    .form-control:focus, .form-select:focus { border-color: var(--primary-cyan); box-shadow: 0 0 0 3px rgba(0, 194, 203, 0.15); background-color: #FFFFFF; }
    .avatar-preview-box { width: 110px; height: 110px; border-radius: 50%; background-color: #E2E8F0; border: 3px solid #FFFFFF; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow: hidden; object-fit: cover; }
    .btn-submit-save { background-color: var(--primary-cyan); color: #FFFFFF; border: none; height: 46px; border-radius: 8px; font-weight: 700; padding: 0 35px; transition: background-color 0.2s; }
    .btn-submit-save:hover { background-color: #00AEC6; color: #FFFFFF; }
  </style>
</head>
<body>

  <!-- ================= TOP NAVIGATION BAR ================= -->
  <nav class="navbar profile-navbar d-flex align-items-center justify-content-between">
    <a href="manager-dashboard.php" class="brand-accent d-flex align-items-center gap-2">
      <img src="img/logo.svg.svg" alt="Logo" style="height: 36px; width: auto;" onerror="this.style.display='none';">
      <span>ARN QuickFix Ltd.</span>
    </a>
    <div class="d-flex align-items-center gap-3">
      <a href="manager-dashboard.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold"><i class="fa fa-arrow-left me-1"></i> Dashboard</a>
    </div>
  </nav>

  <!-- ================= MASTER SHEET CONTAINER ================= -->
  <div class="container py-5">
    
    <?php if ($updateSuccess): ?>
      <div class="alert alert-success border-0 shadow-sm rounded-3 p-3 mb-4 fw-bold font-monospace" style="border-left: 5px solid #10B981 !important;">
        🎉 Profile credentials, image upload, and parameters saved successfully!
      </div>
    <?php endif; ?>
    <?php if (!empty($updateError)): ?>
      <div class="alert alert-danger border-0 shadow-sm rounded-3 p-3 mb-4 fw-bold font-monospace" style="border-left: 5px solid #EF4444 !important;">
        ⚠️ <?php echo $updateError; ?>
      </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
      <h2 class="fw-bold m-0 text-dark" style="font-size: 26px; letter-spacing: -0.5px;">Manager Profile Settings</h2>
      <span class="badge bg-white text-dark border px-3 py-2" style="font-size: 13px; font-weight:600;">User Node: <?php echo htmlspecialchars($managerEmail); ?></span>
    </div>

    <!-- ✅ DESIGN-SAFE MASTER FORM WRAPPER CONTAINER BLOCK -->
    <form action="manager-profile.php" method="POST" enctype="multipart/form-data" class="w-100 m-0 p-0">
      <div class="row g-4">
        
        <!-- LEFT PHOTO CARD COLUMN (NOW FULLY BOUND TO FORM ELEMENT PAYLOADS) -->
        <div class="col-lg-4">
          <div class="settings-panel text-center h-100 d-flex flex-column align-items-center justify-content-center py-5">
            <div class="panel-header-title w-100">Profile Picture</div>
            
            <div class="position-relative mb-3">
              <?php if (!empty($currentImagePath) && file_exists($currentImagePath)): ?>
                <img src="<?php echo $currentImagePath; ?>?v=<?php echo time(); ?>" class="avatar-preview-box" id="avatarView" alt="Manager Picture">
              <?php else: ?>
                <div class="avatar-preview-box d-flex align-items-center justify-content-center" id="avatarPlaceholder">
                  <span style="font-size: 40px;">👤</span>
                </div>
              <?php endif; ?>
            </div>

            <p class="text-muted small mb-3 px-2">Manage your account display picture (.png, .jpg, .jpeg)</p>
            <input type="file" name="profile_pic" id="fileSelector" class="form-control d-none" accept="image/*" onchange="previewAvatar(event)">
            <input type="hidden" name="remove_avatar_flag" id="removeAvatarFlag" value="0">
            
            <div class="d-flex flex-column gap-2 w-100 px-4">
              <button type="button" class="btn btn-sm btn-light border py-2 fw-bold rounded-pill text-secondary" onclick="document.getElementById('fileSelector').click();">Choose Picture</button>
              <button type="button" class="btn btn-sm btn-outline-danger py-2 fw-bold rounded-pill" id="removePhotoBtn" onclick="removeProfilePhoto()">Remove Photo</button>
            </div>
          </div>
        </div>

        <!-- RIGHT HAND MAIN SETTINGS COLUMN -->
        <div class="col-lg-8">
          <div class="settings-panel shadow-sm">
            <div class="panel-header-title">Personal Information</div>
            
            <div class="row g-3">
                            <!-- ✅ FIXED: Normal input container allows the manager to freely change their name while remaining always visible -->
              <div class="col-12">
                <label class="form-label small fw-bold text-secondary">Full Name</label>
                <input type="text" name="full_name" class="form-control fw-bold" style="letter-spacing: 0.2px;" value="<?php echo htmlspecialchars(!empty($managerName) ? $managerName : ($_SESSION['name'] ?? 'Operations Manager')); ?>" required>
              </div>


              <!-- Column Box 2: Unique Admin ID Tracker (Stays Read-Only) -->
              <div class="col-sm-6">
                <label class="form-label small fw-bold text-secondary">Unique Manager ID</label>
                <input type="text" class="form-control text-muted font-monospace bg-light fw-bold" style="letter-spacing: 0.5px;" value="<?php echo htmlspecialchars($db_admin_id ?? ''); ?>" readonly>
              </div>

              <div class="col-sm-6">
                <label class="form-label small fw-bold text-secondary">Gender</label>
                <select name="gender" class="form-select fw-bold">
                  <option value="" disabled <?php echo empty($db_gender) ? 'selected' : ''; ?>>Select Gender</option>
                  <option value="Male" <?php echo ($db_gender === 'Male') ? 'selected' : ''; ?>>Male</option>
                  <option value="Female" <?php echo ($db_gender === 'Female') ? 'selected' : ''; ?>>Female</option>
                  <option value="Other" <?php echo ($db_gender === 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>
              </div>

              <div class="col-sm-6">
                <label class="form-label small fw-bold text-secondary">Phone Number</label>
                <input type="tel" name="phone_number" class="form-control font-monospace fw-bold" placeholder="Enter 11 digit number" value="<?php echo htmlspecialchars($db_phone ?? ''); ?>">
              </div>

              <div class="col-sm-6">
                <label class="form-label small fw-bold text-secondary">Language Preferred</label>
                <input type="text" name="language" class="form-control fw-bold" value="<?php echo htmlspecialchars($db_language ?? 'English'); ?>">
              </div>
            </div>
          </div>

          <div class="settings-panel shadow-sm mt-4">
            <div class="panel-header-title">Security & Credentials Management</div>
            <p class="text-muted small mb-3">To change your active account password parameters, complete both fields below. Leave blank to retain your current password.</p>
            
            <div class="row g-3">
              <div class="col-sm-6">
                <label class="form-label small fw-bold text-secondary">New Password</label>
                <div class="input-group">
                  <input type="password" name="change_password" id="newPassInput" class="form-control" placeholder="••••••••" style="border-right: none;">
                  <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('newPassInput', 'newPassEye')" style="border-left: none; background-color: #F8FAFC;"><span id="newPassEye">👁️</span></button>
                </div>
              </div>
              <div class="col-sm-6">
                <label class="form-label small fw-bold text-secondary">Confirm New Password</label>
                <div class="input-group">
                  <input type="password" name="confirm_password" id="confirmPassInput" class="form-control" placeholder="••••••••" style="border-right: none;">
                  <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('confirmPassInput', 'confirmPassEye')" style="border-left: none; background-color: #F8FAFC;"><span id="confirmPassEye">👁️</span></button>
                </div>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-submit-save shadow-sm text-white font-monospace fw-bold" style="background-color: #00C2CB; border:none; padding:10px 24px; border-radius:30px;"><i class="fa fa-circle-check me-1"></i> Save Profile Parameters</button>
          </div>

        </div>
      </div>
    </form>
  </div>

  <!-- ================= JAVASCRIPT CONTROLLERS VALIDATION ENGINE ================= -->
  <script>
    function previewAvatar(event) {
        const reader = new FileReader();
        reader.onload = function() {
            let view = document.getElementById('avatarView');
            if(!view) {
                const placeholder = document.getElementById('avatarPlaceholder');
                const img = document.createElement('img');
                img.id = 'avatarView';
                img.className = 'avatar-preview-box';
                if (placeholder) { placeholder.replaceWith(img); }
                view = img;
            }
            view.src = reader.result;
            document.getElementById('removeAvatarFlag').value = "0";
        }
        // ✅ FIXED ARRAY BINDING KEY INDEX FOR PRE-RENDERING THUMBNAILS
        if (event.target.files && event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }

    function removeProfilePhoto() {
        if(confirm("Are you sure you want to remove your profile display picture?")) {
            document.getElementById('removeAvatarFlag').value = "1";
            const currentImg = document.getElementById('avatarView');
            if (currentImg) {
                const placeholder = document.createElement('div');
                placeholder.id = 'avatarPlaceholder';
                placeholder.className = 'avatar-preview-box d-flex align-items-center justify-content-center';
                placeholder.innerHTML = '<span style="font-size: 40px;">👤</span>';
                currentImg.replaceWith(placeholder);
            }
            document.getElementById('fileSelector').value = "";
        }
    }

    function togglePasswordVisibility(inputId, eyeId) {
        const passInput = document.getElementById(inputId);
        const eyeIcon = document.getElementById(eyeId);
        if (passInput.type === "password") {
            passInput.type = "text";
            eyeIcon.innerText = "🙈";
        } else {
            passInput.type = "password";
            eyeIcon.innerText = "👁️";
        }
    }
  </script>
</body>
</html>
<?php if (isset($conn)) { $conn->close(); } ?>
