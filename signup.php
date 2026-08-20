<?php
// 1. Establish Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "arn_quickfix";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. Wait for Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Collect and sanitize user input strings safely
    $rawRoleSelection = strtolower(trim($_POST['role'] ?? 'client'));
    $rawSpecialization = trim($_POST['specialization'] ?? '');

    // ====================================================================
    // INTELLIGENT FIELD ENGINEERING ROLE TOKEN ALLOCATOR (PERFECT SYNC)
    // ====================================================================
    // FIXED: Perfectly matches your exact HTML values ('AC', 'Generator', 'Elevator')
    $finalRoleToken = 'client'; // Default fallback marker
    $savedSpecializationString = '';

    if ($rawRoleSelection === 'technician') {
        $savedSpecializationString = $rawSpecialization;
        
        // Exact case-sensitive match against your specific HTML option values!
        if ($rawSpecialization === 'AC') { 
            $finalRoleToken = 'tech_ac'; 
        } elseif ($rawSpecialization === 'Generator') { 
            $finalRoleToken = 'tech_generator'; 
        } elseif ($rawSpecialization === 'Elevator') { 
            $finalRoleToken = 'tech_elevator'; 
        } else {
            // Safety fallback if choice is empty or unrecognized
            $finalRoleToken = 'tech_ac'; 
        }
    } else {
        $finalRoleToken = $rawRoleSelection; // Keeps 'manager', 'client', or 'user' pristine
    }

    // Your input collection lines continue exactly right below here:



    $first_name = $_POST['first_name'];
    $second_name = $_POST['second_name'];
    $fullName = $first_name . " " . $second_name; 
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];
    
    // Check if passwords match
    if ($pass !== $confirm_pass) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit();
    }

    // Check if email already exists in the database
    $checkEmail = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();
    
    if ($checkEmail->num_rows > 0) {
        echo "<script>alert('This email address is already registered!'); window.history.back();</script>";
        $checkEmail->close();
        $conn->close();
        exit();
    }
    $checkEmail->close();
    
    // Securely hash the password
    $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
    
    // 3. Prepare SQL query (Saves your clean, unified $finalRoleToken straight into your database!)
    $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, role, specialization) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $fullName, $email, $hashed_password, $finalRoleToken, $savedSpecializationString);
    
       if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        
        // SUCCESS HOOK: Stash a success flag parameter inside your session memory
        $_SESSION['registration_success_trigger'] = true;
        
        header("Location: login.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

}
?>





<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up | ARN QuickFix Ltd.</title>
  
  <style>
    :root {
      --primary-color: #00C2CB;
      --text-dark: #333333;
      --text-muted: #888888;
      --border-color: #cbd5e1;
      --bg-card: #ffffff;
      --font-stack: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    body {
      background-color: #f1f5f9;
      font-family: var(--font-stack);
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
      padding: 40px 20px;
      box-sizing: border-box;
    }

    .signup-container {
      background: var(--bg-card);
      width: 100%;
      max-width: 500px;
      padding: 50px 40px 40px 40px;
      border-radius: 20px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
      box-sizing: border-box;
      text-align: center;
      position: relative;
    }

    .back-btn {
      position: absolute;
      top: 30px;
      left: 30px;
      background: none;
      border: none;
      cursor: pointer;
      color: var(--text-dark);
      padding: 5px;
      display: flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      transition: transform 0.2s ease;
    }

    .back-btn:hover {
      transform: translateX(-3px);
    }

    .brand-title {
      color: #00C2CB;
      font-size: 28px;
      font-weight: 700;
      margin: 0 0 5px 0;
    }

    .subtitle {
      color: var(--text-muted);
      font-size: 18px;
      margin: 0 0 30px 0;
    }

    .signup-form {
      text-align: left;
    }

    .form-group {
      margin-bottom: 20px;
    }

    /* Animation class to smoothly show the field */
    .form-group.conditional-field {
      display: none;
      opacity: 0;
      transform: translateY(-10px);
      transition: opacity 0.3s ease, transform 0.3s ease;
    }

    .form-group.conditional-field.show {
      display: block;
      opacity: 1;
      transform: translateY(0);
    }

    .form-group label {
      display: block;
      font-size: 14px;
      color: var(--text-dark);
      margin-bottom: 8px;
      font-weight: 500;
    }

    .input-wrapper, .select-wrapper {
      position: relative;
      display: flex;
      align-items: center;
    }

    .input-wrapper input, .select-wrapper select {
      width: 100%;
      padding: 14px 45px 14px 20px;
      border: 1px solid var(--border-color);
      border-radius: 8px;
      font-size: 14px;
      outline: none;
      box-sizing: border-box;
      color: var(--text-dark);
      background-color: #fff;
    }

    .select-wrapper select {
      padding: 14px 20px;
      appearance: none;
      cursor: pointer;
    }

    .select-wrapper::after {
      content: "▼";
      font-size: 10px;
      color: var(--text-muted);
      position: absolute;
      right: 20px;
      pointer-events: none;
    }

    .input-wrapper input::placeholder {
      color: #94a3b8;
    }

    .toggle-password {
      position: absolute;
      right: 15px;
      background: none;
      border: none;
      cursor: pointer;
      color: #94a3b8;
      font-size: 16px;
      padding: 0;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .submit-btn {
      width: 100%;
      background-color: var(--primary-color);
      color: white;
      border: none;
      padding: 14px;
      border-radius: 8px;
      font-size: 16px;
      font-weight: 700;
      cursor: pointer;
      margin-top: 15px;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 8px;
      transition: opacity 0.2s ease;
    }

    .submit-btn:hover {
      opacity: 0.9;
    }

    .footer-text {
      font-size: 16px;
      color: var(--text-muted);
      margin-top: 25px;
    }

    .footer-text a {
      color: var(--primary-color);
      text-decoration: none;
      font-weight: 600;
    }

    .footer-text a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="signup-container">
    <a href="login.php" class="back-btn" aria-label="Go back to login">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <line x1="19" y1="12" x2="5" y2="12"></line>
        <polyline points="12 19 5 12 12 5"></polyline>
      </svg>
    </a>

    <h1 class="brand-title">ARN QuickFix Ltd.</h1>
    <p class="subtitle">Create Account</p>

   <!-- Update your form tag to look exactly like this -->
<form class="signup-form" action="signup.php" method="POST">

      <div class="form-group">
        <label for="role">Register As</label>
        <div class="select-wrapper">
          <!-- Added id="role" and onchange function to watch selections -->
          <select id="role" name="role" onchange="checkRole()" required>
            <option value="" disabled selected hidden>Select role</option>
            <option value="client">Client</option>
            <option value="technician">Technician</option>
            <option value="manager">Manager</option>
          </select>
        </div>
      </div>

      <!-- New Conditional Specialization Dropdown Field -->
      <div class="form-group conditional-field" id="specialization-group">
        <label for="specialization">Select Specialized Field</label>
        <div class="select-wrapper">
          <select id="specialization"  name="specialization">
            <option value="" disabled selected hidden>Select Specialization</option>
            <option value="Elevator">Elevator</option>
            <option value="AC">AC</option>
            <option value="Generator">Generator</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label for="first-name">First Name</label>
        <div class="input-wrapper">
         <input type="text" id="first-name" name="first_name" placeholder="Your first name" required>

        </div>
      </div>

      <div class="form-group">
        <label for="second-name">Second Name</label>
        <div class="input-wrapper">
          <input type="text" id="second-name" name="second_name" placeholder="Your second name" required>

        </div>
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <div class="input-wrapper">
          <input type="email" id="email" name="email" placeholder="you@example.com" required>

        </div>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <div class="input-wrapper">
          <input type="password" id="password" name="password" placeholder="Create password" required>

          <button type="button" class="toggle-password" onclick="togglePass('password')">👁</button>
        </div>
      </div>

      <div class="form-group">
        <label for="confirm-password">Confirm Password</label>
        <div class="input-wrapper">
          <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm password" required>

          <button type="button" class="toggle-password" onclick="togglePass('confirm-password')">👁</button>
        </div>
      </div>

      <button type="submit" class="submit-btn">Sign Up</button>
    </form>

    <p class="footer-text">
      Already have an account? <a href="login.php">Login</a>
    </p>
  </div>

  <script>
    // Toggles password visibility
    function togglePass(id) {
      const input = document.getElementById(id);
      if (input.type === "password") {
        input.type = "text";
      } else {
        input.type = "password";
      }
    }

    // Controls showing/hiding the Specialization field dynamically
    function checkRole() {
      const roleSelect = document.getElementById('role');
      const specGroup = document.getElementById('specialization-group');
      const specSelect = document.getElementById('specialization');

      if (roleSelect.value === 'technician') {
        specGroup.classList.add('show');
        specSelect.setAttribute('required', 'required'); // Requires field if technician
      } else {
        specGroup.classList.remove('show');
        specSelect.removeAttribute('required'); // Removes requirement if other roles
        specSelect.value = ""; // Resets field value
      }
    }
  </script>

</body>
</html>
