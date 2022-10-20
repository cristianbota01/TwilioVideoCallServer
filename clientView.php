<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <script src="//sdk.twilio.com/js/video/releases/2.17.1/twilio-video.min.js"></script>
    <title>Client view</title>
</head>

<body>
    <div class="video-container-main-div">
        <div class="video-container">

        </div>
    </div>
    <div class="footer-main-div">
        <button class="button" onclick="JoinRoom()">
            Inizia chiamata
        </button>
        <button class="button" onclick="Terminate()">
            Esci dalla chiamata
        </button>
    </div>
    <script>
        const Video = Twilio.Video;

        const JoinRoom = () => {
            fetch("./joinRoom.php?identity=guest", {
                method: "GET"
            }).then(response => {
                return response.json()
            }).then(json_response => {
                if (json_response.response === "ok") {
                    connectToRoom(json_response.token, json_response.room_name, true, true, true)
                }
            })
        }

        const connectToRoom = (token, roomName, audioinputvalue, audiooutputvalue, videoinputvalue) => {

            const {
                connect,
                createLocalVideoTrack,
                createLocalTracks
            } = Video;

            createLocalTracks({
                audio: {
                    deviceId: audioinputvalue
                },
                video: {
                    deviceId: videoinputvalue
                }
            }).then(local_tracks => {

                let connectOption = {
                    name: roomName,
                    tracks: local_tracks
                };

                connect(token, connectOption).then(room => {

                    console.log(`Successfully joined a Room: ${room}`);

                    const videoChatWindow = document.querySelector('.video-container');

                    createLocalVideoTrack().then(track => {
                        videoChatWindow.appendChild(track.attach());
                    });

                    room.on('trackSubscribed', track => {

                        if (track.kind === 'audio') {
                            const audioElement = track.attach();
                            audioElement.setSinkId(audiooutputvalue).then(() => {
                                document.body.appendChild(audioElement);
                            });
                        }
                    });

                    room.on('participantConnected', participant => {

                        console.log(`Participant "${participant.identity}" connected`);

                        participant.tracks.forEach(publication => {
                            if (publication.isSubscribed) {
                                const track = publication.track;
                                videoChatWindow.appendChild(track.attach());
                            }
                        });

                        participant.on('trackSubscribed', track => {
                            videoChatWindow.appendChild(track.attach());
                        });


                    });
                }, error => {
                    console.error(`Unable to connect to Room: ${error.message}`);
                });
            })

        };
    </script>
</body>

</html>