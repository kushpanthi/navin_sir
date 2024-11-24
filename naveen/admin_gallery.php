<?php
require_once 'config.php';

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload'])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["galleryImage"]["name"]);

    if (move_uploaded_file($_FILES["galleryImage"]["tmp_name"], $target_file)) {
        $image_url = $target_file;
        $caption = $_POST['caption'];

        $sql = "INSERT INTO gallery_images (image_url, caption) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $image_url, $caption);

        if ($stmt->execute()) {
            echo "Image uploaded successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Error uploading file.";
    }
}

// Handle image delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "SELECT image_url FROM gallery_images WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($image_url);
    $stmt->fetch();
    $stmt->close();

    if (file_exists($image_url)) {
        unlink($image_url);
    }

    $delete_sql = "DELETE FROM gallery_images WHERE id=?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $id);
    if ($delete_stmt->execute()) {
        echo "Image deleted successfully!";
    } else {
        echo "Error deleting image.";
    }
}

// Handle image edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    $id = $_POST['id'];
    $caption = $_POST['caption'];

    // If new image is uploaded
    if (!empty($_FILES['editImage']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["editImage"]["name"]);

        if (move_uploaded_file($_FILES["editImage"]["tmp_name"], $target_file)) {
            $image_url = $target_file;

            $edit_sql = "UPDATE gallery_images SET image_url=?, caption=? WHERE id=?";
            $edit_stmt = $conn->prepare($edit_sql);
            $edit_stmt->bind_param("ssi", $image_url, $caption, $id);
        } else {
            echo "Error uploading file.";
        }
    } else {
        // If only the caption is updated
        $edit_sql = "UPDATE gallery_images SET caption=? WHERE id=?";
        $edit_stmt = $conn->prepare($edit_sql);
        $edit_stmt->bind_param("si", $caption, $id);
    }

    if ($edit_stmt->execute()) {
        echo "Caption updated successfully!";
    } else {
        echo "Error updating caption.";
    }
}

// Handle swapping images
if (isset($_GET['move'])) {
    $id = $_GET['id'];
    $direction = $_GET['move'];

    $current_sql = "SELECT id, ordering FROM gallery_images WHERE id=?";
    $current_stmt = $conn->prepare($current_sql);
    $current_stmt->bind_param("i", $id);
    $current_stmt->execute();
    $current_result = $current_stmt->get_result();
    $current = $current_result->fetch_assoc();

    $ordering = $current['ordering'];

    if ($direction == 'up') {
        $swap_sql = "SELECT id, ordering FROM gallery_images WHERE ordering < ? ORDER BY ordering DESC LIMIT 1";
    } else {
        $swap_sql = "SELECT id, ordering FROM gallery_images WHERE ordering > ? ORDER BY ordering ASC LIMIT 1";
    }

    $swap_stmt = $conn->prepare($swap_sql);
    $swap_stmt->bind_param("i", $ordering);
    $swap_stmt->execute();
    $swap_result = $swap_stmt->get_result();
    $swap = $swap_result->fetch_assoc();

    if ($swap) {
        $swap_id = $swap['id'];
        $swap_ordering = $swap['ordering'];

        // Swap the ordering
        $update_current = "UPDATE gallery_images SET ordering=? WHERE id=?";
        $update_swap = "UPDATE gallery_images SET ordering=? WHERE id=?";

        $update_stmt1 = $conn->prepare($update_current);
        $update_stmt1->bind_param("ii", $swap_ordering, $id);
        $update_stmt1->execute();

        $update_stmt2 = $conn->prepare($update_swap);
        $update_stmt2->bind_param("ii", $ordering, $swap_id);
        $update_stmt2->execute();
    }
}

// Fetch all gallery images
$gallery_sql = "SELECT * FROM gallery_images ORDER BY ordering ASC";
$gallery_result = $conn->query($gallery_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Gallery Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- <link rel="stylesheet" href="css/admin_style.css"> -->
    <style>
        /* CSS for Admin Gallery Management */
:root {
    --primary-color: #4A90E2;
    --secondary-color: #50E3C2;
    --background-color: #1a1a1a;
    --text-color: #ffffff;
    --input-bg: #2a2a2a;
    --input-text-color: #fff;
    --input-border-color: #4a4a4a;
    --highlight-color: #ff6f61;
    --shadow-color: rgba(0, 0, 0, 0.3);
    --dark-mode-bg: #121212;
    --dark-mode-text: #e0e0e0;
}

body {
    font-family: 'Poppins', sans-serif;
    background-color: var(--background-color);
    color: var(--text-color);
    padding: 2rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    transition: background-color 0.5s ease, color 0.5s ease;
}

h1 {
    margin-bottom: 2rem;
    font-size: 2rem;
    color: var(--primary-color);
    text-align: center;
    transition: color 0.5s ease;
}

form {
    background: var(--input-bg);
    padding: 2rem;
    border-radius: 10px;
    /* box-shadow: 0 4px 15px var(--shadow-color); */
    max-width: 400px;
    width: 100%;
    margin-bottom: 2rem;
    transition: background 0.5s ease;
}

label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

input[type="file"],
input[type="text"],
input[type="submit"] {
    width: 100%;
    padding: 0.8rem;
    margin-bottom: 1.5rem;
    border: 1px solid var(--input-border-color);
    border-radius: 5px;
    background: var(--input-bg);
    color: var(--input-text-color);
    font-size: 1rem;
    outline: none;
    transition: all 0.3s ease;
}

input[type="file"] {
    padding: 0.5rem;
}

input[type="submit"] {
    cursor: pointer;
    background: var(--primary-color);
    border: none;
    color: #fff;
    transition: background-color 0.3s ease, transform 0.3s ease;
    font-weight: 700;
}

input[type="submit"]:hover {
    background: var(--secondary-color);
    transform: translateY(-3px);
}

table {
    width: 100%;
    max-width: 800px;
    margin-top: 2rem;
    border-collapse: collapse;
    background: var(--input-bg);
    border-radius: 10px;
    box-shadow: 0 4px 15px var(--shadow-color);
    overflow: hidden;
    transition: background 0.5s ease;
}

table thead {
    background: var(--primary-color);
    color: var(--text-color);
}

table th, table td {
    padding: 1rem;
    text-align: center;
    border-bottom: 1px solid var(--input-border-color);
    transition: background-color 0.5s ease;
}

table th {
    font-weight: 700;
}

.action-buttons {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
}

.action-buttons a,
.action-buttons button {
    padding: 0.5rem;
    background: var(--primary-color);
    color: var(--text-color);
    text-decoration: none;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.action-buttons a:hover,
.action-buttons button:hover {
    background: var(--highlight-color);
    transform: translateY(-3px);
}

.arrow-button {
    padding: 0.5rem;
    background: transparent;
    border: none;
    cursor: pointer;
    color: var(--primary-color);
    font-size: 1.2rem;
    transition: color 0.3s ease;
}

.arrow-button:hover {
    /* color: var(--highlight-color); */
}

.dark-mode-toggle {
    margin-top: 2rem;
    cursor: pointer;
    padding: 0.5rem 1rem;
    background: var(--primary-color);
    border: none;
    color: var(--text-color);
    border-radius: 5px;
    transition: background 0.3s ease;
}

.dark-mode-toggle:hover {
    background: var(--secondary-color);
}

/* Dark mode styling */
body.dark-mode {
    background-color: var(--dark-mode-bg);
    color: var(--dark-mode-text);
}

table.dark-mode,
form.dark-mode,
input[type="file"].dark-mode,
input[type="text"].dark-mode,
input[type="submit"].dark-mode {
    background: var(--dark-mode-bg);
    color: var(--dark-mode-text);
    border-color: var(--input-border-color);
}

table.dark-mode thead {
    background: var(--highlight-color);
}

table.dark-mode th, table.dark-mode td {
    border-bottom: 1px solid var(--input-border-color);
}

/* Responsive Design */
@media (max-width: 768px) {
    form {
        padding: 1.5rem;
    }

    table th, table td {
        padding: 0.8rem;
    }

    .action-buttons {
        flex-direction: column;
        gap: 0.3rem;
    }
}

@media (max-width: 480px) {
    h1 {
        font-size: 1.5rem;
    }

    input[type="file"],
    input[type="text"],
    input[type="submit"] {
        font-size: 0.9rem;
        padding: 0.6rem;
    }

    .dark-mode-toggle {
        padding: 0.4rem 0.8rem;
    }
}

    </style>
</head>
<body>
    <h1>Upload New Gallery Image</h1>
    <form action="admin_gallery.php" method="post" enctype="multipart/form-data">
        <label for="galleryImage">Select Image:</label>
        <input type="file" name="galleryImage" id="galleryImage" required><br><br>
        <label for="caption">Caption:</label>
        <input type="text" name="caption" id="caption"><br><br>
        <input type="submit" name="upload" value="Upload Image">
    </form>

    <h1>Manage Gallery Images</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Image</th>
                <th>Caption</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $gallery_result->data_seek(0); while ($row = $gallery_result->fetch_assoc()) { ?>
                <tr>
                    <td><img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="Gallery Image" width="100"></td>
                    <td>
                        <form action="admin_gallery.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="file" name="editImage">
                            <input type="text" name="caption" value="<?php echo htmlspecialchars($row['caption']); ?>">
                            <input type="submit" name="edit" value="Update">
                        </form>
                    </td>
                    <td class="action-buttons">
                        <button class="arrow-button" onclick="window.location.href='admin_gallery.php?move=up&id=<?php echo $row['id']; ?>'">&#x25B2;</button>
                        <button class="arrow-button" onclick="window.location.href='admin_gallery.php?move=down&id=<?php echo $row['id']; ?>'">&#x25BC;</button>
                        <a href="admin_gallery.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this image?');">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>
