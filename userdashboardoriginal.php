<?php
session_start();
require_once __DIR__ . '/includes/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Get user's reviews
$stmt = $pdo->prepare("
    SELECT pr.*, p.product_name, rr.reply_text, rr.reply_date, a.admin_name 
    FROM productreview pr 
    JOIN product p ON pr.product_id = p.product_id 
    LEFT JOIN review_replies rr ON pr.review_id = rr.review_id 
    LEFT JOIN admin a ON rr.admin_id = a.admin_id 
    WHERE pr.customer_id = ? 
    ORDER BY pr.review_date DESC
");
$stmt->execute([$user_id]);
$user_reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all products for the review form
$stmt = $pdo->query("SELECT product_id, product_name FROM product ORDER BY product_name");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle new review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $rating = $_POST['rating'];
    $comment = trim($_POST['review_text']);
    $product_id = $_POST['product_id'];
    
    if ($comment !== '') {
        $review_id = uniqid('REV_');
        $stmt = $pdo->prepare("INSERT INTO productreview (review_id, product_id, customer_id, rating, comment, review_date) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$review_id, $product_id, $user_id, $rating, $comment]);
        header('Location: userdashboard.php?tab=reviews&success=1');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard | CLS FLOWERS</title>
    <link rel="stylesheet" href="<?php echo isset($base_url) ? $base_url : ''; ?>css/base.css">
    <link rel="stylesheet" href="<?php echo isset($base_url) ? $base_url : ''; ?>css/header.css">
    <link rel="stylesheet" href="<?php echo isset($base_url) ? $base_url : ''; ?>css/footer.css">
    <link rel="stylesheet" href="<?php echo isset($base_url) ? $base_url : ''; ?>css/customerreview.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .dashboard-container {
            max-width: 800px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            padding: 32px 24px 24px 24px;
        }
        .dashboard-header {
            text-align: center;
            margin-bottom: 32px;
        }
        .dashboard-header h1 {
            font-size: 2rem;
            margin-bottom: 8px;
            color: #aa336a;
        }
        .dashboard-menu {
            display: flex;
            flex-wrap: wrap;
            gap: 24px;
            justify-content: center;
        }
        .dashboard-card {
            background: #aa336a;
            border-radius: 10px;
            box-shadow: 0 1px 4px rgba(170,51,106,0.07);
            padding: 28px 32px;
            min-width: 220px;
            text-align: center;
            transition: box-shadow 0.2s, transform 0.2s, background 0.2s, color 0.2s;
            text-decoration: none;
            color: #fff;
            font-weight: 500;
            font-size: 1.08rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .dashboard-card i {
            font-size: 2rem;
            margin-bottom: 12px;
            color: #fff;
        }
        .dashboard-card:hover {
            box-shadow: 0 4px 16px rgba(170,51,106,0.13);
            background: #c94a9c;
            color: #fff;
            transform: translateY(-3px) scale(1.03);
        }
        .dashboard-card:hover i {
            color: #fff;
        }
        @media (max-width: 600px) {
            .dashboard-menu {
                flex-direction: column;
                gap: 16px;
            }
            .dashboard-card {
                min-width: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h1>
            <p>Your personal dashboard</p>
        </div>
        <div class="dashboard-menu">
            <a href="profile.php" class="dashboard-card">
                <i class="fas fa-user"></i>
                View Profile
            </a>

            <a href="paymentpending.php" class="dashboard-card">
                <i class="fas fa-credit-card"></i>
                View Payment Status
            </a>

            <a href="orderhistory.php" class="dashboard-card">
                <i class="fas fa-history"></i>
                View Order History
            </a>

            <a href="delivery.php" class="dashboard-card">
                <i class="fas fa-truck"></i>
                View Delivery/Pickup Status
            </a>

            <a href="customerservice.php" class="dashboard-card">
                <i class="fas fa-comments"></i>
                Chat with Customer Service
            </a>

            <a href="customerreview.php" class="dashboard-card">
                <i class="fas fa-star"></i>
                Product Reviews
            </a>
        </div>
    </div>
    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html> 
