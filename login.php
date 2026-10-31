
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Parent College Communication</title>
    <link rel="stylesheet" href="css/style.css?v=5">
</head>

<body>

    <header class="navbar">
        <div class="logo">🎓 Parent College Communication</div>
        <nav>
            <a href="index.php">Home</a>
        </nav>
    </header>

    <section class="login-section">
        <div class="login-card">
            <p class="small-title">WELCOME BACK</p>
            <h1>Login to Your Account</h1>
            <p class="login-description">
                Access your college communication dashboard.
            </p>

            <form action="login_process.php" method="POST">
                <label for="username">Username</label>
                <input type="text" id="username" name="username"
                       placeholder="Enter your username" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       placeholder="Enter your password" required>

                <button type="submit" class="login-submit">Login</button>
            </form>

            <p class="login-note">
                Please use the login details provided by your college.
            </p>
        </div>
    </section>

    <footer>
        <h3>🎓 Parent College Communication</h3>
        <p>Connecting Parents and College, Better.</p>
        <p class="copyright">© 2026 Parent College Communication System</p>
    </footer>

</body>
</html>
