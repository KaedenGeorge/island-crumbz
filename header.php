<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/config.php";

$loggedIn = isset($_SESSION['user_id']);
$photo = $_SESSION['profile_photo'] ?? null;

function userInitials() {
    $fn = $_SESSION['first_name'] ?? '';
    $ln = $_SESSION['last_name'] ?? '';
    return ($fn || $ln) ? strtoupper(($fn[0] ?? '') . ($ln[0] ?? '')) : "U";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Island Crumbz</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        /* ------------------------------
           HAMBURGER MENU TRANSLUCENT
        ------------------------------*/
        .mobile-menu {
            position: fixed;
            top: 0;
            right: -320px;
            width: 280px;
            height: 100vh;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;

            /* TRANSLUCENT BACKGROUND */
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(30px) saturate(150%);
            -webkit-backdrop-filter: blur(30px) saturate(150%);

            border-left: 1px solid rgba(255,255,255,0.25);
            box-shadow: -4px 0 35px rgba(0,0,0,0.25);

            border-radius: 22px 0 0 22px;

            transition: right 0.33s cubic-bezier(.4,0,.2,1);
            z-index: 200;
        }

        /* Dark mode translucent */
        body.dark-mode .mobile-menu {
            background: rgba(15, 23, 42, 0.45);
            border-left: 1px solid rgba(255,255,255,0.12);
            backdrop-filter: blur(28px) saturate(180%);
        }

        .mobile-menu.open {
            right: 0;
        }

        .mobile-menu a {
            text-decoration: none;
            font-size: 1rem;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            backdrop-filter: blur(5px);
            transition: 0.25s;
        }

        body.dark-mode .mobile-menu a { color: #fff; }
        body:not(.dark-mode) .mobile-menu a { color: #1b1b1b; }

        .mobile-menu a:hover {
            background: rgba(255,255,255,0.2);
            transform: translateX(5px);
        }

        /* CLICK-OFF OVERLAY */
        .mobile-menu-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.55);
            backdrop-filter: blur(8px);
            opacity: 0;
            pointer-events: none;
            transition: 0.25s ease;
            z-index: 150;
        }

        .mobile-menu-overlay.open {
            opacity: 1;
            pointer-events: auto;
        }

        /* HAMBURGER BUTTON */
        .hamburger {
            font-size: 2rem;
            cursor: pointer;
            background: transparent;
            border: none;
            color: #fff;
            padding: 0.25rem 0.5rem;
            border-radius: 10px;
        }

        body.dark-mode .hamburger { color: #f1f1f1; }

        /* PROFILE IMAGE / INITIALS BUTTON */
        .profile-btn img,
        .profile-btn .initials {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            cursor: pointer;
        }

        .profile-btn .initials {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ff7a00;
            color: #fff;
            font-weight: 700;
        }
    </style>

</head>

<body>

<!-- OVERLAY -->
<div id="menuOverlay" class="mobile-menu-overlay"></div>

<!-- SLIDE OUT HAMBURGER MENU -->
<div id="mobileMenu" class="mobile-menu">
    <a href="index.php">Home</a>
    <a href="cakes.php">Cakes</a>
    <a href="ponche-creme.php">Ponche Crème</a>
    <a href="shop.php">Shop</a>
    <a href="faq.php">FAQ</a>
    <a href="contact.php">Contact Us</a>
    <a href="about.php">About</a>
    <a href="cart.php">Cart (<?php echo getCartCount(); ?>)</a>

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

        <!-- LOGO -->
        <a href="index.php">
            <img src="assets/img/Mom's Logo.jpg" class="site-logo">
        </a>

        <div class="header-right">

            <!-- DARK MODE TOGGLE -->
            <button id="themeToggle">🌙</button>

            <!-- PROFILE BUTTON -->
            <?php if ($loggedIn): ?>
                <a href="profile.php" class="profile-btn">
                    <?php if ($photo): ?>
                        <!-- ✅ restored avatar class -->
                        <div class="avatar">
    <img src="<?php echo $_SESSION['profile_photo']; ?>" alt="Profile">
</div>

                    <?php else: ?>
                        <div class="initials"><?= userInitials(); ?></div>
                    <?php endif; ?>
                </a>
            <?php endif; ?>

            <!-- HAMBURGER -->
            <button class="hamburger" id="hamburgerBtn">☰</button>
        </div>
    </div>
</header>

<script>
// MENU OPEN/CLOSE
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

document.addEventListener("keydown", e => {
    if (e.key === "Escape") {
        menu.classList.remove("open");
        overlay.classList.remove("open");
    }
});

// DARK MODE TOGGLE
const toggle = document.getElementById("themeToggle");

toggle.onclick = () => {
    document.body.classList.toggle("dark-mode");
    localStorage.setItem(
        "theme",
        document.body.classList.contains("dark-mode") ? "dark" : "light"
    );
};

// Load theme on page load
if (localStorage.theme === "dark") {
    document.body.classList.add("dark-mode");
}
</script>

<main class="site-main">
<div class="container">
