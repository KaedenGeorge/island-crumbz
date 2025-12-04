<?php
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
if ($order_id <= 0) {
    header('Location: index.php');
    exit;
}

// Fetch order
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    header('Location: index.php');
    exit;
}

// Fetch items
$stmt = $conn->prepare(
    "SELECT oi.quantity, oi.price_each, p.name
     FROM order_items oi
     JOIN products p ON oi.product_id = p.id
     WHERE oi.order_id = ?"
);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result();
$stmt->close();

include 'header.php';
?>

<div class="container">
    <h1>Thank You!</h1>
    <p>Your order <strong>#<?php echo $order['id']; ?></strong> has been placed.</p>
    <p>Status: <strong><?php echo htmlspecialchars($order['status']); ?></strong></p>

    <?php if (!empty($order['delivery_method'])): ?>
        <p>Delivery Method: <strong><?php echo htmlspecialchars($order['delivery_method']); ?></strong></p>
    <?php endif; ?>

    <?php if (!empty($order['payment_method'])): ?>
        <p>Payment Method: <strong><?php echo htmlspecialchars($order['payment_method']); ?></strong></p>
    <?php endif; ?>

    <?php if (!empty($order['address'])): ?>
        <p>Delivery Address: <strong><?php echo nl2br(htmlspecialchars($order['address'])); ?></strong></p>
    <?php endif; ?>

    <h2>Items</h2>
    <table class="cart-table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Price Each</th>
                <th>Line Total</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = $items->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo (int)$item['quantity']; ?></td>
                    <td>$<?php echo number_format($item['price_each'], 2); ?></td>
                    <td>$<?php echo number_format($item['price_each'] * $item['quantity'], 2); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <p class="cart-total">
        <strong>Total:</strong> $<?php echo number_format($order['total'], 2); ?>
    </p>

    <p>We’ll be in touch soon with pickup/delivery details 💛</p>
</div>

<?php include 'footer.php'; ?>
