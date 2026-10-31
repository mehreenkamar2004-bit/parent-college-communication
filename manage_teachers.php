
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
    $teacher_name = trim($_POST["teacher_name"]);
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $department = trim($_POST["department"]);
    $subject = trim($_POST["subject"]);

    $stmt = $conn->prepare(
        "INSERT INTO teachers
        (teacher_name, username, email, phone, department, subject)
        VALUES (?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "ssssss",
        $teacher_name,
        $username,
        $email,
        $phone,
        $department,
        $subject
    );

    if ($stmt->execute()) {
        $message = "Teacher added successfully!";
    } else {
        $message = "Unable to add teacher. Check whether the username already exists.";
    }

    $stmt->close();
}

$result = $conn->query("SELECT * FROM teachers ORDER BY id DESC");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teachers</title>
    <link rel="stylesheet" href="css/style.css?v=9">

    <style>
        .management-page {
            max-width: 1100px;
            margin: 0 auto;
            padding: 30px 24px;
        }

        .back-link {
            display: inline-block;
            color: #15191C;
            text-decoration: none;
            margin-bottom: 24px;
            font-weight: 500;
        }

        .page-heading {
            text-align: center;
            margin: 10px 0 8px;
            color: #182522;
        }

        .page-description {
            text-align: center;
            color: #647570;
            margin-bottom: 35px;
        }

        .teacher-card, .teacher-list-card {
            background: #fff;
            border: 1px solid #d8e2de;
            border-radius: 18px;
            padding: 30px;
            margin-bottom: 28px;
            box-shadow: 0 8px 24px rgba(21, 25, 28, 0.04);
        }

        .teacher-card h2, .teacher-list-card h2 {
            text-align: center;
            margin: 0 0 28px;
            color: #182522;
        }

        .teacher-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .teacher-field label {
            display: block;
            margin-bottom: 9px;
            font-weight: 600;
            color: #26332f;
        }

        .teacher-field input {
            width: 100%;
            box-sizing: border-box;
            padding: 13px 15px;
            border: 1px solid #d5dfdb;
            border-radius: 9px;
            background: #f3f6f5;
            font-size: 16px;
            outline: none;
        }

        .teacher-field input:focus {
            border-color: #20745f;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(32, 116, 95, 0.12);
        }

        .teacher-actions {
            display: flex;
            gap: 12px;
            margin-top: 26px;
        }

        .teacher-submit, .teacher-clear {
            padding: 13px 22px;
            border-radius: 9px;
            font-size: 16px;
            cursor: pointer;
        }

        .teacher-submit {
            flex: 1;
            background: #20745f;
            color: white;
            border: 1px solid #20745f;
            font-weight: 600;
        }

        .teacher-submit:hover {
            background: #185b4b;
        }

        .teacher-clear {
            background: white;
            color: #182522;
            border: 1px solid #d5dfdb;
        }

        .teacher-table-wrap {
            overflow-x: auto;
        }

        .teacher-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .teacher-table th {
            background: #182522;
            color: white;
            padding: 14px;
        }

        .teacher-table td {
            padding: 14px;
            border-bottom: 1px solid #e5ebe8;
        }

        .teacher-table tr:nth-child(even) td {
            background: #f7f9f8;
        }

        .teacher-message {
            background: #e3f4ed;
            color: #17634d;
            padding: 13px 16px;
            border-radius: 9px;
            margin-bottom: 20px;
        }

        @media (max-width: 650px) {
            .management-page {
                padding: 20px 14px;
            }

            .teacher-card, .teacher-list-card {
                padding: 20px;
            }

            .teacher-form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
<div class="management-page">

    <a class="back-link" href="admin_dashboard.php">← Back to Dashboard</a>

    <h1 class="page-heading">Manage Teachers</h1>
    <p class="page-description">
        Add teacher details and view registered teachers.
    </p>

    <?php if ($message != ""): ?>
        <p class="teacher-message">
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="manage_teachers.php" class="teacher-card">
        <h2>Add New Teacher</h2>

        <div class="teacher-form-grid">
            <div class="teacher-field">
                <label for="teacher_name">Teacher Name</label>
                <input id="teacher_name" type="text" name="teacher_name"
                       placeholder="e.g. Anjali Menon" required>
            </div>

            <div class="teacher-field">
                <label for="username">Username</label>
                <input id="username" type="text" name="username"
                       placeholder="Create a username" required>
            </div>

            <div class="teacher-field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email"
                       placeholder="e.g. teacher@example.com">
            </div>

            <div class="teacher-field">
                <label for="phone">Phone Number</label>
                <input id="phone" type="tel" name="phone"
                       placeholder="Enter phone number">
            </div>

            <div class="teacher-field">
                <label for="department">Department</label>
                <input id="department" type="text" name="department"
                       placeholder="e.g. MCA">
            </div>

            <div class="teacher-field">
                <label for="subject">Subject</label>
                <input id="subject" type="text" name="subject"
                       placeholder="e.g. Database Management">
            </div>
        </div>

        <div class="teacher-actions">
            <button class="teacher-submit" type="submit">Add Teacher</button>
            <button class="teacher-clear" type="reset">Clear</button>
        </div>
    </form>

    <section class="teacher-list-card">
        <h2>Registered Teachers</h2>

        <div class="teacher-table-wrap">
            <table class="teacher-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Department</th>
                        <th>Subject</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row["id"]); ?></td>
                            <td><?php echo htmlspecialchars($row["teacher_name"]); ?></td>
                            <td><?php echo htmlspecialchars($row["username"]); ?></td>
                            <td><?php echo htmlspecialchars($row["email"] ?? ""); ?></td>
                            <td><?php echo htmlspecialchars($row["phone"] ?? ""); ?></td>
                            <td><?php echo htmlspecialchars($row["department"] ?? ""); ?></td>
                            <td><?php echo htmlspecialchars($row["subject"] ?? ""); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No teachers added yet.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

</div>
</body>
</html>


<?php
$conn->close();
?>
