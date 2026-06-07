<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <?php
    session_start();
    if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
        header("Location: login.php");
        exit();
    }
    echo "Welcome to the Admin Dashboard, " . $_SESSION["username"] . "!";
    ?>
</body>
</html>