<?php
function refreshAccessToken($refreshToken) {
    $client_id = 'your_spotify_client_id';
    $client_secret = 'your_spotify_client_secret';

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
