<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $description = $conn->real_escape_string($_POST['description']);
    
    // Handle profile image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
        $profile_image = 'uploads/' . basename($_FILES['profile_image']['name']);
        move_uploaded_file($_FILES['profile_image']['tmp_name'], $profile_image);
    } else {
        $profile_image = $conn->real_escape_string($_POST['existing_profile_image']);
    }

    // Handle CV upload
    if (isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] == UPLOAD_ERR_OK) {
        $cv_link = 'uploads/' . basename($_FILES['cv_file']['name']);
        move_uploaded_file($_FILES['cv_file']['tmp_name'], $cv_link);
    } else {
        $cv_link = $conn->real_escape_string($_POST['existing_cv_link']);
    }

    $sql = "UPDATE about_section SET description = '$description', profile_image = '$profile_image', cv_link = '$cv_link' WHERE id = 1";

    if ($conn->query($sql) === TRUE) {
        echo "About section updated successfully!";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

$sql = "SELECT * FROM about_section WHERE id = 1";
$result = $conn->query($sql);
$about_content = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Update About Section</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: #333;
            padding: 2rem;
            background-color: #f9f9f9;
        }
        form {
            max-width: 600px;
            margin: 0 auto;
            padding: 2rem;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        label {
            font-weight: 600;
            margin-top: 1rem;
            display: block;
        }
        textarea, input[type="text"], input[type="file"] {
            width: 100%;
            padding: 0.75rem;
            margin-top: 0.5rem;
        }
        button {
            margin-top: 1.5rem;
            padding: 1rem 2rem;
            background-color: #ff6f61;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #ffa177;
        }
    </style>
</head>
<body>
    <h2>Update About Section</h2>
    <form method="POST" action="" enctype="multipart/form-data">
        <label for="profile_image">Profile Image:</label>
        <input type="file" name="profile_image" id="profile_image">
        <input type="hidden" name="existing_profile_image" value="<?php echo htmlspecialchars($about_content['profile_image']); ?>">

        <label for="description">Description:</label>
        <textarea name="description" id="description"><?php echo htmlspecialchars($about_content['description']); ?></textarea>

        <label for="cv_file">CV File:</label>
        <input type="file" name="cv_file" id="cv_file">
        <input type="hidden" name="existing_cv_link" value="<?php echo htmlspecialchars($about_content['cv_link']); ?>">

        <button type="submit">Update</button>
    </form>
</body>
</html>
