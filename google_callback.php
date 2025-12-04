<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'vendor/autoload.php';
require_once 'config.php';

// Google client setup
$client = new Google_Client();
$client->setClientId('GOOGLE_CLIENT_ID');
$client->setClientSecret('GOOGLE_CLIENT_SECRET');
$client->setRedirectUri('GOOGLE_REDIRECT_URI');
$client->addScope("email");
$client->addScope("profile");

if (!isset($_GET['code'])) {
    echo "Google Login Error: Missing OAuth code.";
    exit;
}

$token = null;

try {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
} catch (Exception $e) {
    echo "Google Login Error: ".$e->getMessage();
    exit;
}

if (isset($token['error'])) {
    echo "Google Login Error: ".$token['error_description'];
    exit;
}

$client->setAccessToken($token);
$oauth = new Google_Service_Oauth2($client);
$googleUser = $oauth->userinfo->get();

// Extract Google data
$first = $googleUser->givenName;
$last = $googleUser->familyName;
$email = $googleUser->email;
$picture = $googleUser->picture;

// Check if user already exists
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // User exists — update photo
    $user = $result->fetch_assoc();

    $update = $conn->prepare("UPDATE users SET profile_photo = ? WHERE id = ?");
    $update->bind_param("si", $picture, $user['id']);
    $update->execute();

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['profile_photo'] = $picture;

} else {
    // New user → Create account
    $role = "user";
    $password = null; // Google accounts don't have passwords

    $insert = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, role, profile_photo)
                              VALUES (?, ?, ?, NULL, ?, ?)");
    $insert->bind_param("sssss", $first, $last, $email, $role, $picture);
    $insert->execute();

    $newID = $insert->insert_id;

    $_SESSION['user_id'] = $newID;
    $_SESSION['user_role'] = $role;
    $_SESSION['user_email'] = $email;
    $_SESSION['profile_photo'] = $picture;
}

header("Location: index.php");
exit;
?>
699751271962-viuk6o9nair59mr341404hvvk1pitn70.apps.googleusercontent.com