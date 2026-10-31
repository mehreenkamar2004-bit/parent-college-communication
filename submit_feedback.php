
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
    die("Database connection failed.");
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $parent_name = trim($_POST["parent_name"]);
    $student_name = trim($_POST["student_name"]);
    $category = trim($_POST["feedback_category"]);
    $feedback = trim($_POST["feedback_message"]);

    if (
        $parent_name !== "" &&
        $student_name !== "" &&
        $category !== "" &&
        $feedback !== ""
    ) {
        $stmt = $conn->prepare(
            "INSERT INTO parent_feedback
            (parent_name, student_name, feedback_category, feedback_message)
            VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "ssss",
            $parent_name,
            $student_name,
            $category,
            $feedback
        );

        if ($stmt->execute()) {
            $message = "Your feedback has been submitted successfully!";
        } else {
            $message = "Unable to submit feedback. Please try again.";
        }

        $stmt->close();
    } else {
        $message = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Parent Feedback</title>
    <link rel="stylesheet" href="css/style.css?v=11">

    <style>
        .feedback-form-page {
            max-width: 650px;
            margin: auto;
            padding: 35px 20px;
        }

        .feedback-form-card {
            background: #FFFFFF;
            padding: 28px;
            margin-top: 22px;
            border-radius: 10px;
            border: 1px solid #DDE3E6;
        }

        .feedback-form-card label {
            display: block;
            font-weight: bold;
            margin: 17px 0 7px;
        }

        .feedback-form-card input,
        .feedback-form-card select,
        .feedback-form-card textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 12px;
            border: 1px solid #D3D9DD;
            border-radius: 6px;
            font: inherit;
        }

        .feedback-form-card textarea {
            min-height: 130px;
            resize: vertical;
        }

        .feedback-submit {
            margin-top: 22px;
            padding: 12px 20px;
            background: #14B8A6;
            color: #15191C;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }

        .feedback-status {
            margin-top: 18px;
            color: #087F74;
        }
    </style>
</head>

<body>
    <main class="feedback-form-page">
        <a href="index.php">← Back to Home</a>

        <h1 style="margin-top: 22px;">Parent Feedback Form</h1>
        <p>Share your feedback about your child's development.</p>

        <section class="feedback-form-card">
            <form method="POST" action="submit_feedback.php">
                <label for="parent_name">Parent Name</label>
                <input type="text" id="parent_name" name="parent_name"
                       maxlength="100" required>

                <label for="student_name">Student Name</label>
                <input type="text" id="student_name" name="student_name"
                       maxlength="100" required>

                <label for="feedback_category">Feedback Category</label>
                <select id="feedback_category" name="feedback_category" required>
                    <option value="">Select a category</option>
                    <option value="Academic Progress">Academic Progress</option>
                    <option value="Attendance">Attendance</option>
                    <option value="Behaviour">Behaviour</option>
                    <option value="Discipline">Discipline</option>
                    <option value="Overall Development">Overall Development</option>
                    <option value="Other">Other</option>
                </select>

                <label for="feedback_message">Your Feedback</label>
                <textarea id="feedback_message" name="feedback_message"
                          placeholder="Write your feedback here..."
                          required></textarea>

                <button type="submit" class="feedback-submit">
                    Submit Feedback
                </button>
            </form>

            <?php if ($message !== ""): ?>
                <p class="feedback-status">
                    <?php echo htmlspecialchars($message); ?>
                </p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>

<?php
$conn->close();
?>
