<?php
require_once 'config.php';
if (session_status()===PHP_SESSION_NONE) session_start();
if ($_SESSION['user_role'] !== 'admin') die("Access denied");

// Handle status update
if (isset($_POST['update_status'])) {
    $oid = $_POST['order_id'];
    $new = $_POST['status'];

    $u = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
    $u->bind_param("si", $new, $oid);
    $u->execute();

    header("Location: orders_admin.php?updated=1");
    exit;
}

$orders = $conn->query("SELECT orders.*, users.email FROM orders 
                        JOIN users ON users.id = orders.user_id
                        ORDER BY orders.id DESC");

include 'header.php';
?>

<div class="container">
    <h1>Manage Orders</h1>

    <table class="cart-table" style="margin-top:1rem;">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Total</th>
                <th>Status</th>
                <th>Items</th>
                <th>Update</th>
            </tr>
        </thead>
        <tbody>

        <?php while($o = $orders->fetch_assoc()): ?>
            <tr>
                <td><?= $o['id'] ?></td>
                <td><?= $o['email'] ?></td>
                <td>$<?= number_format($o['total'],2) ?></td>
                <td><?= $o['status'] ?></td>

                <td>
                    <a href="order_view.php?id=<?= $o['id'] ?>" class="btn btn-outline">View</a>
                </td>

                <td>
                    <form method="post">
                        <input type="hidden" name="order_id" value="<?= $o['id'] ?>">

                        <select name="status">
                            <option value="pending"   <?= $o['status']=="pending"?"selected":"" ?>>pending</option>
                            <option value="completed" <?= $o['status']=="completed"?"selected":"" ?>>completed</option>
                            <option value="cancelled" <?= $o['status']=="cancelled"?"selected":"" ?>>cancelled</option>
                        </select>

                        <button class="btn btn-primary" name="update_status">Save</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>
