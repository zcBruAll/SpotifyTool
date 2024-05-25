<?php
session_start();
require 'refresh_token.php';

function getAccessToken() {
    if (time() > $_SESSION['token_expires']) {
        // Token is expired, refresh it
        $refreshToken = $_SESSION['refresh_token'];
        $newTokens = refreshAccessToken($refreshToken);
        $_SESSION['access_token'] = $newTokens['access_token'];
        $_SESSION['token_expires'] = time() + $newTokens['expires_in'];
    }

    return $_SESSION['access_token'];
}

// Return a valid access token
header('Content-Type: application/json');
echo json_encode(['access_token' => getAccessToken()]);
?>
