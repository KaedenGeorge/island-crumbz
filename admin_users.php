<?php
require_once 'config.php';
if (session_status()===PHP_SESSION_NONE) session_start();

if ($_SESSION['user_role'] !== 'admin') die("Access denied");

// Promote user
if (isset($_GET['promote'])) {
    $uid = $_GET['promote'];
    $conn->query("UPDATE users SET role='admin' WHERE id=$uid");
    header("Location: admin_users.php");
    exit;
}

$users = $conn->query("SELECT * FROM users ORDER BY id DESC");

include 'header.php';
?>

<div class="container">
    <h1>Manage Users</h1>

    <table class="cart-table" style="margin-top:1rem;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th></th>
            </tr>
        </thead>
        <tbody>

        <?php while($u = $users->fetch_assoc()): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['first_name']." ".$u['last_name']) ?></td>
                <td><?= $u['email'] ?></td>
                <td><?= $u['role'] ?></td>
                <td>
                    <?php if ($u['role'] !== 'admin'): ?>
                        <a class="btn btn-outline" href="admin_users.php?promote=<?= $u['id'] ?>">Promote</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>

        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>
