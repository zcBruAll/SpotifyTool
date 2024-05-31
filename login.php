<?php

$client_id = '858d35f633844f64a1acc72d17c9d63c';
$redirect_uri = 'https://project.zeacold.com/SpotifyTool/callback.php';
$scopes = 'user-read-private user-read-email user-read-currently-playing user-read-playback-state user-modify-playback-state user-library-read user-library-modify playlist-read-private';
$state = 'some_random_state';  // Generate a random state for security

$auth_url = 'https://accounts.spotify.com/authorize' .
    '?response_type=code' .
    '&client_id=' . $client_id .
    '&scope=' . urlencode($scopes) .
    '&redirect_uri=' . urlencode($redirect_uri);

header('Location: ' . $auth_url);
exit();
?>
