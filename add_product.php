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
                $imagePath = $targetFile;
            } else {
                $message = "Error uploading image.";
            }
        } else {
            $message = "Invalid file type. Allowed: JPG, JPEG, PNG, GIF, WEBP.";
        }
    }

    if (empty($message)) {
        $stmt = $conn->prepare("INSERT INTO products (name,description,price,image,category_id) VALUES (?,?,?,?,?)");
        $stmt->bind_param("ssdss", $name, $desc, $price, $imagePath, $cat);

        if ($stmt->execute()) {
            $message = "Product Added!";
        } else {
            $message = "Database error: " . $conn->error;
        }
    }
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
