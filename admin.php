<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Access denied.");
}

include 'header.php';
?>

<style>
.admin-wrapper {
    max-width: 1100px;
    margin: 2rem auto;
    padding: 1rem;
}
.admin-title {
    font-size: 2rem;
    color: #ff7f00;
    margin-bottom: 1.5rem;
    font-weight: bold;
}
.admin-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 1.2rem;
}
.admin-card {
    background: #fff;
    padding: 1.4rem;
    border-radius: 14px;
    border-left: 6px solid #ff7f00;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    cursor: pointer;
    transition: 0.2s ease;
}
.admin-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.25);
}
.admin-card h3 {
    margin: 0 0 0.4rem;
}
.admin-card p {
    opacity: 0.7;
}
</style>

<div class="admin-wrapper">

    <h1 class="admin-title">Admin Dashboard</h1>

    <div class="admin-cards">

        <div class="admin-card" onclick="location.href='admin_products.php'">
            <h3>🍰 Manage Products</h3>
            <p>View, edit and delete products</p>
        </div>

        <div class="admin-card" onclick="location.href='add_product.php'">
            <h3>➕ Add Product</h3>
            <p>Add new items to your bakery</p>
        </div>

        <div class="admin-card" onclick="location.href='orders_admin.php'">
            <h3>📦 Manage Orders</h3>
            <p>View and update customer orders</p>
        </div>

        <div class="admin-card" onclick="location.href='admin_users.php'">
            <h3>👤 Manage Users</h3>
            <p>Promote, view, or manage users</p>
        </div>

    </div>
</div>

<?php include 'footer.php'; ?>
