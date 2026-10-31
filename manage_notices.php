
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

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["add_notice"])) {
        $title = trim($_POST["title"]);
        $notice_message = trim($_POST["notice_message"]);

        if ($title !== "" && $notice_message !== "") {
            $stmt = $conn->prepare(
                "INSERT INTO notices (title, message, created_by)
                 VALUES (?, ?, ?)"
            );

            $created_by = "admin";
            $stmt->bind_param(
                "sss",
                $title,
                $notice_message,
                $created_by
            );

            if ($stmt->execute()) {
                $message = "Notice published successfully!";
            } else {
                $message = "Unable to publish notice.";
            }

            $stmt->close();
        } else {
            $message = "Please enter both the title and message.";
        }
    }

    if (isset($_POST["delete_notice"])) {
        $id = (int) $_POST["notice_id"];

        $stmt = $conn->prepare(
            "DELETE FROM notices WHERE id = ?"
        );
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $message = "Notice deleted successfully.";
        } else {
            $message = "Unable to delete notice.";
        }

        $stmt->close();
    }
}

$result = $conn->query(
    "SELECT * FROM notices ORDER BY published_date DESC, id DESC"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Notices</title>

    <link rel="stylesheet" href="css/style.css?v=9">

    <style>
        .notice-page {
            max-width: 1000px;
            margin: auto;
            padding: 30px 20px;
        }

        .notice-form,
        .notice-card {
            background: #FFFFFF;
            padding: 22px;
            margin: 20px 0;
            border-radius: 10px;
            border: 1px solid #DDE3E6;
        }

        .notice-form input,
        .notice-form textarea {
            width: 100%;
            padding: 12px;
            margin: 8px 0 16px;
            border: 1px solid #D3D9DD;
            border-radius: 6px;
            box-sizing: border-box;
            font: inherit;
        }

        .notice-form textarea {
            min-height: 120px;
            resize: vertical;
        }

        .notice-button {
            padding: 11px 18px;
            background: #14B8A6;
            color: #15191C;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        .delete-button {
            padding: 8px 13px;
            background: #15191C;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .notice-message {
            white-space: pre-wrap;
            overflow-wrap: anywhere;
            line-height: 1.6;
        }

        .notice-date {
            color: #606970;
            font-size: 13px;
        }
    </style>
</head>

<body>
    <main class="notice-page">

        <a href="admin_dashboard.php">← Back to Dashboard</a>

        <h1>Manage Notices & Announcements</h1>
        <p>Publish important updates for your college community.</p>

        <?php if ($message !== ""): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <section class="notice-form">
            <h2>Publish a New Notice</h2>

            <form method="POST" action="manage_notices.php">
                <label for="title">Notice Title</label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    maxlength="200"
                    placeholder="Example: Internal Examination Timetable"
                    required
                >

                <label for="notice_message">Notice Details</label>
                <textarea
                    id="notice_message"
                    name="notice_message"
                    placeholder="Enter the complete notice here..."
                    required
                ></textarea>

                <button
                    type="submit"
                    name="add_notice"
                    class="notice-button"
                >
                    Publish Notice
                </button>
            </form>
        </section>

        <h2>Published Notices</h2>

        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>

                <article class="notice-card">
                    <h3>
                        <?php echo htmlspecialchars($row["title"]); ?>
                    </h3>

                    <p class="notice-message"><?php
                        echo htmlspecialchars($row["message"]);
                    ?></p>

                    <p class="notice-date">
                        Published:
                        <?php echo htmlspecialchars($row["published_date"]); ?>
                    </p>

                    <form method="POST" action="manage_notices.php"
                          onsubmit="return confirm('Delete this notice?');">
                        <input type="hidden" name="notice_id"
                               value="<?php echo (int) $row["id"]; ?>">

                        <button type="submit" name="delete_notice"
                                class="delete-button">
                            Delete Notice
                        </button>
                    </form>
                </article>

            <?php endwhile; ?>
        <?php else: ?>
            <p>No notices have been published yet.</p>
        <?php endif; ?>

    </main>
</body>
</html>

<?php
$conn->close();
?>
