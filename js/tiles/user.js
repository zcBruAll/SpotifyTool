function getAccessToken() {
    return $.ajax({
        url: 'get_access_token.php',
        method: 'GET',
        dataType: 'json'
    });
}

$(document).ready(function() {
    // Function to update player information
    getAccessToken().then(function(data) {
        const accessToken = data.access_token;

        $.ajax({
            url: 'https://api.spotify.com/v1/me', // Endpoint to fetch current track information
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + accessToken
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
    }).catch(function(error) {
        console.error('Error getting access token:', error);
    });
});