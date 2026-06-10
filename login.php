

<?php
    require_once "core/database.php";
    session_start();
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST["email"];
        $password = $_POST["password"];

        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"] = $user["role"];
            echo "Login successful!";
        } else {
            echo "Invalid email or password.";
        }
    }

    if (isset($_SESSION["user_id"])) {
        if ($_SESSION["role"] === "admin") {
            header("Location: admin/admin.php");
        } else {
            header("Location: user/dashboard.php");
        }
        exit();
    }
    ?>

    
<form method="POST" action="login.php">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required> 
    <button type="submit">Login</button>

    <p>Don't have an account? <a href="register.php">Register here</a>.</p>
</form>         