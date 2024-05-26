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

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ],
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if ($result === FALSE) {
        die('Error refreshing token');
    }

    return json_decode($result, true);
}
?>
