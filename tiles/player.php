<!-- player.php -->
<link rel="stylesheet" href="css/global.css">
<link rel="stylesheet" href="css/tiles/player.css">
<div id="player" class="tile">
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
            <img id="volume-btn" src="images/volume.png" class="btn" alt="volume" onclick="toggleVolume()">
            <progress id="volume-bar" min="0" max="100"></progress>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="js/tiles/player.js"></script>