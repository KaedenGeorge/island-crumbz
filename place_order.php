<?php
require_once 'config.php';
require_once 'stripe_config.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header('Location: cart.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: checkout.php');
    exit;
}

// Recalculate total (do not trust client value)
$cart_total = 0;
foreach ($cart as $item) {
    $cart_total += $item['price'] * $item['quantity'];
}

$user_id        = $_SESSION['user_id'];
$customer_name  = trim($_POST['customer_name'] ?? '');
$email          = trim($_POST['email'] ?? '');
$phone          = trim($_POST['phone'] ?? '');
$delivery_method = $_POST['delivery_method'] ?? 'pickup';
$payment_method  = $_POST['payment_method'] ?? 'cash';

$address_line1  = trim($_POST['address_line1'] ?? '');
$city           = trim($_POST['city'] ?? '');
$parish         = trim($_POST['parish'] ?? '');
$notes          = trim($_POST['notes'] ?? '');

$full_address = '';
if ($delivery_method === 'delivery') {
    $parts = array_filter([$address_line1, $city, $parish, $notes]);
    $full_address = implode(', ', $parts);
}

// Default order status
$status = 'pending';

// If paying by card, verify Stripe payment
if ($payment_method === 'card') {
    $intent_id = $_POST['stripe_payment_intent_id'] ?? null;

    if (!$intent_id) {
        die("Payment error: missing payment intent.");
    }

    $stripe = stripeClient();
    $intent = $stripe->paymentIntents->retrieve($intent_id, []);

    if ($intent->status === 'succeeded') {
        $status = 'paid';
    } else {
        die("Card payment not completed. Status: " . htmlspecialchars($intent->status));
    }
}

// Insert order
$stmt = $conn->prepare(
    "INSERT INTO orders (user_id, customer_name, email, phone, delivery_method, payment_method, address, total, status)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param(
    "issssssds",
    $user_id,
    $customer_name,
    $email,
    $phone,
    $delivery_method,
    $payment_method,
    $full_address,
    $cart_total,
    $status
);
$stmt->execute();
$order_id = $stmt->insert_id;
$stmt->close();

// Insert order items
$stmtItem = $conn->prepare(
    "INSERT INTO order_items (order_id, product_id, quantity, price_each)
     VALUES (?, ?, ?, ?)"
);

foreach ($cart as $item) {
    $pid   = (int)$item['id'];
    $qty   = (int)$item['quantity'];
    $price = (float)$item['price'];

    $stmtItem->bind_param("iiid", $order_id, $pid, $qty, $price);
    $stmtItem->execute();
}
$stmtItem->close();

// Clear cart
$_SESSION['cart'] = [];

// Redirect
header("Location: thankyou.php?order_id=" . $order_id);
exit;
