<?php
// Include the common session configuration
require_once __DIR__ . '/session_config.php';
require_once __DIR__ . '/includes/db_connect.php';

// Check if user is already logged in (customer or admin)
if(isset($_SESSION['user_id']) || isset($_SESSION['admin_id'])) {
    // Redirect based on type
    header("Location: homepage.php");
    exit();
}

$error = '';

// Process login form if submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if(empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        // Check the customer table
        try {
            $stmt = $pdo->prepare("SELECT customer_id, customer_email, customer_password, customer_name FROM customer WHERE customer_email = ?");
            $stmt->execute([$email]);
            $customer = $stmt->fetch();

            if ($customer && password_verify($password, $customer['customer_password'])) {
                // Customer Login Successful
                session_regenerate_id(true); // Prevent session fixation
                
                // These are the critical session variables needed by header.php
                $_SESSION['user_id'] = $customer['customer_id'];
                $_SESSION['user_type'] = 'customer';  // This is checked in header.php
                $_SESSION['user_email'] = $customer['customer_email'];
                $_SESSION['user_name'] = $customer['customer_name'];

                // Debug - remove in production
                error_log("Login successful - User ID: " . $_SESSION['user_id']);
                
                header("Location: homepage.php");
                exit();
            }
        } catch (PDOException $e) {
            $error = "An error occurred during login. Please try again.";
            error_log("Customer login error: " . $e->getMessage());
            goto display_form;
        }

        // If not found or password wrong in customer, check the admin table
        try {
            $stmt = $pdo->prepare("SELECT admin_id, admin_email, admin_password, admin_name FROM admin WHERE admin_email = ?");
            $stmt->execute([$email]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['admin_password'])) {
                // Admin Login Successful
                session_regenerate_id(true);
                
                // These are the critical session variables needed by header.php
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['user_type'] = 'admin';
                $_SESSION['user_email'] = $admin['admin_email'];
                $_SESSION['user_name'] = $admin['admin_name'];
                
                // Debug - remove in production
                error_log("Login successful - Admin ID: " . $_SESSION['admin_id']);
                
                header("Location: admin/dashboard.php");
                exit();
            }
        } catch (PDOException $e) {
            if (empty($error)) {
                $error = "An error occurred during login. Please try again.";
            }
            error_log("Admin login error: " . $e->getMessage());
            goto display_form;
        }

        // If not found in either or password wrong
        if (empty($error)) {
            $error = "Invalid email or password.";
        }
    }
}

display_form: // Label for goto statement
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | CLS FLOWERS</title>
    <link rel="stylesheet" href="<?php echo $_SESSION['base_url']; ?>css/base.css">
    <link rel="stylesheet" href="<?php echo $_SESSION['base_url']; ?>css/header.css">
    <link rel="stylesheet" href="<?php echo $_SESSION['base_url']; ?>css/footer.css">
    <link rel="stylesheet" href="<?php echo $_SESSION['base_url']; ?>css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Existing styles remain unchanged */
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 10px;
            text-align: center;
        }

        .modal-title {
            margin-bottom: 20px;
            color: #333;
            font-size: 1.5em;
        }

        .modal-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .modal-button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .admin-btn {
            background-color: #dc3545;
            color: white;
        }

        .user-btn {
            background-color: #28a745;
            color: white;
        }

        .modal-button:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #333;
        }
        
        /* Styles for password toggle icon */
        .password-field-wrapper {
            position: relative;
        }
        .password-field-wrapper .password-toggle-icon {
            position: absolute;
            right: 15px;
            left: auto; /* Override default left position */
            top: 50%;
            /* Adjust top alignment considering label height */
            top: calc(50% + 10px); /* Adjust this value as needed */
            transform: translateY(-50%);
            cursor: pointer;
            color: #aaa;
            transition: color 0.2s;
        }
        .password-field-wrapper .password-toggle-icon:hover {
            color: #333;
        }
        /* Align form elements to the right */
        .form-container .login-form {
            text-align: right;
        }
        .form-container .login-form .form-group label {
             text-align: left; /* Keep labels left-aligned if preferred */
             /* Or remove this line if you want labels right-aligned too */
        }
        .form-container .login-form .input-with-icon input {
            text-align: left; /* Keep input text left-aligned */
            padding-left: 40px !important;
            padding-right: 15px !important;
        }
        .form-container .login-form .input-with-icon input[type='password'] {
            padding-right: 55px !important;
        }
        .input-with-icon {
            position: relative;
        }
        .input-with-icon > i:not(.password-toggle-icon) {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            z-index: 2;
        }

        /* Login Button Styles */
        .login-button {
            width: 100%;
            padding: 12px 20px;
            background-color: #ff69b4;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .login-button:hover {
            background-color: #ff1493;
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(255, 105, 180, 0.3);
        }

        .login-button:active {
            transform: translateY(0);
        }

        /* Register link styles */
        .register-link {
            text-align: center;
            margin-top: 15px;
        }

        .register-link a {
            color: #ff69b4;
            text-decoration: none;
            font-weight: 500;
        }

        .register-link a:hover {
            color: #ff1493;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php 
    include __DIR__ . '/includes/header.php'; 
    // Uncomment for debugging
    // debug_session();
    ?>

    <section class="page-banner">
        <div class="banner-content">
            <h1>Login</h1>
        </div>
    </section>

    <section class="login-section">
        <div class="container">
            <div class="form-container">
                <div class="form-header">
                    <h2>Welcome Back</h2>
                    <p>Sign in to your account to continue</p>
                </div>

                <?php if(!empty($error)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>

                <form action="login.php" method="POST" class="login-form">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email" placeholder="Enter your email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-with-icon password-field-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" placeholder="Enter your password" required>
                            <i class="fas fa-eye-slash password-toggle-icon" onclick="togglePasswordVisibility()"></i>
                        </div>
                    </div>

                    <div class="form-options">
                        <a href="#" onclick="openResetModal(); return false;" class="forgot-password">Forgot Password?</a>
                    </div>

                    <button type="submit" class="login-button">Login</button>

                    <div class="register-link">
                        <p>Don't have an account? <a href="register.php">Register Now</a></p>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Password Reset Modal -->
    <div id="resetModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeResetModal()">&times;</span>
            <h3 class="modal-title">Choose Password Reset Option</h3>
            <div class="modal-buttons">
                <button class="modal-button admin-btn" onclick="window.location.href='admin/forgot_admin_password.php'">
                    Admin Reset
                </button>
                <button class="modal-button user-btn" onclick="window.location.href='forgotpassword.php'">
                    User Reset
                </button>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/includes/footer.php'; ?>

    <script>
        // Password visibility toggle
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.password-toggle-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            }
        }

        // Modal functions
        function openResetModal() {
            document.getElementById('resetModal').style.display = 'block';
        }

        function closeResetModal() {
            document.getElementById('resetModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('resetModal');
            if (event.target == modal) {
                closeResetModal();
            }
        }
    </script>
</body>
</html>
