
<?php

session_start();

if (
    !isset($_SESSION["user_id"]) ||
    ($_SESSION["role"] ?? "") !== "admin"
) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "parent_college_db");

if ($conn->connect_error) {
    die("Database connection failed.");
}

$result = $conn->query(
    "SELECT * FROM parent_feedback ORDER BY submitted_date DESC, id DESC"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Feedback</title>
    <link rel="stylesheet" href="css/style.css?v=10">

    <style>
        .feedback-page {
            max-width: 1000px;
            margin: auto;
            padding: 30px 20px;
        }

        .feedback-card {
            background: #FFFFFF;
            border: 1px solid #DDE3E6;
            border-radius: 10px;
            padding: 22px;
            margin: 18px 0;
        }

        .feedback-card h3 {
            color: #087F74;
            margin-bottom: 12px;
        }

        .feedback-card p {
            margin: 9px 0;
            overflow-wrap: anywhere;
        }

        .feedback-date {
            color: #606970;
            font-size: 13px;
        }
    </style>
</head>

<body>
    <main class="feedback-page">
        <a href="admin_dashboard.php">← Back to Dashboard</a>

        <h1 style="margin-top: 22px;">Parent Feedback</h1>
        <p>View feedback submitted by parents.</p>

        <?php if ($result && $result->num_rows > 0): ?>

            <?php while ($row = $result->fetch_assoc()): ?>
                <article class="feedback-card">

                    <h3>
                        <?php echo htmlspecialchars($row["feedback_category"]); ?>
                    </h3>

                    <p>
                        <strong>Parent Name:</strong>
                        <?php echo htmlspecialchars($row["parent_name"]); ?>
                    </p>

                    <p>
                        <strong>Student Name:</strong>
                        <?php echo htmlspecialchars($row["student_name"]); ?>
                    </p>

                    <p>
                        <strong>Feedback:</strong><br>
                        <?php
                        echo nl2br(htmlspecialchars($row["feedback_message"]));
                        ?>
                    </p>

                    <p class="feedback-date">
                        Submitted:
                        <?php echo htmlspecialchars($row["submitted_date"]); ?>
                    </p>

                </article>
            <?php endwhile; ?>

        <?php else: ?>
            <div class="feedback-card">
                <p>No parent feedback has been submitted yet.</p>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>

<?php
$conn->close();
?>
