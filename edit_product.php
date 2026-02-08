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
    $message = "";

    if (!empty($_FILES['image']['name'])) {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = basename($_FILES['image']['name']);
        // Sanitize filename
        $fileName = preg_replace("/[^a-zA-Z0-9\._-]/", "", $fileName);
        $targetFile = $uploadDir . time() . "_" . $fileName;

        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $image = $targetFile;
            } else {
                $message = "Error uploading image.";
            }
        } else {
            $message = "Invalid file type. Allowed: JPG, JPEG, PNG, GIF, WEBP.";
        }
    }

    if (empty($message)) {
        $u = $conn->prepare("UPDATE products SET name=?,description=?,price=?,image=?,category_id=? WHERE id=?");
        $u->bind_param("ssdssi", $name, $desc, $price, $image, $cat, $id);
        $u->execute();

        header("Location: admin_products.php?updated=1");
        exit;
    }
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
