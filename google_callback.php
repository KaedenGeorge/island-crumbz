<?php
require_once 'config.php';

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // 1. Exchange the authorization code for an access token
    $tokenParams = [
        'code'          => $code,
        'client_id'     => $googleCli834816762295-vujggr8n41gmu4rjq37rvngglearr6tc.apps.googleusercontent.comentID,
        'client_secret' => $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,
        'redirect_uri'  => $googleRedirectURL,
        'grant_type'    => 'authorization_code'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/token');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenParams));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $tokenInfo = json_decode($response, true);

    if (isset($tokenInfo['access_token'])) {
        // 2. Get User Profile Data using the token
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/oauth2/v2/userinfo');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $tokenInfo['access_token']]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $userInfoResponse = curl_exec($ch);
        curl_close($ch);

        $googleUser = json_decode($userInfoResponse, true);

        // 3. Process User (Login or Signup)
        $email = $googleUser['email'];
        $firstName = $googleUser['given_name'];
        $lastName = $googleUser['family_name'];
        $picture = $googleUser['picture'];

        // Check if user exists in database
        $stmt = $conn->prepare("SELECT id, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // -- USER EXISTS: LOG THEM IN --
            $user = $result->fetch_assoc();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['first_name'] = $firstName;
            $_SESSION['last_name'] = $lastName;
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['profile_photo'] = $picture; // Use Google photo
            
            header("Location: index.php");
            exit;
        } else {
            // -- USER NEW: CREATE ACCOUNT --
            // Create a random password since they use Google
            $randomPass = password_hash(bin2hex(random_bytes(10)), PASSWORD_DEFAULT);
            $role = 'customer';

            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password_hash, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $firstName, $lastName, $email, $randomPass, $role);
            
            if ($stmt->execute()) {
                $_SESSION['user_id'] = $stmt->insert_id;
                $_SESSION['first_name'] = $firstName;
                $_SESSION['last_name'] = $lastName;
                $_SESSION['user_role'] = $role;
                $_SESSION['profile_photo'] = $picture;

                header("Location: index.php");
                exit;
            } else {
                die("Error creating account: " . $conn->error);
            }
        }
    } else {
        die("Error fetching token from Google.");
    }
} else {
    // If someone tries to visit this page without a code, send them back to login
    header("Location: login.php");
    exit;
}
?>