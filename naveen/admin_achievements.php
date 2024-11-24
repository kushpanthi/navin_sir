<?php
require_once 'config.php';

// Handle achievement upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $icon = $_POST['icon'];

    $sql = "INSERT INTO achievements (title, description, date, icon, ordering) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $ordering = 1; // Add a default ordering value (you can modify this accordingly)
    $stmt->bind_param("ssssi", $title, $description, $date, $icon, $ordering);

    if ($stmt->execute()) {
        echo "Achievement added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Handle achievement delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $delete_sql = "DELETE FROM achievements WHERE id=?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $id);
    if ($delete_stmt->execute()) {
        echo "Achievement deleted successfully!";
    } else {
        echo "Error deleting achievement.";
    }
}

// Handle achievement edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $icon = $_POST['icon'];

    $edit_sql = "UPDATE achievements SET title=?, description=?, date=?, icon=? WHERE id=?";
    $edit_stmt = $conn->prepare($edit_sql);
    $edit_stmt->bind_param("ssssi", $title, $description, $date, $icon, $id);

    if ($edit_stmt->execute()) {
        echo "Achievement updated successfully!";
    } else {
        echo "Error updating achievement.";
    }
}

// Fetch all achievements
$achievements_sql = "SELECT * FROM achievements ORDER BY ordering ASC";
$achievements_result = $conn->query($achievements_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Achievements Management</title>
    <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>
    <h1>Add New Achievement</h1>
    <form action="admin_achievements.php" method="post">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" required><br><br>

        <label for="description">Description:</label>
        <textarea name="description" id="description" required></textarea><br><br>

        <label for="date">Date (e.g., 2022):</label>
        <input type="text" name="date" id="date" required><br><br>

        <label for="icon">Icon (emoji or Unicode):</label>
        <input type="text" name="icon" id="icon" required><br><br>

        <input type="submit" name="upload" value="Add Achievement">
    </form>

    <h1>Manage Achievements</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Date</th>
                <th>Icon</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $achievements_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo htmlspecialchars($row['date']); ?></td>
                    <td><?php echo htmlspecialchars($row['icon']); ?></td>
                    <td class="action-buttons">
                        <form action="admin_achievements.php" method="post">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="text" name="title" value="<?php echo htmlspecialchars($row['title']); ?>">
                            <textarea name="description"><?php echo htmlspecialchars($row['description']); ?></textarea>
                            <input type="text" name="date" value="<?php echo htmlspecialchars($row['date']); ?>">
                            <input type="text" name="icon" value="<?php echo htmlspecialchars($row['icon']); ?>">
                            <input type="submit" name="edit" value="Update">
                        </form>
                        <a href="admin_achievements.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this achievement?');">
                            Delete
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>
