
<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit();
}

$username = trim($_POST["username"] ?? "");
$password = $_POST["password"] ?? "";

if ($username === "" || $password === "") {
    exit("Please enter your username and password.");
}

$conn = new mysqli("localhost", "root", "", "parent_college_db");

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    exit("Unable to connect to the database.");
}

$conn->set_charset("utf8mb4");

$stmt = $conn->prepare(
    "SELECT id, username, password, role
     FROM users
     WHERE username = ?
     LIMIT 1"
);

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$validPassword = false;

if ($user) {
    // Supports password hashes and existing plain-text demo passwords.
    if (password_verify($password, $user["password"])) {
        $validPassword = true;
    } elseif (hash_equals((string)$user["password"], $password)) {
        $validPassword = true;

        // Upgrade the old plain-text password to a secure hash.
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $update = $conn->prepare(
            "UPDATE users SET password = ? WHERE id = ?"
        );
        $update->bind_param("si", $newHash, $user["id"]);
        $update->execute();
        $update->close();
    }
}

if (!$user || !$validPassword) {
    $stmt->close();
    $conn->close();
    exit("Invalid username or password. Please go back and try again.");
}

$role = strtolower(trim($user["role"]));

if (!in_array($role, ["admin", "parent"], true)) {
    $stmt->close();
    $conn->close();
    exit("This account role is not configured yet.");
}

session_regenerate_id(true);

$_SESSION["user_id"] = (int)$user["id"];
$_SESSION["username"] = $user["username"];
$_SESSION["role"] = $role;

$stmt->close();
$conn->close();

if ($role === "admin") {
    header("Location: admin_dashboard.php");
} else {
    header("Location: parent_dashboard.php");
}
exit();
?>
