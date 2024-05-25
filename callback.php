<?php
session_start();

if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $client_id = '858d35f633844f64a1acc72d17c9d63c';
    $client_secret = '154e0018249145aeb99d86a5df80a281';
    $redirect_uri = 'https://project.zeacold.com/SpotifyTool/callback.php';

    $token_url = 'https://accounts.spotify.com/api/token';
    $data = [
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => $redirect_uri,
        'client_id' => $client_id,
        'client_secret' => $client_secret
    ];

    // Initialize cURL
    $ch = curl_init();

    // Set the URL and other options
    curl_setopt($ch, CURLOPT_URL, $token_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);

    // Execute the request and fetch the response
    $response = curl_exec($ch);

    // Check for errors
    if ($response === false) {
        die('Error getting access token: ' . curl_error($ch));
    }

    // Close cURL resource
    curl_close($ch);

    $response = json_decode($response, true);

    // Check for errors in the response
    if (isset($response['error'])) {
        die('Error in token response: ' . $response['error'] . ' - ' . $response['error_description']);
    }

    // Ensure access token is set
    if (!isset($response['access_token'])) {
        die('Error: Access token not found in response');
    }

    $_SESSION['access_token'] = $response['access_token'];

    header('Location: index.php');
    exit();
} else {
    echo 'Authorization code not found';
}
?>
