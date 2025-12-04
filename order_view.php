<?php
require_once 'config.php';
if (session_status()===PHP_SESSION_NONE) session_start();
if ($_SESSION['user_role'] !== 'admin') die("Access denied");

$id = $_GET['id'];

$order = $conn->query("SELECT * FROM orders WHERE id=$id")->fetch_assoc();
$items = $conn->query("SELECT order_items.*, products.name 
                       FROM order_items 
                       JOIN products ON products.id = order_items.product_id
                       WHERE order_id=$id");

include 'header.php';
?>

<div class="container">
    <h1>Order #<?= $id ?></h1>

    <p><strong>Status:</strong> <?= $order['status'] ?></p>
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
                <td><?= $i['name'] ?></td>
                <td><?= $i['quantity'] ?></td>
                <td>$<?= number_format($i['price_each'],2) ?></td>
                <td>$<?= number_format($i['quantity'] * $i['price_each'],2) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>
