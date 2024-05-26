function updatePlaylists() {
  getAccessToken()
    .then(function (data) {
      const accessToken = data.access_token;
      $.ajax({
        url: "https://api.spotify.com/v1/me/playlists",
        method: "GET",
        headers: {
          Authorization: "Bearer " + accessToken,
        },
        success: function (response) {
          if (response.items) {
            $("#playlist-list1").empty();
            $("#playlist-list2").empty();
            response.items.forEach((playlist) => {
              let listItem = `
                                      <li class="playlist-item">
                                          <img class="playlist-pic" src="${
                                            playlist.images != null
                                              ? playlist.images[0].url
                                              : "images/playlist.png"
                                          }" alt="Playlist picture">
                                          <a class="playlist-name" href="${
                                            playlist.external_urls.spotify
                                          }" target="_blank">${
                playlist.name
              }</a>
                                      </li>
                                  `;
              $("#playlist-list1").append(listItem);
              $("#playlist-list2").append(listItem);
            });
          } else {
            $("#playlists").addClass("hidden");
          }
        },
        error: function (xhr, status, error) {
          console.error("Error fetching playlists:", error);
        },
      });
    })
    .catch(function (error) {
      console.error("Error getting access token:", error);
    });
}

function selectPlaylist(event) {
    const selectedElement = $(event.currentTarget);
    selectedElement.parent().children('.playlist-item').removeClass('selected');
    selectedElement.addClass('selected');
}

updatePlaylists();

// Use event delegation to handle click events for dynamically created elements
$('#playlist-list1').on('click', '.playlist-item', selectPlaylist);
$('#playlist-list2').on('click', '.playlist-item', selectPlaylist);


