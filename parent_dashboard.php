
<?php
session_start();

if (
    !isset($_SESSION["user_id"]) ||
    ($_SESSION["role"] ?? "") !== "parent"
) {
    header("Location: logout.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Dashboard</title>
    <link rel="stylesheet" href="css/style.css?v=14">
</head>

<body class="dashboard-body">

    <aside class="sidebar">
        <h2>🎓 Parent College</h2>
        <p class="sidebar-label">PARENT MENU</p>

        <a href="parent_dashboard.php" class="active">Dashboard</a>
        <a href="parent_notices.php">College Notices</a>
        <a href="parent_attendance.php">Child's Attendance</a>
        <a href="parent_results.php">Academic Results</a>
        <a href="submit_feedback.php">Submit Feedback</a>
        <a href="index.php">Visit Home Page</a>
    </aside>

    <main class="dashboard-main">

        <header class="dashboard-header">
            <div>
                <p class="small-title">PARENT PORTAL</p>
                <h1>Parent Dashboard</h1>
                <p>Stay connected with your child's college.</p>
            </div>

            <a href="logout.php" class="logout-button">Logout</a>
        </header>

        <section class="welcome-card">
            <h2>Welcome, Parent! 👋</h2>
            <p>
                Access college announcements, attendance records,
                examination results and feedback.
            </p>
        </section>

        <section class="dashboard-cards">

            <div class="dashboard-card">
                <span>📢</span>
                <h3>College Notices</h3>
                <p>Read important announcements from the college.</p>
                <a href="parent_notices.php">View Notices →</a>
            </div>

            <div class="dashboard-card">
                <span>📅</span>
                <h3>Child's Attendance</h3>
                <p>Check attendance records and status.</p>
                <a href="parent_attendance.php">View Attendance →</a>
            </div>

            <div class="dashboard-card">
                <span>📊</span>
                <h3>Academic Results</h3>
                <p>View examination marks and academic performance.</p>
                <a href="parent_results.php">View Results →</a>
            </div>

            <div class="dashboard-card">
                <span>💬</span>
                <h3>Parent Feedback</h3>
                <p>Share your feedback with the college.</p>
                <a href="submit_feedback.php">Submit Feedback →</a>
            </div>

        </section>

        <footer class="dashboard-footer">
            Parent College Communication System © 2026
        </footer>

    </main>
</body>
</html>
