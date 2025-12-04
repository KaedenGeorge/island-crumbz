<?php
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // get user from database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    if (!$stmt) {
        die("Database error: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // hash given password
        $hashed = hash("sha256", $password);

        if ($hashed === $row['password']) {

            // store login info
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_email'] = $row['email'];
            $_SESSION['user_role']  = $row['role'];  // VERY IMPORTANT

            $stmt->close();
            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
    }

    $stmt->close();
}

include 'header.php';
?>

<div class="auth-wrapper">
    <h1>Login</h1>

    <div class="auth-card">
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post">
            <label>Email
                <input type="email" name="email" required placeholder="user@example.com">
            </label>

            <label>Password
                <input type="password" name="password" required placeholder="secret123">
            </label>

            <button class="btn btn-primary" type="submit">Login</button>
        </form>

        <div style="margin-top:1rem; text-align:center;">
            Don’t have an account? <a href="signup.php">Sign Up</a>
        </div>

        <hr style="margin:1.4rem 0; opacity:0.3;">

        <a href="google_login.php" class="btn btn-outline" style="width:100%; text-align:center;">
            <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" 
                 style="height:18px; vertical-align:middle; margin-right:6px;">
            Login with Google
        </a>
    </div>
</div>

<?php include 'footer.php'; ?>
