<?php

session_start();
require_once "core/database.php";

if(!isset($_SESSION["user_id"])){
    header("Location: login.php");
    exit();
}

if($_SERVER["REQUEST_METHOD"] === "POST"){

    $content = trim($_POST["content"] ?? "");
    $video_id = $_POST["video_id"];
    $user_id = $_SESSION["user_id"];

    $pdo = getPDO();

    $stmt = $pdo->prepare("
        INSERT INTO comments
        (user_id, video_id, content)
        VALUES (?, ?, ?)
    ");

    $stmt->execute([
        $user_id,
        $video_id,
        $content
    ]);

    header("Location: watch.php?id=" . $video_id);
    exit();
}

?>