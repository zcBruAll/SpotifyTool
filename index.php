<?php
session_start(); // Start session if not already started

// Check if user info exists in session
if (!isset($_SESSION['access_token'])) {
    // Redirect to login page if access token is not found
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spotify Tool</title>
    <link rel="shortcut icon" href="images/spotify_icon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/index.css">
</head>
<body>

<!-- Include player.php -->
<?php include 'tiles/user.php'; ?>
<?php include 'tiles/player.php'; ?>
<?php include 'tiles/upcoming.php'; ?>

</body>
</html>