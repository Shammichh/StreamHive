<?php

session_start();

require_once "core/database.php";

header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "error" => "Je moet ingelogd zijn."
    ]);

    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "error" => "Ongeldige request."
    ]);

    exit();
}

$video_id = $_POST["video_id"] ?? null;
$user_id = $_SESSION["user_id"];

if (!$video_id) {

    echo json_encode([
        "error" => "Geen video ID ontvangen."
    ]);

    exit();
}

$pdo = getPDO();

$stmt = $pdo->prepare("
    SELECT id
    FROM likes
    WHERE user_id = ?
    AND video_id = ?
");

$stmt->execute([
    $user_id,
    $video_id
]);

$existingLike = $stmt->fetch();

$liked = false;

if ($existingLike) {

    $stmt = $pdo->prepare("
        DELETE
        FROM likes
        WHERE user_id = ?
        AND video_id = ?
    ");

    $stmt->execute([
        $user_id,
        $video_id
    ]);

} else {

    $stmt = $pdo->prepare("
        INSERT INTO likes
        (user_id, video_id)
        VALUES (?, ?)
    ");

    $stmt->execute([
        $user_id,
        $video_id
    ]);

    $liked = true;
}

$countStmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM likes
    WHERE video_id = ?
");

$countStmt->execute([
    $video_id
]);

$likeCount = $countStmt->fetchColumn();

echo json_encode([
    "liked" => $liked,
    "likes" => $likeCount
]);