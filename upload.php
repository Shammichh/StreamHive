<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Video</title>
</head>
<body>
    <?php
    require_once "core/database.php";
    session_start();
    $title = $_POST["title"] ?? "";
    $description = $_POST["description"] ?? "";


    $allowed_types = ["video/mp4", "video/webm", "video/ogg"];
    if (isset($_FILES["video"]) && in_array($_FILES["video"]["type"], $allowed_types)) {
        $upload_dir = "uploads/";
        $filename = basename($_FILES["video"]["name"]);
        $target_file = $upload_dir . $filename;

        if (move_uploaded_file($_FILES["video"]["tmp_name"], $target_file)) {
            echo "Video uploaded successfully!";
        } else {
            echo "Error uploading video.";
        }
    } else {
        echo "Invalid video format. Allowed formats: MP4, WebM, OGG.";
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_SESSION["user_id"])) {
            $pdo = getPDO();
            $stmt = $pdo->prepare("INSERT INTO videos (title, description, filename, user_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $description, $filename, $_SESSION["user_id"]]);
            echo "Video uploaded successfully!";
        } else {
            echo "You must be logged in to upload a video.";
        }
    }
    ?>

<form method="POST" action="upload.php" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="Video Title" required>
    <textarea name="description" placeholder="Video Description"></textarea>
    <input type="file" name="video" accept="video/*" required>
    <button type="submit">Upload Video</button>
</form>
</body>
</html>