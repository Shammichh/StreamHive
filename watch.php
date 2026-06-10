<?php

session_start();
require_once "core/database.php";

$id = $_GET["id"] ?? null;

$pdo = getPDO();

$stmt = $pdo->prepare("
    SELECT *
    FROM videos
    WHERE id = ?
");

$stmt->execute([$id]);

$video = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$video) {
    die("Video not found");
}

$likeStmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM likes
    WHERE video_id = ?
");

$likeStmt->execute([$id]);

$likes = $likeStmt->fetchColumn();

$userLiked = false;

if(isset($_SESSION["user_id"])){

    $checkStmt = $pdo->prepare("
        SELECT id
        FROM likes
        WHERE user_id = ?
        AND video_id = ?
    ");

    $checkStmt->execute([
        $_SESSION["user_id"],
        $id
    ]);

    $userLiked = $checkStmt->fetch();
}

$commentStmt = $pdo->prepare("
    SELECT c.*, u.username
    FROM comments c
    JOIN users u
    ON c.user_id = u.id
    WHERE c.video_id = ?
    ORDER BY c.id DESC
");

$commentStmt->execute([$id]);

$comments = $commentStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
    <title>
        <?= htmlspecialchars($video["title"]) ?>
    </title>
</head>
<body>

<a href="uploads/videos.php">
    ← Back
</a>

<h1>
    <?= htmlspecialchars($video["title"]) ?>
</h1>

<video width="800" controls>
    <source
        src="uploads/<?= htmlspecialchars($video["filename"]) ?>"
        type="video/mp4">
</video>

<p>
    <?= nl2br(htmlspecialchars($video["description"])) ?>
</p>

<p>
    👍 <span id="likes"><?= $likes ?></span>
</p>

<?php if(isset($_SESSION["user_id"])): ?>

<button
    id="likeBtn"
    data-video="<?= $video["id"] ?>">

    <?= $userLiked ? "👎 Unlike" : "👍 Like" ?>

</button>

<?php endif; ?>

<hr>

<h2>Comments</h2>

<?php if(isset($_SESSION["user_id"])): ?>

<form method="POST" action="comments.php">

    <input
        type="hidden"
        name="video_id"
        value="<?= $video["id"] ?>">

    <textarea
        name="content"
        required
        rows="4"
        cols="50"></textarea>

    <br>

    <button type="submit">
        Comment
    </button>

</form>

<?php endif; ?>

<hr>

<?php foreach($comments as $comment): ?>

<div style="margin-bottom:20px;">

    <strong>
        <?= htmlspecialchars($comment["username"]) ?>
    </strong>

    <p>
        <?= nl2br(htmlspecialchars($comment["content"])) ?>
    </p>

</div>

<?php endforeach; ?>

<script>

const btn = document.getElementById("likeBtn");

if(btn){

btn.addEventListener("click", () => {

    const videoId = btn.dataset.video;

    fetch("likes.php", {

        method: "POST",

        headers: {
            "Content-Type":
            "application/x-www-form-urlencoded"
        },

        body:
        "video_id=" + videoId

    })

    .then(res => res.json())

    .then(data => {

        document.getElementById("likes")
        .innerText = data.likes;

        btn.innerText =
            data.liked
            ? "👎 Unlike"
            : "👍 Like";

    });

});

}

</script>

</body>
</html>
