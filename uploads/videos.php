<?php

session_start();
require_once __DIR__ . "/../core/database.php";

$pdo = getPDO();

$stmt = $pdo->query("
    SELECT *
    FROM videos
    ORDER BY created_at DESC
");

$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Videos</title>
</head>
<body>

<h1>All Videos</h1>

<?php foreach($videos as $video): ?>

<?php

$likeStmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM likes
    WHERE video_id = ?
");

$likeStmt->execute([$video["id"]]);

$likes = $likeStmt->fetchColumn();

$commentStmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM comments
    WHERE video_id = ?
");

$commentStmt->execute([$video["id"]]);

$comments = $commentStmt->fetchColumn();

?>

<div style="margin-bottom:40px; border:1px solid #ccc; padding:20px;">

    <h2>
        <?= htmlspecialchars($video["title"]) ?>
    </h2>

    <video width="600" controls>
        <source
            src="../uploads/<?= htmlspecialchars($video["filename"]) ?>"
            type="video/mp4">
    </video>

    <p>
        👍 <?= $likes ?>
        |
        💬 <?= $comments ?>
    </p>

    <a href="../watch.php?id=<?= $video["id"] ?>">
        Watch Video
    </a>

</div>

<?php endforeach; ?>

</body>
</html>