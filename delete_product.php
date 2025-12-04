<?php
require_once 'config.php';
if (session_status()===PHP_SESSION_NONE) session_start();

if ($_SESSION['user_role'] !== 'admin') die("Access denied");

$id = $_GET['id'];

$del = $conn->prepare("DELETE FROM products WHERE id=?");
$del->bind_param("i",$id);
$del->execute();

header("Location: admin_products.php?deleted=1");
exit;
