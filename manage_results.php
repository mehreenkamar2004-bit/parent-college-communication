
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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $admission_number = trim($_POST["admission_number"] ?? "");
    $subject = trim($_POST["subject"] ?? "");
    $exam_name = trim($_POST["exam_name"] ?? "");
    $marks_input = $_POST["marks_obtained"] ?? "";
    $maximum_input = $_POST["maximum_marks"] ?? "";
    $remarks = trim($_POST["remarks"] ?? "");

    $marks = filter_var($marks_input, FILTER_VALIDATE_FLOAT);
    $maximum = filter_var($maximum_input, FILTER_VALIDATE_FLOAT);

    if (
        $admission_number === "" ||
        $subject === "" ||
        $exam_name === "" ||
        $marks === false ||
        $maximum === false ||
        $maximum <= 0 ||
        $marks < 0 ||
        $marks > $maximum
    ) {
        $message = "Please enter valid details. Marks must be between 0 and the maximum marks.";
    } else {
        $check = $conn->prepare(
            "SELECT id FROM students WHERE admission_number = ?"
        );
        $check->bind_param("s", $admission_number);
        $check->execute();
        $student_result = $check->get_result();

        if ($student_result->num_rows === 0) {
            $message = "Student not found. Add the student first.";
        } else {
            $stmt = $conn->prepare(
                "INSERT INTO academic_results
                (student_admission_number, subject, exam_name,
                 marks_obtained, maximum_marks, remarks)
                VALUES (?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                marks_obtained = VALUES(marks_obtained),
                maximum_marks = VALUES(maximum_marks),
                remarks = VALUES(remarks)"
            );

            $stmt->bind_param(
                "sssdds",
                $admission_number,
                $subject,
                $exam_name,
                $marks,
                $maximum,
                $remarks
            );

            if ($stmt->execute()) {
                $message = "Academic result saved successfully!";
            } else {
                $message = "Unable to save the result.";
            }

            $stmt->close();
        }

        $check->close();
    }
}

$result = $conn->query(
    "SELECT r.*, s.student_name
     FROM academic_results r
     LEFT JOIN students s
       ON s.admission_number = r.student_admission_number
     ORDER BY r.created_at DESC, r.id DESC"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Academic Results</title>
    <link rel="stylesheet" href="css/style.css?v=13">

    <style>
        .results-page {
            max-width: 1100px;
            margin: auto;
            padding: 30px 20px;
        }

        .results-form,
        .results-table-container {
            background: white;
            padding: 22px;
            margin: 22px 0;
            border: 1px solid #DDE3E6;
            border-radius: 10px;
        }

        .results-form label {
            display: block;
            font-weight: bold;
            margin: 14px 0 6px;
        }

        .results-form input,
        .results-form textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 11px;
            border: 1px solid #D3D9DD;
            border-radius: 6px;
            font: inherit;
        }

        .results-form button {
            margin-top: 18px;
            padding: 12px 20px;
            background: #14B8A6;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }

        .results-table-container {
            overflow-x: auto;
        }

        .results-table {
            width: 100%;
            border-collapse: collapse;
        }

        .results-table th,
        .results-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #DDE3E6;
        }

        .results-table th {
            background: #15191C;
            color: white;
        }
    </style>
</head>

<body>
<main class="results-page">
    <a href="admin_dashboard.php">← Back to Dashboard</a>

    <h1 style="margin-top: 22px;">Academic Performance & Results</h1>
    <p>Enter and view students' examination marks.</p>

    <?php if ($message !== ""): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <section class="results-form">
        <h2>Add or Update Result</h2>

        <form method="POST" action="manage_results.php">
            <label for="admission_number">Student Admission Number</label>
            <input type="text" id="admission_number"
                   name="admission_number" required
                   placeholder="Example: MCA001">

            <label for="subject">Subject</label>
            <input type="text" id="subject" name="subject"
                   maxlength="100" required
                   placeholder="Example: Database Management">

            <label for="exam_name">Examination Name</label>
            <input type="text" id="exam_name" name="exam_name"
                   maxlength="100" required
                   placeholder="Example: Internal Exam 1">

            <label for="marks_obtained">Marks Obtained</label>
            <input type="number" id="marks_obtained"
                   name="marks_obtained" min="0" step="0.01" required>

            <label for="maximum_marks">Maximum Marks</label>
            <input type="number" id="maximum_marks"
                   name="maximum_marks" min="0.01"
                   step="0.01" required>

            <label for="remarks">Remarks (Optional)</label>
            <textarea id="remarks" name="remarks" maxlength="255"
                      placeholder="Example: Good performance"></textarea>

            <button type="submit">Save Result</button>
        </form>
    </section>

    <h2>Saved Examination Results</h2>

    <div class="results-table-container">
        <table class="results-table">
            <tr>
                <th>Student</th>
                <th>Admission No.</th>
                <th>Subject</th>
                <th>Exam</th>
                <th>Marks</th>
                <th>Maximum</th>
                <th>Remarks</th>
            </tr>

            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row["student_name"] ?? "Unknown"); ?></td>
                        <td><?php echo htmlspecialchars($row["student_admission_number"]); ?></td>
                        <td><?php echo htmlspecialchars($row["subject"]); ?></td>
                        <td><?php echo htmlspecialchars($row["exam_name"]); ?></td>
                        <td><?php echo htmlspecialchars($row["marks_obtained"]); ?></td>
                        <td><?php echo htmlspecialchars($row["maximum_marks"]); ?></td>
                        <td><?php echo htmlspecialchars($row["remarks"] ?? ""); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No examination results available.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</main>
</body>
</html>

<?php
$conn->close();
?>
