<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $subtitle = $conn->real_escape_string($_POST['subtitle']);
    $description = $conn->real_escape_string($_POST['description']);
    
    // Handle image upload
    if (isset($_FILES['background_image']) && $_FILES['background_image']['error'] == 0) {
        $target_dir = "../uploads/";
        $file_extension = pathinfo($_FILES["background_image"]["name"], PATHINFO_EXTENSION);
        $file_name = uniqid() . "." . $file_extension;
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES["background_image"]["tmp_name"], $target_file)) {
            $image_path = "uploads/" . $file_name;
            $sql = "UPDATE home_content SET background_image = '$image_path' WHERE id = 1";
            $conn->query($sql);
        }
    }
    
    $sql = "UPDATE home_content SET title = '$title', subtitle = '$subtitle', description = '$description' WHERE id = 1";
    $conn->query($sql);
    $success = "Content updated successfully!";
}

$sql = "SELECT * FROM home_content WHERE id = 1";
$result = $conn->query($sql);
$content = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="dashboard-container">
        <nav class="admin-nav">
            <h2>Admin Dashboard</h2>
            <a href="logout.php">Logout</a>
        </nav>
        
        <div class="content-section">
            <h3>Edit Home Content</h3>
            <?php if (isset($success)): ?>
                <div class="success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" value="<?php echo $content['title']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Subtitle</label>
                    <input type="text" name="subtitle" value="<?php echo $content['subtitle']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" required><?php echo $content['description']; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Background Image</label>
                    <input type="file" name="background_image" accept="image/*">
                    <?php if ($content['background_image']): ?>
                        <img src="../<?php echo $content['background_image']; ?>" alt="Current background" style="max-width: 200px;">
                    <?php endif; ?>
                </div>
                
                <button type="submit">Update Content</button>
            </form>
        </div>
    </div>
</body>
</html>