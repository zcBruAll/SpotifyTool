<link rel="stylesheet" href="css/global.css">
<link rel="stylesheet" href="css/tiles/playlist_comparison.css">

<div id="playlists_comparison" class="tile">
    <h2>Playlist comparison</h2>
    <div class="inline">
        <ul id="playlist-list1" class="playlist-list"></ul>
        <ul id="playlist-list2" class="playlist-list"></ul>
    </div>

    <div id="buttons">
        <div id="refresh" class="button" onclick="updatePlaylists()">
            <img src="images/refresh.png" alt="Refresh" class="btn">
            <p>Refresh</p>
        </div>
        <div id="compare" class="button">
            <p>Compare</p>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="js/tiles/playlist_comparison.js"></script>