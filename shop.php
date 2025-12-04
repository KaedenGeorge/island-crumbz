<?php
require_once 'config.php';
include 'header.php';

// Fetch all products
$result = $conn->query("SELECT id, name, description, price FROM products");
?>

<h1>Shop</h1>
<p>Select your cakes and Ponche Crème below and add them to your cart.</p>

<div class="cards-grid">
<?php while ($row = $result->fetch_assoc()): ?>
    <div class="card">
        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
        <p><?php echo htmlspecialchars($row['description']); ?></p>
        <p><strong>$<?php echo number_format($row['price'], 2); ?></strong></p>

        <form method="post" action="add_to_cart.php" class="add-to-cart-form">
            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
            <label>
                Qty
                <input type="number" name="quantity" value="1" min="1" style="width:60px;">
            </label>
            <button class="btn btn-primary" type="submit">Add to Cart</button>
        </form>
    </div>
<?php endwhile; ?>
</div>

<?php include 'footer.php'; ?>
