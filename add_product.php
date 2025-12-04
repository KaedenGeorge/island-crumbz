<?php
require_once 'config.php';
if (session_status()===PHP_SESSION_NONE) session_start();
if ($_SESSION['user_role'] !== 'admin') die("Access denied");

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $cat   = $_POST['category_id'];

    $imagePath = "";

    if (!empty($_FILES['image']['name'])) {
        $file = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $file);
        $imagePath = "uploads/".$file;
    }

    $stmt = $conn->prepare("INSERT INTO products (name,description,price,image,category_id) VALUES (?,?,?,?,?)");
    $stmt->bind_param("ssdss",$name,$desc,$price,$imagePath,$cat);
    $stmt->execute();

    $message = "Product Added!";
}

include 'header.php';
?>

<div class="container">
    <h1>Add Product</h1>
    <?php if($message): ?><div class="alert alert-info"><?= $message ?></div><?php endif; ?>

    <form method="post" enctype="multipart/form-data" style="display:grid; gap:1rem; max-width:450px;">

        <label>Name<input type="text" name="name" required></label>
        <label>Description<textarea name="description" required></textarea></label>
        <label>Price<input type="number" step="0.01" name="price" required></label>
        <label>Category<input type="number" name="category_id" value="1"></label>
        <label>Image<input type="file" name="image" required></label>

        <button class="btn btn-primary">Add Product</button>
    </form>
</div>

<?php include 'footer.php'; ?>
