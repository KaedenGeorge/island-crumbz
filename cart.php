<?php
require_once 'config.php';

// Handle updates / remove / checkout
$message = "";

// UPDATE QUANTITIES
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
        foreach ($_POST['quantities'] as $index => $qty) {
            $qty = (int) $qty;
            if ($qty <= 0) {
                // Remove item if qty is 0 or less
                unset($_SESSION['cart'][$index]);
            } else {
                $_SESSION['cart'][$index]['quantity'] = $qty;
            }
        }
        // reindex array
        $_SESSION['cart'] = array_values($_SESSION['cart']);
        $message = "Cart updated.";
    }
}

// REMOVE SINGLE ITEM
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_item'])) {
    $index = (int) $_POST['remove_item'];
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
        $message = "Item removed from cart.";
    }
}

// CHECKOUT (place order)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    if (!isLoggedIn()) {
        // Force login before checkout
        header("Location: login.php");
        exit;
    }

    if (empty($_SESSION['cart'])) {
        $message = "Your cart is empty.";
    } else {
        $user_id = $_SESSION['user_id'];
        $total = 0;

        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Insert into orders
        $status = "pending";
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total, status) VALUES (?, ?, ?)");
        $stmt->bind_param("ids", $user_id, $total, $status);
        $stmt->execute();
        $order_id = $stmt->insert_id;
        $stmt->close();

        // Insert each item into order_items
        $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_each) VALUES (?, ?, ?, ?)");
        foreach ($_SESSION['cart'] as $item) {
            $pid = $item['id'];
            $qty = $item['quantity'];
            $price_each = $item['price'];

            $stmtItem->bind_param("iiid", $order_id, $pid, $qty, $price_each);
            $stmtItem->execute();
        }
        $stmtItem->close();

        // Clear cart
        $_SESSION['cart'] = [];
        $message = "Order placed successfully! Your order ID is #" . $order_id . ".";
    }
}

include 'header.php';

// Calculate totals for display
$cart = $_SESSION['cart'];
$cart_total = 0;
foreach ($cart as $item) {
    $cart_total += $item['price'] * $item['quantity'];
}
?>

<h1>Your Cart</h1>

<?php if ($message): ?>
    <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<?php if (empty($cart)): ?>
    <p>Your cart is empty. <a href="shop.php">Browse the shop</a>.</p>
<?php else: ?>

<form method="post">
    <table class="cart-table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Price</th>
                <th style="width: 90px;">Qty</th>
                <th>Subtotal</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($cart as $index => $item): 
            $subtotal = $item['price'] * $item['quantity'];
        ?>
            <tr>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td>$<?php echo number_format($item['price'], 2); ?></td>
                <td>
                    <input type="number" name="quantities[<?php echo $index; ?>]" value="<?php echo $item['quantity']; ?>" min="0" style="width:60px;">
                </td>
                <td>$<?php echo number_format($subtotal, 2); ?></td>
                <td>
                    <button class="btn btn-outline" type="submit" name="remove_item" value="<?php echo $index; ?>">Remove</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <p class="cart-total">
        <strong>Total:</strong> $<?php echo number_format($cart_total, 2); ?>
    </p>

    <div class="cart-actions">
        <button class="btn btn-outline" type="submit" name="update_cart">Update Cart</button>
        <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>

    </div>
</form>

<?php endif; ?>

<?php include 'footer.php'; ?>
