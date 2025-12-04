<?php
/**
 * ------------------------------------------------------------
 *  CONFIGURATION EXAMPLE FILE
 * ------------------------------------------------------------
 *  
 * This file provides placeholder values for database and OAuth
 * configuration so it can be safely committed to GitHub.
 * 
 * DO NOT USE THIS FILE IN PRODUCTION.
 * 
 * Copy this file to:
 *      config.php
 * and replace the placeholder values with your real credentials.
 * 
 * ------------------------------------------------------------
 */

// Start session for login, cart, user data, etc.
session_start();

/* ------------------------------------------------------------
 *  DATABASE SETTINGS (PLACEHOLDERS)
 * ------------------------------------------------------------ */
$host = "YOUR_DB_HOST";        // e.g. localhost or server IP
$user = "YOUR_DB_USERNAME";    // e.g. cpanel_user123
$pass = "YOUR_DB_PASSWORD";    // e.g. strongpassword123
$db   = "YOUR_DB_NAME";        // e.g. islandcrumbz_db

// Create database connection (do not change)
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed.");
}

/* ------------------------------------------------------------
 *  GOOGLE OAUTH SETTINGS (PLACEHOLDERS)
 * ------------------------------------------------------------ */
$googleClientID     = "YOUR_GOOGLE_CLIENT_ID";
$googleClientSecret = "YOUR_GOOGLE_CLIENT_SECRET";
$googleRedirectURL  = "https://yourdomain.com/google-callback.php";

/* ------------------------------------------------------------
 *  SESSION INITIALIZATION
 * ------------------------------------------------------------ */

// Ensure cart exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = []; 
}

/* ------------------------------------------------------------
 *  AUTHENTICATION HELPERS
 * ------------------------------------------------------------ */

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

/* ------------------------------------------------------------
 *  CART HELPERS
 * ------------------------------------------------------------ */

function getCartCount() {
    $count = 0;
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $count += $item['quantity'];
        }
    }
    return $count;
}
