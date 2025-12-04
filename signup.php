<?php
require_once 'config.php';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first = trim($_POST['first_name']);
    $last = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $pass = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($pass !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $errors[] = "This email is already registered.";
    }
    $stmt->close();

    if (empty($errors)) {
        // Hash password using SHA256 (same as login)
        $hashed = hash("sha256", $pass);

        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password) 
                                VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $first, $last, $email, $hashed);

        if ($stmt->execute()) {
            header("Location: login.php?signup=success");
            exit;
        } else {
            $errors[] = "Something went wrong. Try again.";
        }

        $stmt->close();
    }
}

include 'header.php';
?>

<div class="auth-wrapper">
    <h1>Create Your Account</h1>

    <div class="auth-card">

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $e) echo "<p>$e</p>"; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <label>First Name
                <input type="text" name="first_name" required>
            </label>

            <label>Last Name
                <input type="text" name="last_name" required>
            </label>

            <label>Email
                <input type="email" name="email" required>
            </label>

            <label>Password
                <input type="password" name="password" required>
            </label>

            <label>Confirm Password
                <input type="password" name="confirm_password" required>
            </label>

            <button class="btn btn-primary" type="submit">Create Account</button>
        </form>

        <div style="margin-top:1rem; text-align:center;">
            Already have an account? <a href="login.php">Log in</a>
        </div>
<hr style="margin:1.4rem 0; opacity:0.3;">

<a href="google_login.php" class="btn btn-outline" style="width:100%; text-align:center;">
    <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" 
         style="height:18px; vertical-align:middle; margin-right:6px;">
    Sign Up with Google
</a>

    </div>
</div>

<?php include 'footer.php'; ?>
