<?php
require_once 'config.php';
if (session_status()===PHP_SESSION_NONE) session_start();
if ($_SESSION['user_role'] !== 'admin') die("Access denied");

$id = $_GET['id'];

// FIX: Use prepared statement for Order
$stmt = $conn->prepare("SELECT * FROM orders WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) die("Order not found");

// FIX: Use prepared statement for Items
$stmtItems = $conn->prepare("SELECT order_items.*, products.name 
                       FROM order_items 
                       JOIN products ON products.id = order_items.product_id
                       WHERE order_id=?");
$stmtItems->bind_param("i", $id);
$stmtItems->execute();
$items = $stmtItems->get_result();

include 'header.php';
?>

<div class="container">
    <h1>Order #<?= $id ?></h1>

    <p><strong>Status:</strong> <?= htmlspecialchars($order['status']) ?></p>
    <p><strong>Total:</strong> $<?= number_format($order['total'],2) ?></p>

    <h2>Items</h2>
    <table class="cart-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Price Each</th>
                <th>Line Total</th>
            </tr>
        </thead>
        <tbody>
        <?php while($i = $items->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($i['name']) ?></td>
                <td><?= $i['quantity'] ?></td>
                <td>$<?= number_format($i['price_each'],2) ?></td>
                <td>$<?= number_format($i['quantity'] * $i['price_each'],2) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>