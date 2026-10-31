
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

// Demo admission number — we will connect this to the parent's
// linked student after login is properly connected.

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


$sql = "SELECT * FROM academic_results
        WHERE student_admission_number = ?
        ORDER BY id DESC";

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
    <title>Examination Results</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            margin: 0;
            padding: 30px;
            background: #EEF1F3;
            font-family: Arial, sans-serif;
            color: #15191C;
        }

        .results-container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 25px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        h1 { color: #0F9488; }

        .table-wrapper { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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

        .back-link {
            display: inline-block;
            margin-top: 22px;
            color: #0F9488;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="results-container">
        <h1>Examination Results</h1>
        <p>
            Admission Number:
            <?php echo htmlspecialchars($admission_number); ?>
        </p>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <?php
                        if ($result->num_rows > 0) {
                            foreach (array_keys($result->fetch_assoc()) as $column) {
                                echo "<th>" . htmlspecialchars(
                                    ucwords(str_replace("_", " ", $column))
                                ) . "</th>";
                            }
                            $result->data_seek(0);
                        } else {
                            echo "<th>Results</th>";
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <?php foreach ($row as $value): ?>
                                    <td>
                                        <?php echo htmlspecialchars((string)($value ?? "—")); ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td>No examination results found for this student.</td>
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
