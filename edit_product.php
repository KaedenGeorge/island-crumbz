<?php
require_once 'config.php';
if (session_status()===PHP_SESSION_NONE) session_start();

if ($_SESSION['user_role'] !== 'admin') die("Access denied");

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) die("Product not found");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $cat   = $_POST['category_id'];
    $image = $product['image'];

    if (!empty($_FILES['image']['name'])) {
        $file = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/".$file);
        $image = "uploads/".$file;
    }

    $u = $conn->prepare("UPDATE products SET name=?,description=?,price=?,image=?,category_id=? WHERE id=?");
    $u->bind_param("ssdssi", $name,$desc,$price,$image,$cat,$id);
    $u->execute();

    header("Location: admin_products.php?updated=1");
    exit;
}

include 'header.php';
?>

<div class="container">
    <h1>Edit Product</h1>

    <form method="post" enctype="multipart/form-data" style="display:grid; gap:1rem; max-width:450px;">

        <label>Name
            <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
        </label>

        <label>Description
            <textarea name="description" required><?= htmlspecialchars($product['description']) ?></textarea>
        </label>

        <label>Price
            <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required>
        </label>

        <label>Category
            <input type="number" name="category_id" value="<?= $product['category_id'] ?>">
        </label>

        <label>Current Image:</label>
        <img src="<?= $product['image'] ?>" style="height:100px; margin-bottom:1rem;">

        <label>New Image (optional)
            <input type="file" name="image">
        </label>

        <button class="btn btn-primary">Save Changes</button>
    </form>
</div>

<?php include 'footer.php'; ?>
