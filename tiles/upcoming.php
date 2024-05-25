<link rel="stylesheet" href="css/global.css">
<link rel="stylesheet" href="css/tiles/upcoming.css">
<div id="upcoming" class="tile">
    <h2>Upcoming tracks</h2>
    <ul id="upcoming-tracks">
    </ul>
    <div id="arrows">
        <img src="images/up-arrow.png" class="btn" alt="up" onclick="decreaseTracks()">
        <img src="images/down-arrow.png" class="btn" alt="down" onclick="increaseTracks()">
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let tracksToShow = 3;

    function getAccessToken() {
        return $.ajax({
            url: 'get_access_token.php',
            method: 'GET',
            dataType: 'json'
        });
    }

    function updateUpcoming() {
        getAccessToken().then(function(data) {
            const accessToken = data.access_token;

            $.ajax({
                url: 'https://api.spotify.com/v1/me/player/queue', // Endpoint to fetch current track information
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + accessToken
                },
                success: function(response) {
                    if (response && response.queue && response.queue.length > 0) {
                        $('#upcoming-tracks').empty();

                        var idx = 0;

                        response.queue.forEach(element => {
                            idx++;

                            if (idx <= tracksToShow) {
                                const trackItem = $('<li class="track-item"></li>');
                                const trackPic = $('<img class="track-pic" alt="Track picture">').attr('src', element.album.images[0].url);
                                const trackInfo = $('<div class="track-info"></div>');
                                const trackName = $('<a class="track-name" target="_blank"></a>')
                                    .text(element.name)
                                    .attr('href', element.external_urls.spotify);

                                const trackArtists = $('<ul class="artists"></ul>');
                                for (let i = 0; i < element.artists.length; i++) {
                                    trackArtists.append('<li class="artist_li"><a href="' + element.artists[i].external_urls.spotify + '" target="_blank" >' + element.artists[i].name + '</a></li>');
                                    if (i < element.artists.length - 1) {
                                        trackArtists.append('<span> - </span>');
                                    }
                                }

                                trackInfo.append(trackName).append(trackArtists);
                                trackItem.append(trackPic).append(trackInfo);
                                $('#upcoming-tracks').append(trackItem);
                            }
                        });
                    } else {
                        $('#player').addClass('hidden');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching current track:', error);
                }
            });
        }).catch(function(error) {
            console.error('Error getting access token:', error);
        });
    }

    function increaseTracks() {
        if (tracksToShow <= 7) {
            tracksToShow += 2;
            updateUpcoming();
        }
    }

    function decreaseTracks() {
        if (tracksToShow >= 3) {
            tracksToShow -= 2;
            updateUpcoming();
        }
    }

    updateUpcoming();

    setInterval(updateUpcoming, 30000);
</script>