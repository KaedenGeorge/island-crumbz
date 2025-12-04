<?php
require_once 'config.php';
require_once 'stripe_config.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Fetch cart
$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header("Location: cart.php");
    exit;
}

// Calculate totals
$cart_total = 0;
foreach ($cart as $item) {
    $cart_total += $item['price'] * $item['quantity'];
}

// Create Stripe PaymentIntent
$stripe = stripeClient();
$paymentIntent = $stripe->paymentIntents->create([
    'amount' => (int) round($cart_total * 100),
    'currency' => 'usd',
    'automatic_payment_methods' => ['enabled' => true],
]);

$clientSecret = $paymentIntent->client_secret;

include 'header.php';
?>

<style>
/* -----------------------------------
   Island Crumbz Modal Theme
-------------------------------------*/
#card-modal.hidden { display: none; }

#card-modal {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.65);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 99999;
}

.card-modal-box {
    background: #fff7ef;
    padding: 1.8rem;
    border-radius: 16px;
    width: 100%;
    max-width: 400px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.25);
    border: 3px solid #ff8c00;
    animation: popIn 0.25s ease-out;
}

@keyframes popIn {
    from { transform: scale(.9); opacity: 0; }
    to   { transform: scale(1); opacity: 1; }
}

.card-modal-box h2 {
    margin-top: 0;
    color: #e36b00;
    font-weight: 700;
    text-align: center;
}

#card-element {
    padding: 12px;
    border: 2px solid #ffb15e;
    border-radius: 8px;
    background: #fff;
}

#card-errors {
    margin-top: 8px;
    color: red;
    text-align: center;
    font-size: 0.9rem;
}

.btn-primary {
    background: linear-gradient(to right, #ff9800, #e06d00);
    border: none;
    padding: 10px 16px;
    border-radius: 10px;
    color: white;
    cursor: pointer;
    font-weight: bold;
}

.btn-outline {
    background: white;
    border: 2px solid #e06d00;
    color: #e06d00;
    padding: 10px 16px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: bold;
}

.btn-outline:hover {
    background: #ffe1c4;
}
</style>



<div class="container">
    <h1>Checkout</h1>

    <h2>Order Summary</h2>
    <table class="cart-table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Line Total</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($cart as $item): 
            $line = $item['price'] * $item['quantity'];
        ?>
            <tr>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td><?php echo (int)$item['quantity']; ?></td>
                <td>$<?php echo number_format($item['price'], 2); ?></td>
                <td>$<?php echo number_format($line, 2); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <p class="cart-total"><strong>Total:</strong> $<?php echo number_format($cart_total, 2); ?></p>

    <!-- Checkout Form -->
    <form id="checkout-form" method="post" action="place_order.php" autocomplete="off">

        <!-- Autofill trap -->
        <input type="text" name="fake-fill" style="display:none" autocomplete="off">

        <h2>Your Details</h2>

        <label>Full Name
            <input type="text" name="customer_name" autocomplete="off" required>
        </label>

        <label>Email
            <input type="email" name="email" autocomplete="off" required>
        </label>

        <label>Phone
            <input type="text" name="phone" autocomplete="off">
        </label>

        <h2>Delivery Method</h2>
        <label><input type="radio" name="delivery_method" value="pickup" required> Pickup</label><br>
        <label><input type="radio" name="delivery_method" value="delivery" required> Delivery</label>

        <div id="address-box" style="display:none; margin-top:1rem;">
            <label>Street Address
                <input type="text" name="address_line1">
            </label>
            <label>City / Town
                <input type="text" name="city">
            </label>
            <label>Parish
                <input type="text" name="parish">
            </label>
            <label>Delivery Notes
                <textarea name="notes"></textarea>
            </label>
        </div>

        <h2>Payment Method</h2>
        <label><input type="radio" name="payment_method" value="cash" required> Pay In Person</label><br>
        <label><input type="radio" name="payment_method" value="card" required> Pay With Card</label>

        <div id="card-section" class="hidden" style="margin-top:1rem;">
            <button type="button" class="btn-primary" id="open-card-modal">Enter Card Details</button>
            <input type="hidden" name="stripe_payment_intent_id"
                value="<?php echo htmlspecialchars($paymentIntent->id); ?>">
        </div>

        <button type="submit" class="btn-primary" style="margin-top:1rem;">Place Order</button>
        <input type="hidden" name="total_confirm" value="<?php echo htmlspecialchars($cart_total); ?>">
    </form>
</div>


<!-- CARD PAYMENT MODAL -->
<div id="card-modal" class="hidden">
    <div class="card-modal-box">
        <h2>Enter Card Details</h2>

        <form id="card-form">
            <div id="card-element"></div>
            <div id="card-errors"></div>

            <button type="submit" class="btn-primary" style="margin-top:1rem;">Pay Now</button>
            <button type="button" class="btn-outline" id="close-card-modal" style="margin-top:0.5rem;">Cancel</button>
        </form>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
// Show/hide delivery address box
document.querySelectorAll("input[name='delivery_method']").forEach(radio => {
    radio.addEventListener("change", () => {
        document.getElementById("address-box").style.display =
            (radio.value === "delivery") ? "block" : "none";
    });
});

// Payment selection
const cardSection = document.getElementById("card-section");
document.querySelectorAll("input[name='payment_method']").forEach(radio => {
    radio.addEventListener("change", () => {
        cardSection.classList.toggle("hidden", radio.value !== "card");
    });
});

// Stripe Setup
const stripe = Stripe("<?php echo $stripe_public_key; ?>");
const elements = stripe.elements();
let card = null;

// Open modal
document.getElementById("open-card-modal").addEventListener("click", () => {
    if (!card) {
        card = elements.create("card");
        card.mount("#card-element");
    }
    document.getElementById("card-modal").classList.remove("hidden");
});

// Close modal
document.getElementById("close-card-modal").addEventListener("click", () => {
    document.getElementById("card-modal").classList.add("hidden");
});

// Handle Stripe Payment
document.getElementById("card-form").addEventListener("submit", async (e) => {
    e.preventDefault();
    const clientSecret = "<?php echo $clientSecret; ?>";
    const {error, paymentIntent} = await stripe.confirmCardPayment(clientSecret, {
        payment_method: { card }
    });

    if (error) {
        document.getElementById("card-errors").textContent = error.message;
        return;
    }

    if (paymentIntent.status === "succeeded") {
        document.getElementById("card-modal").classList.add("hidden");
        document.getElementById("checkout-form").submit();
    }
});
</script>

<?php include 'footer.php'; ?>
