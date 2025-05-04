<?php
session_start();

if(isset($_POST['confirm_logout'])) {
    session_destroy();
    header("Location: homepage.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout | CLS FLOWERS</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/logout.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="logout-section">
        <div class="logout-icon">
            <i class="fas fa-sign-out-alt"></i>
        </div>
        <h1>Logout Confirmation</h1>
        <p class="logout-message">Are you sure you want to logout from your account?</p>
        
        <div class="logout-buttons">
            <form action="logout.php" method="POST">
                <button type="submit" name="confirm_logout" class="btn btn-primary">Yes, Logout</button>
            </form>
            <a href="homepage.php" class="btn btn-outline">No, Take Me Back</a>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html> 
