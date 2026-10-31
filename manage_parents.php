
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
    $parent_name = trim($_POST["parent_name"]);
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $student_admission_number = trim($_POST["student_admission_number"]);

    $stmt = $conn->prepare(
        "INSERT INTO parents
        (parent_name, username, email, phone, student_admission_number)
        VALUES (?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "sssss",
        $parent_name,
        $username,
        $email,
        $phone,
        $student_admission_number
    );

    if ($stmt->execute()) {
        $message = "Parent added successfully!";
    } else {
        $message = "Unable to add parent. Check whether the username already exists.";
    }

    $stmt->close();
}

$result = $conn->query("SELECT * FROM parents ORDER BY id DESC");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Parents</title>
    <link rel="stylesheet" href="css/style.css?v=8">

    <style>
        .parents-page {
            max-width: 1100px;
            width: calc(100% - 40px);
            margin: 0 auto;
            padding: 28px 0 50px;
        }

        .parents-heading {
            text-align: center;
            margin: 35px 0 45px;
        }

        .parents-heading h1 {
            margin: 0 0 12px;
            font-size: clamp(30px, 4vw, 44px);
            color: #172522;
        }

        .parents-heading p {
            margin: 0;
            color: #647570;
            font-size: 18px;
        }

        .parents-card {
    background: #ffffff;
    width: 100%;
    max-width: 760px;
    box-sizing: border-box;
    margin: 0 auto 30px;
    padding: 30px 35px;
    border: 1px solid #d9e3df;
    border-radius: 22px;
    box-shadow: 0 12px 35px rgba(20, 50, 40, 0.06);
}

        .parents-card h2 {
            text-align: center;
            margin: 0 0 30px;
            font-size: 27px;
            color: #172522;
        }

        .parent-field {
            margin-bottom: 22px;
        }

        .parent-field label {
            display: block;
            margin-bottom: 9px;
            font-size: 16px;
            font-weight: 600;
            color: #25332f;
        }

        .parent-field input {
            display: block;
            width: 100%;
            box-sizing: border-box;
            padding: 14px 16px;
            border: 1px solid #d5dfda;
            border-radius: 11px;
            background: #f4f7f5;
            font-size: 16px;
            outline: none;
        }

        .parent-field input:focus {
            border-color: #207560;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(32, 117, 96, 0.12);
        }

        .parents-actions {
            display: flex;
            gap: 12px;
            margin-top: 28px;
        }

        .parents-actions button,
        .parents-actions .clear-button {
            min-height: 52px;
            border-radius: 11px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }

        .parents-actions button {
            flex: 1;
            border: none;
            color: #fff;
            background: #207560;
        }

        .parents-actions button:hover {
            background: #185c4b;
        }

        .parents-actions .clear-button {
            padding: 0 25px;
            background: #fff;
            border: 1px solid #d5dfda;
            color: #172522;
        }

        .parents-table-wrap {
            overflow-x: auto;
        }

        .parents-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .parents-table th {
            background: #172522;
            color: #fff;
            padding: 15px;
            white-space: nowrap;
        }

        .parents-table td {
            padding: 15px;
            border-bottom: 1px solid #e6ece9;
            color: #35433e;
        }

        .parents-table tbody tr:hover {
            background: #f4f8f6;
        }

        .parents-count {
            text-align: center;
            color: #647570;
            margin: -18px 0 25px;
        }

        .parents-message {
            text-align: center;
            color: #207560;
            font-weight: 600;
            margin-bottom: 20px;
        }

        @media (max-width: 600px) {
            .parents-page {
                width: calc(100% - 24px);
            }

            .parents-card {
                padding: 22px 16px;
            }

            .parents-actions {
                flex-direction: column;
            }

            .parents-actions .clear-button {
                min-height: 48px;
            }
        }
    </style>
</head>

<body>
<div class="parents-page">

    <a href="admin_dashboard.php">← Back to Dashboard</a>

    <header class="parents-heading">
        <h1>Manage parents</h1>
        <p>Add parent details and view registered parents.</p>
    </header>

    <?php if ($message != ""): ?>
        <p class="parents-message">
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php endif; ?>

    <section class="parents-card">
        <h2>Add new parent</h2>

        <form method="POST" action="manage_parents.php" id="parentForm">

            <div class="parent-field">
                <label for="parent_name">Parent name</label>
                <input type="text" id="parent_name" name="parent_name"
                       placeholder="e.g. Anjali Menon" required>
            </div>

            <div class="parent-field">
                <label for="username">Username</label>
                <input type="text" id="username" name="username"
                       placeholder="Create a username" required>
            </div>

            <div class="parent-field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email"
                       placeholder="e.g. anjali@example.com">
            </div>

            <div class="parent-field">
                <label for="phone">Phone number</label>
                <input type="tel" id="phone" name="phone"
                       placeholder="Enter phone number">
            </div>

            <div class="parent-field">
                <label for="student_admission_number">
                    Student admission number
                </label>
                <input type="text" id="student_admission_number"
                       name="student_admission_number"
                       placeholder="e.g. 2026-014">
            </div>

            <div class="parents-actions">
                <button type="submit">Add parent</button>
                <button type="reset" class="clear-button">Clear</button>
            </div>

        </form>
    </section>

    <section class="parents-card">
        <h2>Registered parents</h2>

        <p class="parents-count">
            <?php echo $result ? $result->num_rows : 0; ?>
            parent(s)
        </p>

        <div class="parents-table-wrap">
            <table class="parents-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Parent name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Student admission no.</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row["id"]); ?></td>
                            <td><?php echo htmlspecialchars($row["parent_name"]); ?></td>
                            <td><?php echo htmlspecialchars($row["username"]); ?></td>
                            <td><?php echo htmlspecialchars($row["email"] ?? ""); ?></td>
                            <td><?php echo htmlspecialchars($row["phone"] ?? ""); ?></td>
                            <td><?php echo htmlspecialchars($row["student_admission_number"] ?? ""); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No parents added yet.</td>
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
