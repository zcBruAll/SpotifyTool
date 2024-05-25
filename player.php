<!-- player.php -->
<div id="player">
    <h2>Currently Playing</h2>
    <img id="track-pic" alt="Track picture">
    <a id="track-name" target="_blank"></a>
    <ul id="artists"></ul>
    <div id="time-container">
        <p id="time"></p>
        <div id="player-btn">
            <img id="previous-btn" class="btn play-btn" src="images/previous.png" alt="previous" onclick="previous()">
            <img id="play-btn" class="btn play-btn" src="images/play.png" alt="play" onclick="play()">
            <img id="pause-btn" class="btn play-btn" src="images/pause.png" alt="pause" onclick="pause()">
            <img id="next-btn" class="btn play-btn" src="images/next.png" alt="next" onclick="next()">
        </div>
        <p id="duration"></p>
    </div>
    <progress id="time-bar"></progress>
    <div id="track-addon">
        <img id="like-btn" class="btn" onclick="toggleLike()" alt="like button">
        <div id="volume">
            <img id="volume-btn" src="images/volume.png" class="btn" alt="volume">
            <progress id="volume-bar" min="0" max="100"></progress>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
const fastTimeout = 100;
const mediumTimeout = 250;
const longTimeout = 500;

let deviceId = "";
let contextUri = "";
let currentTrackId = "";

let isPlaying = false;
// Function to update player information
function updatePlayer() {
    $.ajax({
        url: 'https://api.spotify.com/v1/me/player/currently-playing', // Endpoint to fetch current track information
        method: 'GET',
        headers: {
            'Authorization': 'Bearer <?php echo $_SESSION['access_token']; ?>'
        },
        success: function(response) {
            contextUri = response?.context.uri;
            currentTrackId = response?.item.id;

            // Update player interface with track information
            $('#track-pic').attr('src', response?.item.album.images[0].url);

            $('#track-name').text(response?.item.name);
            $('#track-name').attr('href', response?.item.external_urls.spotify);

            $('.artist_li').remove();
            $('#artists span').remove();
            for (let i = 0; i < response?.item.artists.length; i++) {    
                $('#artists').append('<li class="artist_li"><a href="' + response?.item.artists[i].external_urls.spotify + '" target="_blank" >' + response?.item.artists[i].name + '</a></li>');
                if (i < response?.item.artists.length - 1) {
                    $('#artists').append('<span> - </span>');
                }
            }

            isPlaying = response?.is_playing;
            if (isPlaying) {
                $('#play-btn').addClass('hidden');
                $('#pause-btn').removeClass('hidden');
            } else {
                $('#play-btn').removeClass('hidden');
                $('#pause-btn').addClass('hidden');
            }

            // Update time bar, progress, etc.
            var secondsDuration = parseInt(response?.item.duration_ms/1000);
            var minutesDuration = parseInt(secondsDuration/60);
            secondsDuration -= minutesDuration * 60;
            $('#duration').text(minutesDuration + ':' + String(secondsDuration).padStart(2, '0'));

            var secondsProgress = parseInt(response?.progress_ms/1000);
            var minutesProgress = parseInt(secondsProgress/60);
            secondsProgress -= minutesProgress * 60;
            $('#time').text(minutesProgress + ":" + String(secondsProgress).padStart(2, '0'));
            $('#time-bar').attr('max', response?.item.duration_ms);
            $('#time-bar').attr('min', 0);
            $('#time-bar').attr('value', response?.progress_ms);

            isTrackLiked();

            loadDevice();
        },
        error: function(xhr, status, error) {
            console.error('Error fetching current track:', error);
        }
    });
}

function updateTimeBar() {
    if (isPlaying) {
        var timeBar = document.getElementById('time-bar');
        var value = timeBar.value;
        var seconds = parseInt(value/1000);
        var minutes = parseInt(seconds/60);
        seconds -= minutes * 60;
        $('#time').text(minutes + ":" + String(seconds).padStart(2, '0'));
        timeBar.value = value + 100;

        if (timeBar.value >= timeBar.max) {
            setTimeout(updatePlayer, longTimeout);
        }
    }
}

function loadDevice() {
    $.ajax({
            url: 'https://api.spotify.com/v1/me/player/devices',
            method: 'GET',
            headers: {
                'Authorization': 'Bearer <?php echo $_SESSION['access_token']; ?>'
            },
            success: function(response) {
                response?.devices.forEach(element => {
                    if (element.is_active) {
                        deviceId = element.id;

                        if (element.supports_volume) {
                            $('#volume').removeClass('hidden');
                            $('#volume-bar').attr('value', element.volume_percent);
                            if (element.volume_percent < 50) {
                                $('#volume-btn').attr('src', 'images/low-volume.png');
                            } else {
                                $('#volume-btn').attr('src', 'images/volume.png');
                            }
                        } else {
                            $('#volume').addClass('hidden');
                        }
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error('Error fetching current track:', error);
            }
        });
}

function play() {
    $.ajax({
        url: 'https://api.spotify.com/v1/me/player/play',
        method: 'PUT',
        headers: {
            'Authorization': 'Bearer <?php echo $_SESSION['access_token']; ?>'
        },
        parameters: {
            'context_uri': contextUri,
            'position_ms': document.getElementById('time-bar').value
        },
        success: function(response) {
            setTimeout(updatePlayer, mediumTimeout);
        },
        error: function(xhr, status, error) {
            console.error('Error fetching current track:', error);
        }
    });
}

function pause() {
    $.ajax({
        url: 'https://api.spotify.com/v1/me/player/pause',
        method: 'PUT',
        headers: {
            'Authorization': 'Bearer <?php echo $_SESSION['access_token']; ?>'
        },
        success: function(response) {
            setTimeout(updatePlayer, mediumTimeout);
        },
        error: function(xhr, status, error) {
            console.error('Error fetching current track:', error);
        }
    });
}

function next() {
    $.ajax({
        url: 'https://api.spotify.com/v1/me/player/next',
        method: 'POST',
        headers: {
            'Authorization': 'Bearer <?php echo $_SESSION['access_token']; ?>'
        },
        success: function(response) {
            setTimeout(updatePlayer, longTimeout);
        },
        error: function(xhr, status, error) {
            console.error('Error fetching current track:', error);
        }
    });
}

function previous() {
    $.ajax({
        url: 'https://api.spotify.com/v1/me/player/previous',
        method: 'POST',
        headers: {
            'Authorization': 'Bearer <?php echo $_SESSION['access_token']; ?>'
        },
        success: function(response) {
            setTimeout(updatePlayer, longTimeout);
        },
        error: function(xhr, status, error) {
            console.error('Error fetching current track:', error);
        }
    });
}

function isTrackLiked() {
    $.ajax({
        url: 'https://api.spotify.com/v1/me/tracks/contains?ids=' + currentTrackId,
        method: 'GET',
        headers: {
            'Authorization': 'Bearer <?php echo $_SESSION['access_token']; ?>'
        },
        success: function(response) {
            if (response[0]) {
                $('#like-btn').attr('src', 'images/liked.png');
            } else {
                $('#like-btn').attr('src', 'images/like.png');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error checking if track is liked:', error);
        }
    });
}

function toggleLike() {
    var method = "PUT";

    if ($('#like-btn').attr('src') === 'images/liked.png') {
        method = "DELETE";
    }

    $.ajax({
            url: 'https://api.spotify.com/v1/me/tracks',
            method: method,
            headers: {
                'Authorization': 'Bearer <?php echo $_SESSION['access_token']; ?>'
            },
            data: JSON.stringify({
                ids: [currentTrackId]
            }),
            success: function(response) {
                if (method === 'PUT') {
                    $('#like-btn').attr('src', 'images/liked.png');
                } else {
                    $('#like-btn').attr('src', 'images/like.png');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error unliking track:', error);
            }
        });
}

// Click event for progress bar
document.getElementById('time-bar').addEventListener('click', function(e) {
    var progressBar = this;
    var max = progressBar.max;
    var width = progressBar.offsetWidth;
    var clickX = e.offsetX;
    var newValue = Math.round((clickX / width) * max);

    // Seek to the new position
    $.ajax({
        url: 'https://api.spotify.com/v1/me/player/seek?position_ms=' + newValue,
        method: 'PUT',
        headers: {
            'Authorization': 'Bearer <?php echo $_SESSION['access_token']; ?>'
        },
        success: function(response) {
            setTimeout(updatePlayer, mediumTimeout);
        },
        error: function(xhr, status, error) {
            console.error('Error seeking track:', error);
        }
    });
});

document.getElementById('volume-bar').addEventListener('click', function (e) {
    var progressBar = this;
    var max = progressBar.max;
    var width = progressBar.offsetWidth;
    var clickX = e.offsetX;
    var newValue = Math.round((clickX / width) * max);

    // Seek to the new position
    $.ajax({
        url: 'https://api.spotify.com/v1/me/player/volume?volume_percent=' + newValue,
        method: 'PUT',
        headers: {
            'Authorization': 'Bearer <?php echo $_SESSION['access_token']; ?>'
        },
        success: function(response) {
            setTimeout(loadDevice, longTimeout);
        },
        error: function(xhr, status, error) {
            console.error('Error seeking track:', error);
        }
    });
})

// Initial call to update player
updatePlayer();

// Periodically update player every 10 seconds (adjust as needed)
setInterval(updatePlayer, 10000); // 10 seconds
setInterval(updateTimeBar, 100);
</script>
