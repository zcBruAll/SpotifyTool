<?php
function refreshAccessToken($refreshToken) {
    $client_id = '858d35f633844f64a1acc72d17c9d63c';
    $client_secret = '154e0018249145aeb99d86a5df80a281';

    $url = 'https://accounts.spotify.com/api/token';
    $data = [
        'grant_type' => 'refresh_token',
        'refresh_token' => $refreshToken,
        'client_id' => $client_id,
        'client_secret' => $client_secret,
    ];

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        die('Error refreshing token: ' . curl_error($ch));
    }

    curl_close($ch);

    return json_decode($result, true);
}
?>
