<?php
require_once 'config.php';

// (Optional) Require login before adding to cart
// requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
    $quantity   = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;

    if ($product_id > 0 && $quantity > 0) {
        // Fetch product from DB
        $stmt = $conn->prepare("SELECT id, name, price FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();

        if ($product) {
            // If item already in cart, increase quantity
            $found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['id'] == $product['id']) {
                    $item['quantity'] += $quantity;
                    $found = true;
                    break;
                }
            }
            unset($item); // break reference

            // If not found, add new entry
            if (!$found) {
                $_SESSION['cart'][] = [
                    'id'       => $product['id'],
                    'name'     => $product['name'],
                    'price'    => (float) $product['price'],
                    'quantity' => $quantity
                ];
            }
        }
    }
}

// Redirect back to shop or cart
header("Location: cart.php");
exit;
