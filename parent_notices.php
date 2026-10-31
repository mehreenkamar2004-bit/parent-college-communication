
<?php

session_start();

if (
    !isset($_SESSION["user_id"]) ||
    ($_SESSION["role"] ?? "") !== "parent"
) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "parent_college_db");

if ($conn->connect_error) {
    die("Unable to connect to the database.");
}

$sql = "SELECT id, title, message, published_date, created_by
        FROM notices
        ORDER BY published_date DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Notices</title>
    <link rel="stylesheet" href="css/style.css?v=14">
    <style>
        body {
            margin: 0;
            padding: 30px;
            background: #EEF1F3;
            color: #15191C;
            font-family: Arial, sans-serif;
        }

        .notices-container {
            max-width: 1000px;
            margin: 20px auto;
        }

        .notices-header {
            background: #15191C;
            color: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
        }

        .notices-header h1 {
            color: #14B8A6;
            margin-top: 0;
        }

        .notice-card {
            background: white;
            padding: 22px;
            border-radius: 10px;
            margin-bottom: 18px;
            border-left: 5px solid #14B8A6;
            box-shadow: 0 3px 12px rgba(0,0,0,0.06);
        }

        .notice-card h2 {
            margin-top: 0;
            color: #15191C;
        }

        .notice-message {
            white-space: pre-wrap;
            line-height: 1.7;
        }

        .notice-date {
            color: #64748b;
            font-size: 14px;
        }

        .back-link {
            display: inline-block;
            margin: 10px 0 25px;
            color: #0F9488;
            text-decoration: none;
            font-weight: bold;
        }

        .empty-message {
            background: white;
            padding: 22px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <main class="notices-container">
        <header class="notices-header">
            <h1>College Notices</h1>
            <p>Important announcements and updates from your college.</p>
        </header>

        <a class="back-link" href="parent_dashboard.php">
            ← Back to Parent Dashboard
        </a>

        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($notice = $result->fetch_assoc()): ?>
                <article class="notice-card">
                    <h2><?php echo htmlspecialchars($notice['title']); ?></h2>

                    <div class="notice-message"><?php
                        echo htmlspecialchars($notice['message']);
                    ?></div>

                    <p class="notice-date">
                        Published:
                        <?php
                        echo htmlspecialchars(
                            date("d M Y, h:i A", strtotime($notice['published_date']))
                        );
                        ?>
                    </p>
                </article>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-message">
                <h2>No notices available</h2>
                <p>College announcements will appear here when they are published.</p>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
<?php $conn->close(); ?>
