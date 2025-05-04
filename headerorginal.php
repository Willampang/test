<?php 
// includes/header.php

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Require database connection and cart functions
require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/cart_functions.php';

// Get cart quantity if user is logged in as customer
$cart_quantity = 0;
if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'customer') {
    $cart_quantity = getCartTotalQuantity($_SESSION['user_id']);
}

// Dynamically determine Base URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];

// Get the directory path up to the document root
$script_path = dirname($_SERVER['SCRIPT_NAME']);
$base_path = str_replace('\\', '/', dirname(dirname(__FILE__)));
$doc_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
$relative_path = str_replace($doc_root, '', $base_path);
$base_url = $protocol . $host . $relative_path . '/';

// Clean up the URL
$base_url = str_replace('/includes', '', $base_url);
$base_url = rtrim($base_url, '/') . '/';

// Store base URL in session for consistency
$_SESSION['base_url'] = $base_url;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLS FLOWERS</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>css/base.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>css/header.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="announcement-bar">
        WELCOME TO CLS FLOWERS SHOP.
    </div>
    <div style="background-color: #f8f9fa; padding: 10px; margin: 10px; border: 1px solid #ccc;">
    <strong>Debug Session:</strong><br>
    User ID: <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Not set'; ?><br>
    User Type: <?php echo isset($_SESSION['user_type']) ? $_SESSION['user_type'] : 'Not set'; ?><br>
    User Name: <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Not set'; ?><br>
    Base URL: <?php echo $base_url; ?><br>
    Dashboard URL: <?php echo $base_url . 'userdashboard.php'; ?><br>
    <strong>Is User Dashboard Link Created?</strong> 
    <?php echo (isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])) ? 'Yes' : 'No'; ?>
</div>
    <header>
        <div class="header-container">
            <div class="logo">
                <?php if (isset($_SESSION['admin_id'])): ?>
                    <a href="<?php echo $base_url; ?>admin/dashboard.php">
                        <img src="<?php echo $base_url; ?>img/clslogo.png" alt="CLS FLOWERS Logo">
                    </a>
                <?php else: ?>
                    <a href="<?php echo $base_url; ?>homepage.php">
                        <img src="<?php echo $base_url; ?>img/clslogo.png" alt="CLS FLOWERS Logo">
                    </a>
                <?php endif; ?>
            </div>

            <nav>
                <ul class="nav-menu">
                    <?php if (isset($_SESSION['admin_id'])): ?>
                        <li><a href="<?php echo $base_url; ?>homepage.php">Home</a></li>
                        <li><a href="<?php echo $base_url; ?>product.php">Products</a></li>
                        <li><a href="<?php echo $base_url; ?>aboutus.php">About Us</a></li>
                        <li><a href="<?php echo $base_url; ?>customerreview.php">Review</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo $base_url; ?>homepage.php">Home</a></li>
                        <li><a href="<?php echo $base_url; ?>product.php">Products</a></li>
                        <li><a href="<?php echo $base_url; ?>aboutus.php">About Us</a></li>
                        <li><a href="<?php echo $base_url; ?>customerreview.php">Customer Review</a></li>
                    <?php endif; ?>
                </ul>
            </nav>

            <div class="header-actions">
            <?php if (isset($_SESSION['user_id']) || isset($_SESSION['admin_id'])): ?>
    <div class="account-dropdown">
        <div class="account-icon" onclick="toggleDropdown(event)">
            <?php if (isset($_SESSION['admin_id'])): ?>
                <i class="fas fa-user-shield"></i>
            <?php else: ?>
                <i class="fas fa-user"></i>
            <?php endif; ?>
        </div>
        <div class="dropdown-content" id="accountDropdown">
            <div class="simple-dropdown">
                <div class="user-info">
                    <?php if (isset($_SESSION['admin_id'])): ?>
                        <h3>Admin: <?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></h3>
                        <p><?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?></p>
                    <?php else: ?>
                        <h3><?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></h3>
                        <p><?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?></p>
                    <?php endif; ?>
                </div>
                <hr class="dropdown-divider">
                <div class="profile-buttons">
                    <?php if (isset($_SESSION['admin_id'])): ?>
                        <a href="<?php echo $base_url; ?>admin/dashboard.php" class="dropdown-link dashboard profile-btn">
                            <i class="fas fa-tachometer-alt"></i> Admin Dashboard
                        </a>
                    <?php else: ?>
                        <a href="<?php echo $base_url; ?>userdashboard.php" class="dropdown-link profile-btn">
                            <i class="fas fa-tachometer-alt"></i> User Dashboard
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo $base_url; ?>logout.php" class="dropdown-link logout profile-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
                    <div class="account-dropdown">
                        <div class="account-icon" onclick="toggleDropdown(event)">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="dropdown-content" id="accountDropdown">
                            <div class="simple-dropdown">
                                <div class="profile-buttons">
                                    <a href="<?php echo $base_url; ?>login.php" class="dropdown-link profile-btn">
                                        <i class="fas fa-sign-in-alt"></i> Login
                                    </a>
                                    <a href="<?php echo $base_url; ?>register.php" class="dropdown-link profile-btn">
                                        <i class="fas fa-user-plus"></i> Register
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (!isset($_SESSION['admin_id'])): ?>
                    <a href="<?php echo $base_url; ?>cart.php" class="cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                        <?php if ($cart_quantity > 0): ?>
                            <span class="cart-count"><?php echo $cart_quantity; ?></span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Add Login Modal -->
    <div id="loginModal" class="login-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Login Required</h2>
                <span class="close-modal">&times;</span>
            </div>
            <div class="modal-body">
                <p>You need to login to view your cart.</p>
                <button onclick="window.location.href='<?php echo $base_url; ?>login.php'" class="modal-login-btn">Login Now</button>
            </div>
        </div>
    </div>

    <style>
    /* Modal Styles */
    .login-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        animation: fadeIn 0.3s;
    }

    .modal-content {
        background-color: #fff;
        margin: 15% auto;
        padding: 20px;
        border-radius: 8px;
        width: 400px;
        max-width: 90%;
        position: relative;
        animation: slideIn 0.3s;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .modal-header h2 {
        margin: 0;
        color: #333;
        font-size: 1.5rem;
    }

    .close-modal {
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        transition: color 0.3s;
    }

    .close-modal:hover {
        color: #333;
    }

    .modal-body {
        text-align: center;
    }

    .modal-body p {
        margin-bottom: 20px;
        color: #666;
        font-size: 1.1rem;
    }

    .modal-login-btn {
        background-color: #007bff;
        color: white;
        padding: 10px 25px;
        border: none;
        border-radius: 5px;
        font-size: 1rem;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .modal-login-btn:hover {
        background-color: #0056b3;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideIn {
        from { transform: translateY(-100px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    /* Update header actions styles */
    .header-actions {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .auth-icon {
        color: #333;
        font-size: 1.2rem;
        padding: 8px;
        border-radius: 50%;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        background-color: #f8f9fa;
        width: 35px;
        height: 35px;
    }

    .auth-icon:hover {
        background-color: #aa336a;
        color: white;
        transform: translateY(-2px);
    }

    .cart-icon {
        text-decoration: none;
        color: #333;
        position: relative;
        padding: 8px;
        font-size: 1.2rem;
        background-color: #f8f9fa;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .cart-icon:hover {
        background-color: #aa336a;
        color: white;
        transform: translateY(-2px);
    }

    .cart-count {
        position: absolute;
        top: -5px;
        right: -5px;
        background-color: #aa336a;
        color: white;
        border-radius: 50%;
        padding: 2px 6px;
        font-size: 12px;
        min-width: 15px;
        text-align: center;
    }

    .account-dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown-content {
        position: absolute;
        top: calc(100% + 5px);
        right: 0;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        min-width: 250px;
        z-index: 1000;
        display: none;
        overflow: hidden;
    }

    .dropdown-content.show {
        display: block;
    }

    .simple-dropdown {
        padding: 15px;
    }

    .simple-dropdown .user-info {
        padding-bottom: 12px;
        border-bottom: 1px solid #eee;
        margin-bottom: 12px;
    }

    .simple-dropdown .user-info h3 {
        color: #333;
        font-size: 16px;
        margin: 0 0 4px 0;
        font-weight: 500;
    }

    .simple-dropdown .user-info p {
        color: #666;
        font-size: 13px;
        margin: 0;
    }

    .dropdown-link {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        color: #333;
        text-decoration: none;
        font-size: 14px;
        border-radius: 4px;
        margin: 2px 0;
        transition: all 0.2s ease;
    }

    .dropdown-link i {
        margin-right: 8px;
        width: 16px;
        text-align: center;
    }

    .dropdown-link.dashboard {
        color: #17a2b8;
    }

    .dropdown-link.dashboard:hover {
        background-color: #e3f6f9;
    }

    .dropdown-link.logout {
        color: #dc3545;
    }

    .dropdown-link.logout:hover {
        background-color: #fbe7e9;
    }

    .account-icon {
        cursor: pointer;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        border-radius: 50%;
        transition: all 0.2s ease;
    }

    .account-icon:hover {
        background-color: #aa336a;
        color: white;
    }

    .account-icon i {
        font-size: 1.2rem;
    }

    /* Add styles for user greeting and email in dropdown */
    .dropdown-content .user-greeting {
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
        text-align: center;
    }

    /* Add admin badge style */
    .admin-badge {
        background-color: #aa336a;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        text-align: center;
        margin: 10px auto;
        display: inline-block;
        position: relative;
        left: 50%;
        transform: translateX(-50%);
    }

    .dropdown-content .user-email-display {
        font-size: 0.9rem;
        color: #777;
        margin-bottom: 15px;
        text-align: center;
        word-break: break-all;
    }

    .dropdown-content .profile-buttons {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .dropdown-content .profile-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 15px;
        border-radius: 6px;
        text-decoration: none;
        color: #495057 !important;
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
        font-size: 0.95rem;
        opacity: 1;
        visibility: visible;
    }

    .dropdown-content .profile-btn i {
        width: 16px;
        text-align: center;
        color: #007bff;
    }

    /* Specific style for Admin Dashboard link */
    .dropdown-content .profile-btn[href*="admin/dashboard.php"] {
        color: #17a2b8 !important;
    }

    .dropdown-content .profile-btn[href*="admin/dashboard.php"] i {
        color: #17a2b8;
    }

    .dropdown-content .profile-btn:hover {
        background-color: #e9ecef;
        color: #333 !important;
        border-color: #ced4da;
    }
    .dropdown-content .profile-btn:hover i {
        color: #0056b3;
    }

    .dropdown-content .profile-btn.logout {
        color: #dc3545 !important;
        opacity: 1;
        visibility: visible;
    }

    .dropdown-content .profile-btn.logout i {
        color: #dc3545;
    }

    .dropdown-content .profile-btn.logout:hover {
        background-color: #f8d7da;
        color: #721c24 !important;
        border-color: #f5c6cb;
    }
    .dropdown-content .profile-btn.logout:hover i {
         color: #721c24;
    }

    /* Force Admin Dashboard button visibility */
    .dropdown-content .profile-buttons a[href*="admin/dashboard.php"] {
        display: flex !important;      /* Ensure it's displayed as a flex container */
        visibility: visible !important; /* Ensure it's visible */
        opacity: 1 !important;         /* Ensure it's fully opaque */
    }

    /* Style for admin icon */
    .account-icon .fa-user-shield {
        color: #aa336a;
    }

    /* Admin Dashboard button style */
    .profile-btn.admin-dashboard-btn {
        background-color: #aa336a !important;
        color: white !important;
        border: none !important;
        font-weight: 500;
        position: relative;
        overflow: hidden;
    }

    .profile-btn.admin-dashboard-btn i {
        color: white !important;
    }

    .profile-btn.admin-dashboard-btn:hover {
        background-color: #8a2755 !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(170, 51, 106, 0.2);
    }

    /* Add these styles to your header CSS */
    .search-container {
        position: relative;
        margin-right: 15px;
    }

    .search-box {
        display: flex;
        align-items: center;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 20px;
        padding: 5px 10px;
    }

    .search-box input {
        border: none;
        outline: none;
        padding: 5px;
        width: 200px;
        font-size: 14px;
    }

    .search-box button {
        background: none;
        border: none;
        color: #ff69b4;
        cursor: pointer;
        padding: 5px;
    }

    .search-box button:hover {
        color: #ff1493;
    }

    .search-results {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        width: 300px;
        max-height: 400px;
        overflow-y: auto;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        z-index: 1000;
    }

    .search-result-item {
        display: flex;
        align-items: center;
        padding: 10px;
        border-bottom: 1px solid #eee;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .search-result-item:hover {
        background-color: #f8f9fa;
    }

    .search-result-item img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        margin-right: 10px;
        border-radius: 4px;
    }

    .search-result-info {
        flex: 1;
    }

    .search-result-info h4 {
        margin: 0 0 5px 0;
        color: #333;
        font-size: 14px;
    }

    .search-result-info p {
        margin: 0;
        color: #ff69b4;
        font-weight: 500;
        font-size: 13px;
    }

    .search-result-info .product-category {
        display: block;
        color: #666;
        font-size: 12px;
        margin-top: 3px;
        font-style: italic;
    }

    .no-results {
        padding: 15px;
        text-align: center;
        color: #666;
    }

    .search-loading {
        text-align: center;
        padding: 20px;
        color: #666;
    }

    .search-error {
        padding: 15px;
        text-align: center;
        color: #dc3545;
        background-color: #f8d7da;
        border-radius: 4px;
        margin: 10px;
    }
    </style>

<script>
    // Toggle dropdown visibility
    function toggleDropdown(event) {
        event.stopPropagation(); // Prevent the click from bubbling up
        const dropdown = event.currentTarget.nextElementSibling;
        const allDropdowns = document.querySelectorAll('.dropdown-content');

        // Hide all dropdowns except the one clicked
        allDropdowns.forEach(d => {
            if (d !== dropdown) d.classList.remove('show');
        });

        // Toggle this dropdown
        dropdown.classList.toggle('show');
    }

    // Hide dropdown when clicking outside
    document.addEventListener('click', function () {
        const dropdowns = document.querySelectorAll('.dropdown-content');
        dropdowns.forEach(d => d.classList.remove('show'));
    });

    // Prevent closing dropdown when clicking inside
    document.querySelectorAll('.dropdown-content').forEach(dropdown => {
        dropdown.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    });

    // Optional: Handle modal close button
    document.querySelectorAll('.close-modal').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('loginModal').style.display = 'none';
        });
    });
</script>

</body>

</html>
