const fastTimeout = 100;
const mediumTimeout = 250;
const longTimeout = 500;

let deviceId = "";
let contextUri = "";
let currentTrackId = "";

let volume = 0;

let isPlaying = false;

function getAccessToken() {
    return $.ajax({
        url: 'get_access_token.php',
        method: 'GET',
        dataType: 'json'
    });
}

// Function to update player information
function updatePlayer() {
    getAccessToken().then(function(data) {
        const accessToken = data.access_token;

        $.ajax({
            url: 'https://api.spotify.com/v1/me/player/currently-playing', // Endpoint to fetch current track information
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + accessToken
            },
            success: function(response) {
                if (response) {
                    contextUri = response?.context.uri;
                    currentTrackId = response?.item.id;

                    // Update player interface with track information
                    $('#track-pic').attr('src', response?.item.album.images[0].url);

                    $('#track-name').text(response?.item.name);
                    $('#track-name').attr('href', response?.item.external_urls.spotify);

                    $('#player .artist_li').remove();
                    $('#player #artists span').remove();
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
                    var secondsDuration = parseInt(response?.item.duration_ms / 1000);
                    var minutesDuration = parseInt(secondsDuration / 60);
                    secondsDuration -= minutesDuration * 60;
                    $('#duration').text(minutesDuration + ':' + String(secondsDuration).padStart(2, '0'));

                    var secondsProgress = parseInt(response?.progress_ms / 1000);
                    var minutesProgress = parseInt(secondsProgress / 60);
                    secondsProgress -= minutesProgress * 60;
                    $('#time').text(minutesProgress + ":" + String(secondsProgress).padStart(2, '0'));
                    $('#time-bar').attr('max', response?.item.duration_ms);
                    $('#time-bar').attr('min', 0);
                    $('#time-bar').attr('value', response?.progress_ms);

                    isTrackLiked();

                    loadDevice();
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

function updateTimeBar() {
    if (isPlaying) {
        var timeBar = document.getElementById('time-bar');
        var value = timeBar.value;
        var seconds = parseInt(value / 1000);
        var minutes = parseInt(seconds / 60);
        seconds -= minutes * 60;
        $('#time').text(minutes + ":" + String(seconds).padStart(2, '0'));
        timeBar.value = value + 100;

        if (timeBar.value >= timeBar.max) {
            setTimeout(updatePlayer, longTimeout);
        }
    }
}

function loadDevice() {
    getAccessToken().then(function(data) {
        const accessToken = data.access_token;

        $.ajax({
            url: 'https://api.spotify.com/v1/me/player/devices',
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + accessToken
            },
            success: function(response) {
                response?.devices.forEach(element => {
                    if (element.is_active) {
                        deviceId = element.id;

                        if (element.supports_volume) {
                            $('#volume').removeClass('hidden');
                            $('#volume-bar').attr('value', element.volume_percent);
                            if (element.volume_percent === 0) {
                                $('#volume-btn').attr('src', 'images/mute.png');
                            } else if (element.volume_percent < 50) {
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
    }).catch(function(error) {
        console.error('Error getting access token:', error);
    });
}

function play() {
    getAccessToken().then(function(data) {
        const accessToken = data.access_token;

        $.ajax({
            url: 'https://api.spotify.com/v1/me/player/play',
            method: 'PUT',
            headers: {
                'Authorization': 'Bearer ' + accessToken
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
    }).catch(function(error) {
        console.error('Error getting access token:', error);
    });
}

function pause() {
    getAccessToken().then(function(data) {
        const accessToken = data.access_token;

        $.ajax({
            url: 'https://api.spotify.com/v1/me/player/pause',
            method: 'PUT',
            headers: {
                'Authorization': 'Bearer ' + accessToken
            },
            success: function(response) {
                setTimeout(updatePlayer, mediumTimeout);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching current track:', error);
            }
        });
    }).catch(function(error) {
        console.error('Error getting access token:', error);
    });
}

function next() {
    getAccessToken().then(function(data) {
        const accessToken = data.access_token;

        $.ajax({
            url: 'https://api.spotify.com/v1/me/player/next',
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + accessToken
            },
            success: function(response) {
                setTimeout(updatePlayer, longTimeout);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching current track:', error);
            }
        });
    }).catch(function(error) {
        console.error('Error getting access token:', error);
    });
}

function previous() {
    getAccessToken().then(function(data) {
        const accessToken = data.access_token;

        $.ajax({
            url: 'https://api.spotify.com/v1/me/player/previous',
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + accessToken
            },
            success: function(response) {
                setTimeout(updatePlayer, longTimeout);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching current track:', error);
            }
        });
    }).catch(function(error) {
        console.error('Error getting access token:', error);
    });
}

function isTrackLiked() {
    getAccessToken().then(function(data) {
        const accessToken = data.access_token;

        $.ajax({
            url: 'https://api.spotify.com/v1/me/tracks/contains?ids=' + currentTrackId,
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + accessToken
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
    }).catch(function(error) {
        console.error('Error getting access token:', error);
    });
}

function toggleLike() {
    var method = "PUT";

    if ($('#like-btn').attr('src') === 'images/liked.png') {
        method = "DELETE";
    }

    getAccessToken().then(function(data) {
        const accessToken = data.access_token;

        $.ajax({
            url: 'https://api.spotify.com/v1/me/tracks',
            method: method,
            headers: {
                'Authorization': 'Bearer ' + accessToken
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
    }).catch(function(error) {
        console.error('Error getting access token:', error);
    });
}

function toggleVolume() {
    if (document.getElementById('volume-bar').value != 0) {
        volume = document.getElementById('volume-bar').value;
        setVolume(0);
    } else {
        setVolume(volume);
    }
}

function setVolume(percent) {
    getAccessToken().then(function(data) {
        const accessToken = data.access_token;

        $.ajax({
            url: 'https://api.spotify.com/v1/me/player/volume?volume_percent=' + percent,
            method: 'PUT',
            headers: {
                'Authorization': 'Bearer ' + accessToken
            },
            success: function(response) {
                setTimeout(loadDevice, longTimeout);
            },
            error: function(xhr, status, error) {
                console.error('Error seeking track:', error);
            }
        });
    }).catch(function(error) {
        console.error('Error getting access token:', error);
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
    getAccessToken().then(function(data) {
        const accessToken = data.access_token;

        $.ajax({
            url: 'https://api.spotify.com/v1/me/player/seek?position_ms=' + newValue,
            method: 'PUT',
            headers: {
                'Authorization': 'Bearer ' + accessToken
            },
            success: function(response) {
                setTimeout(updatePlayer, mediumTimeout);
            },
            error: function(xhr, status, error) {
                console.error('Error seeking track:', error);
            }
        });
    }).catch(function(error) {
        console.error('Error getting access token:', error);
    });
});

document.getElementById('volume-bar').addEventListener('click', function(e) {
    var progressBar = this;
    var max = progressBar.max;
    var width = progressBar.offsetWidth;
    var clickX = e.offsetX;
    var newValue = Math.round((clickX / width) * max);

    setVolume(newValue);
})

// Initial call to update player
updatePlayer();

// Periodically update player every 10 seconds (adjust as needed)
setInterval(updatePlayer, 10000); // 10 seconds
setInterval(updateTimeBar, 100);