<?php
// 1. Safe Session Start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Load Config with error check
if (file_exists(__DIR__ . "/config.php")) {
    require_once __DIR__ . "/config.php";
}

// 3. Initialize variables to prevent "Undefined Variable" errors
$loggedIn = isset($_SESSION['user_id']);
$photo = $_SESSION['profile_photo'] ?? null;

// 4. Define Helper Functions locally if Config failed to load
if (!function_exists('userInitials')) {
    function userInitials() {
        $fn = $_SESSION['first_name'] ?? '';
        $ln = $_SESSION['last_name'] ?? '';
        return ($fn || $ln) ? strtoupper(($fn[0] ?? '') . ($ln[0] ?? '')) : "U";
    }
}

// Safely get cart count
$cartCount = function_exists('getCartCount') ? getCartCount() : 0;
?>
<!DOCTYPE html>
<html lang="en" data-theme="light-classic">
<head>
    <meta charset="UTF-8">
    <title>Island Crumbz</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        /* INLINE STYLES FOR THEME PICKER & HEADER */
        .theme-wrapper { position: relative; display: inline-block; margin-right: 15px; }
        .theme-btn { background: none; border: none; font-size: 1.5rem; cursor: pointer; padding: 5px; transition: transform 0.2s; }
        .theme-btn:hover { transform: scale(1.1); }
        .theme-dropdown { display: none; position: absolute; top: 100%; right: 0; background: #ffffff; min-width: 180px; box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2); border-radius: 12px; z-index: 300; overflow: hidden; border: 1px solid #eee; }
        [data-theme*="dark"] .theme-dropdown { background: #1e293b; border-color: #334155; }
        .theme-dropdown.show { display: block; }
        .theme-option { width: 100%; text-align: left; padding: 12px 16px; background: none; border: none; cursor: pointer; font-size: 0.9rem; display: flex; align-items: center; gap: 10px; color: #333; transition: background 0.2s; }
        [data-theme*="dark"] .theme-option { color: #fff; }
        .theme-option:hover { background-color: #f1f1f1; }
        [data-theme*="dark"] .theme-option:hover { background-color: #334155; }
        .theme-label { font-size: 0.75rem; color: #888; padding: 8px 16px 4px; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px; }
    </style>
</head>

<body>

<div id="menuOverlay" class="mobile-menu-overlay"></div>

<div id="mobileMenu" class="mobile-menu">
    <a href="index.php">Home</a>
    <a href="cakes.php">Cakes</a>
    <a href="ponche-creme.php">Ponche Crème</a>
    <a href="shop.php">Shop</a>
    <a href="faq.php">FAQ</a>
    <a href="contact.php">Contact Us</a>
    <a href="about.php">About</a>
    <a href="cart.php">Cart (<?php echo $cartCount; ?>)</a>

    <?php if ($loggedIn): ?>
        <a href="profile.php">My Profile</a>
        <a href="my_orders.php">My Orders</a>
        <?php if (!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
            <a href="admin.php">Admin Dashboard</a>
        <?php endif; ?>
        <a href="logout.php" style="color:#ff6666;">Logout</a>
    <?php else: ?>
        <a href="login.php">Login</a>
        <a href="signup.php">Sign Up</a>
    <?php endif; ?>
</div>

<header class="site-header">
    <div class="container header-inner">

        <a href="index.php">
            <img src="assets/img/Mom's Logo.jpg" class="site-logo" alt="Island Crumbz">
        </a>

        <div class="header-right">

            <div class="theme-wrapper">
                <button id="themeToggleBtn" class="theme-btn" title="Change Theme">🎨</button>
                <div id="themeDropdown" class="theme-dropdown">
                    <div class="theme-label">Light Themes</div>
                    <button class="theme-option" onclick="setTheme('light-classic')">☀️ Classic</button>
                    <button class="theme-option" onclick="setTheme('light-ocean')">🌊 Ocean Breeze</button>
                    <button class="theme-option" onclick="setTheme('light-sunset')">🌅 Golden Hour</button>
                    
                    <div class="theme-label">Dark Themes</div>
                    <button class="theme-option" onclick="setTheme('dark-midnight')">🌙 Midnight</button>
                    <button class="theme-option" onclick="setTheme('dark-forest')">🌴 Night Jungle</button>
                    <button class="theme-option" onclick="setTheme('dark-royal')">💜 Royal Velvet</button>
                </div>
            </div>

            <?php if ($loggedIn): ?>
                <a href="profile.php" class="profile-btn">
                    <?php if ($photo): ?>
                        <div class="avatar">
                            <img src="<?php echo htmlspecialchars($photo); ?>" alt="Profile">
                        </div>
                    <?php else: ?>
                        <div class="initials"><?= userInitials(); ?></div>
                    <?php endif; ?>
                </a>
            <?php endif; ?>

            <button class="hamburger" id="hamburgerBtn">☰</button>
        </div>
    </div>
</header>

<script>
// THEME LOGIC
const themeBtn = document.getElementById('themeToggleBtn');
const themeDropdown = document.getElementById('themeDropdown');

themeBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    themeDropdown.classList.toggle('show');
});

document.addEventListener('click', (e) => {
    if (!themeBtn.contains(e.target) && !themeDropdown.contains(e.target)) {
        themeDropdown.classList.remove('show');
    }
});

function setTheme(themeName) {
    document.documentElement.setAttribute('data-theme', themeName);
    localStorage.setItem('theme', themeName);
    themeDropdown.classList.remove('show');
}

const savedTheme = localStorage.getItem('theme') || 'light-classic';
document.documentElement.setAttribute('data-theme', savedTheme);

// MENU LOGIC
const menu = document.getElementById("mobileMenu");
const overlay = document.getElementById("menuOverlay");
const hamburger = document.getElementById("hamburgerBtn");

hamburger.onclick = () => {
    menu.classList.add("open");
    overlay.classList.add("open");
};

overlay.onclick = () => {
    menu.classList.remove("open");
    overlay.classList.remove("open");
};
</script>

<main class="site-main">
<div class="container">