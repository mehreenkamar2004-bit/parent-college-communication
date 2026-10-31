<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: logout.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Parent College Communication</title>
    <link rel="stylesheet" href="css/style.css?v=5">
</head>

<body class="dashboard-body">

    <aside class="sidebar">
        <h2>🎓 Parent College</h2>
        <p class="sidebar-label">ADMIN MENU</p>

        <a href="admin_dashboard.php" class="active">Dashboard</a>
        <a href="manage_students.php">Manage Students</a>
        <a href="manage_parents.php">Manage Parents</a>
        <a href="manage_teachers.php">Manage Teachers</a> 
        <a href="manage_notices.php">Notices</a>
        <a href="parent_feedback.php">Parent Feedback</a>
        <a href="manage_attendance.php">Manage Attendance</a>
        <a href="manage_results.php">Academic Results</a>
        <a href="index.php">Visit Home Page</a>
    </aside>

    <main class="dashboard-main">

        <header class="dashboard-header">
            <div>
                <p class="small-title">ADMINISTRATION</p>
                <h1>Admin Dashboard</h1>
                <p>Manage your college communication system.</p>
            </div>

           
<a href="logout.php" class="logout-button">Logout</a>

        </header>

        <section class="welcome-card">
            <h2>Welcome, Admin! 👋</h2>
            <p>Here is an overview of your college communication portal.</p>
            
<div class="dashboard-card">
    <span>📅</span>
    <h3>Student Attendance</h3>
    <p>Record and view student attendance.</p>
    <a href="manage_attendance.php">Manage Attendance →</a>
</div>

        </section>

        <section class="dashboard-cards">
            <div class="dashboard-card">
                <span>👨‍🎓</span>
                <h3>Students</h3>
                <p>Manage student details and records.</p>
                <a href="manage_parents.php">Manage Parents →</a>
            </div>

            <div class="dashboard-card">
                <span>👨‍👩‍👧</span>
                <h3>Parents</h3>
                <p>View and manage parent information.</p>
                <a href="#parents">Manage Parents →</a>
            </div>

            <div class="dashboard-card">
                <span>👩‍🏫</span>
                <h3>Teachers</h3>
                <p>Manage teacher details and accounts.</p>
                <a href="manage_teachers.php">Manage Teachers →</a>
            </div>

            <div class="dashboard-card">
                <span>📢</span>
                <h3>Notices</h3>
                <p>Share important college announcements.</p>
                <a href="manage_notices.php">Manage Notices →</a>
            </div>

            <div class="dashboard-card">
                <span>💬</span>
                <h3>Parent Feedback</h3>
                <p>Review feedback submitted by parents.</p>
                <a href="parent_feedback.php">View Feedback →</a>
            </div>
            
<div class="dashboard-card">
    <span>📊</span>
    <h3>Academic Results</h3>
    <p>Record and view student examination marks.</p>
    <a href="manage_results.php">Manage Results →</a>
</div>

        </section>

        <footer class="dashboard-footer">
            Parent College Communication System © 2026
        </footer>

    </main>

</body>
</html>
