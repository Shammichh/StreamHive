<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Videos</title>
</head>
<body>
    
    <?php
    require_once "../core/database.php";

    $pdo = getPDO();

    $stmt = $pdo->query("
        SELECT *
        FROM videos
        ORDER BY created_at DESC
    ");

    $videos = $stmt->fetchAll();

    foreach ($videos as $video) {
        echo "<div>";
        echo "<h3>" . htmlspecialchars($video["title"]) . "</h3>";
        echo "<p>" . nl2br(htmlspecialchars($video["description"])) . "</p>";
        echo "<video width='600' height='240' controls>";
        echo "<source src='../uploads/" . htmlspecialchars($video["filename"]) . "' type='video/mp4'>";
        echo "Your browser does not support the video tag.";
        echo "</video>";
        echo "</div><hr>";
    }

    $id = $_GET["id"] ?? null;

    $stmt = $pdo->prepare("SELECT * FROM videos WHERE id = ?");
    $stmt->execute([$id]);
    $video = $stmt->fetch();
    if ($video) {
        echo "<h2>" . htmlspecialchars($video["title"]) . "</h2>";
        echo "<p>" . nl2br(htmlspecialchars($video["description"])) . "</p>";
        echo "<video width='600' height='240' controls>";
        echo "<source src='../uploads/" . htmlspecialchars($video["filename"]) . "' type='video/mp4'>";
        echo "Your browser does not support the video tag.";
        echo "</video>";
    } else {
        echo "Video not found.";
    }
    ?>

</body>
</html>