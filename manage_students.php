
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
    die("Database connection failed");
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_name = trim($_POST["student_name"]);
    $admission_number = trim($_POST["admission_number"]);
    $department = trim($_POST["department"]);
    $semester = (int) $_POST["semester"];
    $parent_username = trim($_POST["parent_username"]);

    $stmt = $conn->prepare(
        "INSERT INTO students
        (student_name, admission_number, department, semester, parent_username)
        VALUES (?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "sssis",
        $student_name,
        $admission_number,
        $department,
        $semester,
        $parent_username
    );

    if ($stmt->execute()) {
        $message = "Student added successfully!";
    } else {
        $message = "Unable to add student. Check that the admission number is unique.";
    }

    $stmt->close();
}

$result = $conn->query("SELECT * FROM students ORDER BY id DESC");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
    <link rel="stylesheet" href="css/style.css?v=7">
</head>
<body class="manage-students-page">

    <main class="students-page">

        <a class="back-dashboard" href="admin_dashboard.php">
            ← Back to dashboard
        </a>

        <header class="students-page-header">
            <h1>Manage students</h1>
            <p>Add student details and view registered students.</p>
        </header>

        <?php if ($message != ""): ?>
            <p class="student-message">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>

        <section class="student-panel">
            <h2>Add new student</h2>

            <form method="POST" action="manage_students.php" class="student-form">

                <div class="student-field">
                    <label for="student_name">Student name</label>
                    <input
                        type="text"
                        id="student_name"
                        name="student_name"
                        placeholder="e.g. Anjali Menon"
                        required
                    >
                </div>

                <div class="student-field">
                    <label for="admission_number">Admission number</label>
                    <input
                        type="text"
                        id="admission_number"
                        name="admission_number"
                        placeholder="e.g. 2026-014"
                        required
                    >
                </div>

                <div class="student-field">
                    <label for="department">Department</label>
                    <input
                        type="text"
                        id="department"
                        name="department"
                        placeholder="e.g. MCA"
                        required
                    >
                </div>

                <div class="student-field">
                    <label for="semester">Semester</label>
                    <input
                        type="number"
                        id="semester"
                        name="semester"
                        min="1"
                        max="10"
                        placeholder="e.g. 2"
                        required
                    >
                </div>

                <div class="student-field">
                    <label for="parent_username">Parent username (optional)</label>
                    <input
                        type="text"
                        id="parent_username"
                        name="parent_username"
                        placeholder="Enter parent username"
                    >
                </div>

                <div class="student-form-actions">
                    <button type="submit" class="add-student-button">
                        Add student
                    </button>
                    <button type="reset" class="clear-student-button">
                        Clear
                    </button>
                </div>

            </form>
        </section>

        <section class="student-panel registered-students-panel">
            <div class="registered-students-heading">
                <h2>Registered students</h2>
                <p><?php echo $result ? $result->num_rows : 0; ?> students</p>
            </div>

            <div class="students-table-wrapper">
                <table class="students-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Admission no.</th>
                            <th>Department</th>
                            <th>Semester</th>
                            <th>Parent username</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row["student_name"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["admission_number"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["department"] ?? ""); ?></td>
                                    <td><?php echo htmlspecialchars($row["semester"] ?? ""); ?></td>
                                    <td><?php echo htmlspecialchars($row["parent_username"] ?? ""); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="empty-students">
                                    No students added yet.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

    </main>

</body>
</html>


<?php
$conn->close();
?>
