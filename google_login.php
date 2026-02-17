<?php
require_once 'config.php';

// Prepare the Google Login URL
$params = [
    'response_type' => 'code',
    'client_id'     => $googleCli834816762295-vujggr8n41gmu4rjq37rvngglearr6tc.apps.googleusercontent.comentID,
    'redirect_uri'  => $googleRedirectURL,
    'scope'         => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
    'access_type'   => 'online',
    'prompt'        => 'select_account'
];

$url = 'https://accounts.google.com/o/oauth2/auth?' . http_build_query($params);

// Redirect user to Google
header("Location: " . $url);
exit;
?>