<?php
session_start();

if (
    !isset($_SESSION["user_id"]) ||
    ($_SESSION["role"] ?? "") !== "parent"
) {
    header("Location: login.php");
    exit();
}


// Connect to the database
$conn = new mysqli("localhost", "root", "", "parent_college_db");

if ($conn->connect_error) {
    die("Database connection failed.");
}

// Demo admission number — replace it with the linked student's
// admission number when parent login is connected.

$parent_username = $_SESSION["username"];

$stmt_student = $conn->prepare(
    "SELECT admission_number
     FROM students
     WHERE parent_username = ?
     LIMIT 1"
);

$stmt_student->bind_param("s", $parent_username);
$stmt_student->execute();

$student_result = $stmt_student->get_result();
$student = $student_result->fetch_assoc();

$stmt_student->close();

if (!$student) {
    die("No student is linked to your parent account.");
}

$admission_number = $student["admission_number"];


$sql = "SELECT attendance_date, status, remarks
        FROM attendance
        WHERE student_admission_number = ?
        ORDER BY attendance_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $admission_number);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Attendance</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            margin: 0;
            padding: 30px;
            background: #EEF1F3;
            font-family: Arial, sans-serif;
            color: #15191C;
        }

        .attendance-container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 25px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        h1 {
            color: #0F9488;
            margin-bottom: 8px;
        }

        .student-number {
            color: #555;
            margin-bottom: 25px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #15191C;
            color: white;
        }

        .present { color: #0F9488; font-weight: bold; }
        .absent { color: #dc2626; font-weight: bold; }
        .late { color: #b45309; font-weight: bold; }

        .back-link {
            display: inline-block;
            margin-top: 22px;
            color: #0F9488;
            text-decoration: none;
            font-weight: bold;
        }

        .empty-message {
            padding: 20px;
            background: #f5f7f8;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="attendance-container">
        <h1>Student Attendance</h1>
        <p class="student-number">
            Admission Number: <?php echo htmlspecialchars($admission_number); ?>
        </p>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php echo htmlspecialchars($row['attendance_date']); ?>
                            </td>
                            <td class="<?php echo strtolower($row['status']); ?>">
                                <?php echo htmlspecialchars($row['status']); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($row['remarks'] ?? '—'); ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">
                            No attendance records found for this student.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <a class="back-link" href="parent_dashboard.php">
            ← Back to Parent Dashboard
        </a>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
