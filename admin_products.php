<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if ($_SESSION['user_role'] !== 'admin') {
    die("Access denied");
}

$result = $conn->query("SELECT * FROM products ORDER BY id DESC");

include 'header.php';
?>

<div class="container manage-products">
    <h1>Manage Products</h1>

    <a href="add_product.php" class="btn btn-primary">Add Product</a>

    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Img</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
            <?php while ($p = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td>
                        <?php if ($p['image']): ?>
                            <img src="<?= $p['image'] ?>" class="product-thumb">
                        <?php endif; ?>
                    </td>
                    <td>$<?= number_format($p['price'], 2) ?></td>
                    <td><?= $p['category_id'] ?></td>
                    <td class="actions">
                        <a class="btn btn-outline" href="edit_product.php?id=<?= $p['id'] ?>">Edit</a>
                        <a class="btn btn-outline delete-btn"
                           href="delete_product.php?id=<?= $p['id'] ?>"
                           onclick="return confirm('Delete this product?')">
                            Delete
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>
