<?php
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ============================================
// HANDLE PROFILE UPDATE
// ============================================
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['first_name'])) {

    $first = trim($_POST['first_name']);
    $last  = trim($_POST['last_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address_line']);
    $city  = trim($_POST['city']);
    $parish = trim($_POST['parish']);

    $stmt = $conn->prepare("
        UPDATE users 
        SET first_name=?, last_name=?, phone=?, address_line=?, city=?, parish=?
        WHERE id=?
    ");
    $stmt->bind_param("ssssssi", $first, $last, $phone, $address, $city, $parish, $user_id);
    $stmt->execute();

    $message = "Profile updated successfully!";
}

// ============================================
// FETCH USER INFO
// ============================================
$stmt = $conn->prepare("SELECT * FROM users WHERE id=? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

include 'header.php';
?>

<div class="container" style="margin-top:2rem; max-width:800px;">

    <!-- ========================= PROFILE CARD ========================= -->
    <div class="profile-section">
        <h1>Your Profile</h1>

        <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="post" class="profile-form">

            <!-- FIRST + LAST NAME ROW -->
            <div class="profile-row">
                <label class="profile-row-item">First Name
                    <input type="text" name="first_name" 
                           value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                </label>

                <label class="profile-row-item">Last Name
                    <input type="text" name="last_name" 
                           value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                </label>
            </div>

            <label>Email (cannot change)
                <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
            </label>

            <label>Phone Number
                <input type="text" name="phone"
                       value="<?php echo htmlspecialchars($user['phone']); ?>">
            </label>

            <label>Address
                <input type="text" name="address_line"
                       value="<?php echo htmlspecialchars($user['address_line']); ?>">
            </label>

            <label>City / Town
                <input type="text" name="city"
                       value="<?php echo htmlspecialchars($user['city']); ?>">
            </label>

            <label>Parish
                <input type="text" name="parish"
                       value="<?php echo htmlspecialchars($user['parish']); ?>">
            </label>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>

    <!-- ======================= PASSWORD CARD ======================= -->
    <div class="password-section" style="margin-top:2rem;">
        <h2>Change Password</h2>

        <form method="post" action="update_password.php" class="profile-form">

            <label>Current Password
                <input type="password" name="old_password" required>
            </label>

            <label>New Password
                <input type="password" name="new_password" required>
            </label>

            <label>Confirm New Password
                <input type="password" name="confirm_password" required>
            </label>

            <button type="submit" class="btn btn-outline">Update Password</button>
        </form>
    </div>

</div>

<?php include 'footer.php'; ?>
