<?php
if (!defined('SESSION_STARTED')) {
    // Include the session configuration if not already included
    $session_config_path = dirname(__DIR__) . '/session_config.php';
    if (file_exists($session_config_path)) {
        require_once $session_config_path;
        define('SESSION_STARTED', true);
    } else if (session_status() === PHP_SESSION_NONE) {
        // Fallback to just starting the session if config file doesn't exist
        session_start();
        define('SESSION_STARTED', true);
    }
}

// Ensure base_url is available
if (!isset($_SESSION['base_url'])) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $base_path = str_replace('\\', '/', dirname(dirname(__FILE__)));
    $doc_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
    $relative_path = str_replace($doc_root, '', $base_path);
    $base_url = $protocol . $host . $relative_path . '/';
    $base_url = rtrim($base_url, '/') . '/';
    $_SESSION['base_url'] = $base_url;
}

// Extract base_url from session
$base_url = $_SESSION['base_url'];

?>

<header class="main-header">
    <div class="top-header">
        <div class="container">
            <div class="top-header-content">
                <div class="top-header-contact">
                    <span><i class="fas fa-envelope"></i> clsflowers@gmail.com</span>
                    <span><i class="fas fa-phone"></i> +60 12-345 6789</span>
                </div>
                <div class="top-header-social">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="main-header-content">
        <div class="container">
            <div class="header-flex">
                <div class="logo">
                    <a href="<?php echo $base_url; ?>homepage.php">
                        <img src="<?php echo $base_url; ?>img/logo.png" alt="CLS Flowers Logo">
                    </a>
                </div>
                
                <nav class="main-nav">
                    <div class="mobile-menu-toggle">
                        <i class="fas fa-bars"></i>
                    </div>
                    <ul class="nav-list">
                        <li><a href="<?php echo $base_url; ?>homepage.php">Home</a></li>
                        <li><a href="<?php echo $base_url; ?>product.php">Products</a></li>
                        <li><a href="<?php echo $base_url; ?>about.php">About Us</a></li>
                        <li><a href="<?php echo $base_url; ?>contact.php">Contact</a></li>
                    </ul>
                </nav>
                
                <div class="header-actions">
                    <?php
                    // User is logged in
                    if (isset($_SESSION['user_id']) || isset($_SESSION['admin_id'])) {
                        $user_type = $_SESSION['user_type'] ?? 'customer';
                        $user_name = $_SESSION['user_name'] ?? 'User';
                        
                        echo '<div class="user-dropdown">';
                        echo '<a href="#" class="user-dropdown-toggle">';
                        echo '<i class="fas fa-user-circle"></i>';
                        echo '<span>' . htmlspecialchars($user_name) . '</span>';
                        echo '<i class="fas fa-chevron-down"></i>';
                        echo '</a>';
                        echo '<div class="user-dropdown-menu">';
                        
                        if ($user_type === 'admin') {
                            echo '<a href="' . $base_url . 'admin/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>';
                        } else {
                            echo '<a href="' . $base_url . 'userdashboard.php"><i class="fas fa-user"></i> My Account</a>';
                            echo '<a href="' . $base_url . 'orders.php"><i class="fas fa-box"></i> My Orders</a>';
                        }
                        
                        echo '<a href="' . $base_url . 'logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>';
                        echo '</div>';
                        echo '</div>';
                        
                        // Only show cart for customers (not admins)
                        if ($user_type !== 'admin') {
                            echo '<a href="' . $base_url . 'cart.php" class="cart-icon">';
                            echo '<i class="fas fa-shopping-cart"></i>';
                            echo '<span class="cart-count">0</span>';
                            echo '</a>';
                        }
                    } 
                    // User is not logged in
                    else {
                        echo '<a href="' . $base_url . 'login.php" class="login-btn">';
                        echo '<i class="fas fa-user"></i> Login';
                        echo '</a>';
                        echo '<a href="' . $base_url . 'cart.php" class="cart-icon">';
                        echo '<i class="fas fa-shopping-cart"></i>';
                        echo '<span class="cart-count">0</span>';
                        echo '</a>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    // Mobile menu toggle
    document.querySelector('.mobile-menu-toggle').addEventListener('click', function() {
        document.querySelector('.nav-list').classList.toggle('active');
    });
    
    // User dropdown toggle
    const userDropdownToggle = document.querySelector('.user-dropdown-toggle');
    if (userDropdownToggle) {
        userDropdownToggle.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelector('.user-dropdown-menu').classList.toggle('active');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.user-dropdown')) {
                const dropdown = document.querySelector('.user-dropdown-menu');
                if (dropdown && dropdown.classList.contains('active')) {
                    dropdown.classList.remove('active');
                }
            }
        });
    }
</script>
