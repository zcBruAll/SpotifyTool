<!-- player.php -->
<link rel="stylesheet" href="css/global.css">
<link rel="stylesheet" href="css/tiles/user.css">

<div id="user" class="tile">
    <h1 id="display-name"></h1>
    <img id="profile-pic" alt="Profile picture">
    <p id="email"></p>
    <p id="product"></p>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Function to update player information
    $.ajax({
        url: 'https://api.spotify.com/v1/me', // Endpoint to fetch current track information
        method: 'GET',
        headers: {
            'Authorization': 'Bearer <?php echo $_SESSION['access_token']; ?>'
        },
        success: function(response) {
            // Update player interface with track information
            $('#display-name').text(response?.display_name);

            $('#profile-pic').attr('src', response?.images[1].url);

            $('#email').text(response?.email);

            $('#product').text(response?.product);
        },
        error: function(xhr, status, error) {
            console.error('Error getting user info:', error);
        }
    });
});
</script>
