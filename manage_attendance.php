
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
    $attendance_date = $_POST["attendance_date"] ?? "";
    $status = $_POST["status"] ?? "";
    $remarks = trim($_POST["remarks"] ?? "");

    $valid_date = DateTime::createFromFormat("!Y-m-d", $attendance_date);
    $date_is_valid = $valid_date &&
        $valid_date->format("Y-m-d") === $attendance_date;

    if (
        $admission_number !== "" &&
        $date_is_valid &&
        in_array($status, ["Present", "Absent", "Late"], true)
    ) {
        $check = $conn->prepare(
            "SELECT id FROM students WHERE admission_number = ?"
        );
        $check->bind_param("s", $admission_number);
        $check->execute();
        $student_result = $check->get_result();

        if ($student_result->num_rows === 0) {
            $message = "Student admission number not found. Add the student first.";
        } else {
            $stmt = $conn->prepare(
                "INSERT INTO attendance
                (student_admission_number, attendance_date, status, remarks)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                status = VALUES(status),
                remarks = VALUES(remarks)"
            );

            $stmt->bind_param(
                "ssss",
                $admission_number,
                $attendance_date,
                $status,
                $remarks
            );

            if ($stmt->execute()) {
                $message = "Attendance saved successfully!";
            } else {
                $message = "Unable to save attendance.";
            }

            $stmt->close();
        }

        $check->close();
    } else {
        $message = "Please enter valid details in all required fields.";
    }
}

$result = $conn->query(
    "SELECT a.*, s.student_name
     FROM attendance a
     LEFT JOIN students s
       ON s.admission_number = a.student_admission_number
     ORDER BY a.attendance_date DESC, a.id DESC"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Attendance</title>
    <link rel="stylesheet" href="css/style.css?v=12">

    <style>
        .attendance-page {
            max-width: 1100px;
            margin: auto;
            padding: 30px 20px;
        }

        .attendance-form,
        .attendance-table-container {
            background: white;
            padding: 22px;
            margin: 22px 0;
            border: 1px solid #DDE3E6;
            border-radius: 10px;
        }

        .attendance-form label {
            display: block;
            font-weight: bold;
            margin: 14px 0 6px;
        }

        .attendance-form input,
        .attendance-form select,
        .attendance-form textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 11px;
            border: 1px solid #D3D9DD;
            border-radius: 6px;
            font: inherit;
        }

        .attendance-form button {
            margin-top: 18px;
            padding: 12px 20px;
            background: #14B8A6;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }

        .attendance-table-container {
            overflow-x: auto;
        }

        .attendance-table {
            width: 100%;
            border-collapse: collapse;
        }

        .attendance-table th,
        .attendance-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #DDE3E6;
        }

        .attendance-table th {
            background: #15191C;
            color: white;
        }
    </style>
</head>

<body>
<main class="attendance-page">
    <a href="admin_dashboard.php">← Back to Dashboard</a>

    <h1 style="margin-top: 22px;">Student Attendance Management</h1>
    <p>Record and view student attendance.</p>

    <?php if ($message !== ""): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <section class="attendance-form">
        <h2>Record Attendance</h2>

        <form method="POST" action="manage_attendance.php">
            <label for="admission_number">Student Admission Number</label>
            <input type="text" id="admission_number"
                   name="admission_number" required
                   placeholder="Enter the student's admission number">

            <label for="attendance_date">Attendance Date</label>
            <input type="date" id="attendance_date"
                   name="attendance_date"
                   value="<?php echo date('Y-m-d'); ?>" required>

            <label for="status">Attendance Status</label>
            <select id="status" name="status" required>
                <option value="">Select status</option>
                <option value="Present">Present</option>
                <option value="Absent">Absent</option>
                <option value="Late">Late</option>
            </select>

            <label for="remarks">Remarks (Optional)</label>
            <textarea id="remarks" name="remarks"
                      maxlength="255"
                      placeholder="Enter remarks if needed"></textarea>

            <button type="submit">Save Attendance</button>
        </form>
    </section>

    <h2>Attendance Records</h2>

    <div class="attendance-table-container">
        <table class="attendance-table">
            <tr>
                <th>Student Name</th>
                <th>Admission Number</th>
                <th>Date</th>
                <th>Status</th>
                <th>Remarks</th>
            </tr>

            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php echo htmlspecialchars(
                                $row["student_name"] ?? "Unknown"
                            ); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars(
                                $row["student_admission_number"]
                            ); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars(
                                $row["attendance_date"]
                            ); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row["status"]); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row["remarks"] ?? ""); ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No attendance records available.</td>
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
