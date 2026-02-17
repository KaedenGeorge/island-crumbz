<?php
// FILE: stripe_config.php

require_once 'vendor/autoload.php';

// 1. Log into your Stripe Dashboard
// 2. Get your Secret Key (starts with sk_test_...) and Publishable Key (pk_test_...)
// 3. Paste them below inside the quotes.

$stripeSecretKey = "sk_test_YOUR_ACTUAL_SECRET_KEY_HERE";
$stripePublishableKey = "pk_test_YOUR_ACTUAL_PUBLISHABLE_KEY_HERE";

function stripeClient() {
    global $stripeSecretKey;
    return new \Stripe\StripeClient($stripeSecretKey);
}

// This variable is used in checkout.php for the frontend
$stripe_public_key = $stripePublishableKey;
?>