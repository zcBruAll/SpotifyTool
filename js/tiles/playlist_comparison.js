function delay(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

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
            let likedSongsItem = `
              <li class="playlist-item">
                  <img class="playlist-pic" src="${"images/liked_songs.png"}" alt="Playlist picture">
                  <a class="playlist-name" target="_blank">Liked Songs</a>
                  <p class="hidden">liked_songs</p>
              </li>
            `;
            $("#playlist-list1").append(likedSongsItem);
            $("#playlist-list2").append(likedSongsItem);
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
              <p class="hidden">${playlist.id}</p>
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
  selectedElement.parent().children(".playlist-item").removeClass("selected");
  selectedElement.addClass("selected");
}

function comparePlaylists() {
  const playlistId1 = $("#playlist-list1")
    .children(".playlist-item.selected")
    .children("p")
    .text();
  const playlistId2 = $("#playlist-list2")
    .children(".playlist-item.selected")
    .children("p")
    .text();

  if (playlistId1 && playlistId2) {
    $("#compare").children("p").text("Processing...");
    $("#compare").addClass("disabled");
    $("#refresh").addClass("disabled");
    Promise.all([fetchAllTrackIds(playlistId1), fetchAllTrackIds(playlistId2)])
      .then(function (results) {
        const tracks1 = results[0];
        const tracks2 = results[1];

        $('#result').removeClass("hidden");
        displayUniques(getUniqueIds(tracks1, tracks2));

        $("#compare").children("p").text("Compare");
        $("#compare").removeClass("disabled");
        $("#refresh").removeClass("disabled");
      })
      .catch(function (error) {
        console.error("Error comparing playlists:", error);

        $("#compare").children("p").text("Compare");
        $("#compare").removeClass("disabled");
        $("#refresh").removeClass("disabled");
      });
  }
}

function fetchAllTrackIds(playlistId) {
  return new Promise((resolve, reject) => {
    const trackIds = [];
    const limit = 50;
    let offset = 0;

    function fetchTracks(offset) {
      getAccessToken()
        .then(function (data) {
          const accessToken = data.access_token;
          let requestend =
            playlistId == "liked_songs" ? "me" : "playlists/" + playlistId;
          $.ajax({
            url: `https://api.spotify.com/v1/${requestend}/tracks`,
            method: "GET",
            headers: {
              Authorization: `Bearer ` + accessToken,
            },
            data: {
              limit: limit,
              offset: offset,
            },
            success: function (response) {
              if (response && response.items) {
                response.items.forEach((item) => {
                  if (item.track && item.track.id) {
                    trackIds.push(item.track.id);
                  }
                });

                if (offset + limit < response.total) {
                  fetchTracks(offset + limit);
                } else {
                  resolve(trackIds);
                }
              } else {
                resolve(trackIds);
              }
            },
            error: function (xhr, status, error) {
              reject(error);
            },
          });
        })
        .catch(function (error) {
          reject(error);
        });
    }

    fetchTracks(offset);
  });
}

function getUniqueIds(array1, array2) {
  const set1 = new Set(array1);
  const set2 = new Set(array2);

  const uniqueToSet1 = [...set1].filter((id) => !set2.has(id));
  const uniqueToSet2 = [...set2].filter((id) => !set1.has(id));

  return [...uniqueToSet1, ...uniqueToSet2];
}

async function displayUniques(ids) {
  $("#differences").empty();
  for (let i = 0; i < ids.length; i += 50) {
    let temp = ids;
    temp = temp.slice(i, i + 50);
    const idsList = temp.join(",");
   
    getAccessToken()
    .then(function (data) {
      const accessToken = data.access_token;
      $.ajax({
        url: "https://api.spotify.com/v1/tracks",
        method: "GET",
        headers: {
          Authorization: "Bearer " + accessToken,
        },
        data: {
          ids: decodeURIComponent(idsList),
        },
        success: function (response) {
          if (response.tracks) {
            response.tracks.forEach((track) => {
              let listItem = `
                                      <li class="track-item">
                                          <img class="track-pic" src="${
                                            track.album.images[0].url != null
                                              ? track.album.images[0].url
                                              : "images/playlist.png"
                                          }" alt="Track picture">
                                          <a class="track-name" href="${
                                            track.external_urls.spotify
                                          }" target="_blank">${track.name}</a>
              <p class="hidden">${track.id}</p>
                                      </li>
                                  `;
              $("#differences").append(listItem);
            });
          } else {
            $("#differences").addClass("hidden");
          }
        },
        error: function (xhr, status, error) {
          console.error("Error fetching playlists:", xhr);
        },
      });
    })
    .catch(function (error) {
      console.error("Error getting access token:", error);
    });

    await delay(250);
  }
}

updatePlaylists();

setInterval(updatePlaylists, 120000);

$("#playlist-list1").on("click", ".playlist-item", selectPlaylist);
$("#playlist-list2").on("click", ".playlist-item", selectPlaylist);
