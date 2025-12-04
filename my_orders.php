<?php
require_once 'config.php';
requireLogin(); // User must be logged in

include 'header.php';

// Fetch user's orders
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id, total, status, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();
?>

<h1>My Orders</h1>

<?php if ($orders->num_rows === 0): ?>
    <p>You haven’t placed any orders yet.</p>
    <a class="btn btn-primary" href="shop.php">Start Shopping</a>

<?php else: ?>

<div class="orders-list">
    <?php while ($order = $orders->fetch_assoc()): ?>
        <div class="order-card">
            <div class="order-header">
                <div>
                    <h3>Order #<?php echo $order['id']; ?></h3>
                    <p class="order-date">Placed on: <?php echo $order['created_at']; ?></p>
                </div>
                <div class="order-summary-right">
                    <span class="order-status <?php echo strtolower($order['status']); ?>">
                        <?php echo ucfirst($order['status']); ?>
                    </span>
                    <p class="order-total">$<?php echo number_format($order['total'], 2); ?></p>
                </div>
            </div>

            <!-- Expand button -->
            <button class="btn btn-outline toggle-details" data-target="details-<?php echo $order['id']; ?>">
                View Items
            </button>

            <!-- Hidden items section -->
            <div id="details-<?php echo $order['id']; ?>" class="order-items hidden">
                <?php
                $order_id = $order['id'];
                $itemStmt = $conn->prepare("
                    SELECT p.name, oi.quantity, oi.price_each
                    FROM order_items oi
                    JOIN products p ON p.id = oi.product_id
                    WHERE oi.order_id = ?
                ");
                $itemStmt->bind_param("i", $order_id);
                $itemStmt->execute();
                $items = $itemStmt->get_result();
                ?>

                <table class="order-table">
                    <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Price Each</th>
                        <th>Subtotal</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($i = $items->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($i['name']); ?></td>
                            <td><?php echo $i['quantity']; ?></td>
                            <td>$<?php echo number_format($i['price_each'], 2); ?></td>
                            <td>$<?php echo number_format($i['price_each'] * $i['quantity'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        </div>
    <?php endwhile; ?>
</div>

<?php endif; ?>

<?php include 'footer.php'; ?>

