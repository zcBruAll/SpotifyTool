<?php
session_start();

$client_id = '858d35f633844f64a1acc72d17c9d63c';
$client_secret = '154e0018249145aeb99d86a5df80a281';
$redirect_uri = 'https://project.zeacold.com/SpotifyTool/callback.php';

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    $url = 'https://accounts.spotify.com/api/token';
    $data = [
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => $redirect_uri,
        'client_id' => $client_id,
        'client_secret' => $client_secret,
    ];

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_RETURNTRANSFER => true,
    ];

    $curl = curl_init();
    curl_setopt_array($curl, $options);
    $response = curl_exec($curl);
    if ($response === FALSE) {
        die('Error');
    }

    $body = json_decode($response, true);

    // Store access and refresh tokens in session
    $_SESSION['access_token'] = $body['access_token'];
    $_SESSION['refresh_token'] = $body['refresh_token'];
    $_SESSION['token_expires'] = time() + $body['expires_in'];

    // Redirect to your profile page or wherever you want
    header('Location: index.php');
    exit();
} else {
    // Handle errors
    echo "Authorization failed.";
}
?>
