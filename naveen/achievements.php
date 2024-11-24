<?php
require_once 'config.php';

// Fetch all achievements
$achievements_sql = "SELECT * FROM achievements ORDER BY ordering ASC";
$achievements_result = $conn->query($achievements_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Achievements - Navin</title>
    <link rel="stylesheet" href="achievements.css">
</head>
<body>
<section class="achievements-container">
    <h2>All Achievements</h2>
    <div class="achievements-grid">
        <?php while ($row = $achievements_result->fetch_assoc()) { ?>
            <div class="achievement-card">
                <h3><?php echo htmlspecialchars($row['title']); ?> <span><?php echo htmlspecialchars($row['icon']); ?></span></h3>
                <p><?php echo htmlspecialchars($row['date']); ?></p>
                <p><?php echo htmlspecialchars($row['description']); ?></p>
            </div>
        <?php } ?>
    </div>
</section>
</body>
</html>

