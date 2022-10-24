<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <script src="//sdk.twilio.com/js/video/releases/2.17.1/twilio-video.min.js"></script>
    <title>Admin view</title>
</head>

<body>
    <div class="video-container-main-div">
        <div class="footer-main-div">
            <button class="button" onclick="CreateRoom()">
                Inizia chiamata
            </button>
            <button class="button" id="terminate-button">
                Termina la chiamata
            </button>
        </div>
        <div class="video-container">
            <p id="status-room"></p>
            <p id="status-participant"></p>
            <div class="video_1">
                <div class="layer1">
                    <div class="tv-static animation1"></div>
                </div>

                <div class="layer2">
                    <div class="tv-static animation2"></div>
                </div>
            </div>
            <!-- <div class="video_2">

            </div> -->
        </div>
        <!-- <div class="control-panel-main-div">
            <div class="control-panel">

            </div>
        </div> -->
    </div>
</body>

<script>
    const Video = Twilio.Video;

    var statusRoom = false
    var statusParticipant = false
    var room_sid = ""

    const GeneralStatus = (status_room, status_participant) => {
        if (status_room == true) {
            statusRoom = true
            document.querySelector("#status-room").innerHTML = "Status room: on"
        } else {
            statusRoom = false
            document.querySelector("#status-room").innerHTML = "Status room: off"
        }

        if (status_participant == true) {
            statusParticipant = true
            statusParticipant = document.querySelector("#status-participant").innerHTML = "Status participant: on"
        } else {
            statusParticipant = false
            statusParticipant = document.querySelector("#status-participant").innerHTML = "Status participant: off"
        }
    }

    GeneralStatus(false, false)

    const JoinRoom = () => {
        fetch("./joinRoom.php?identity=admin", {
            method: "GET"
        }).then(response => {
            return response.json()
        }).then(json_response => {
            if (json_response.response === "ok") {
                GeneralStatus(true, false)
                connectToRoom(json_response.token, json_response.room_name)
            }
        })
    }

    const CreateRoom = () => {
        fetch("./createRoom.php", {
            method: "POST",
            body: new URLSearchParams({
                "auth_id": 12345,
                "type": "create"
            })
        }).then(response => {
            return response.json()
        }).then(json_response => {
            if (json_response.response === "ok") {
                console.log(json_response)
                room_sid = json_response.sid
                JoinRoom()
            }
        })
    }

    const CompleteRoom = () => {
        console.log("icoa")
        fetch("./createRoom.php", {
            method: "POST",
            body: new URLSearchParams({
                "auth_id": 12345,
                "type": "complete",
                "sid": room_sid
            })
        }).then(response => {
            return response.json()
        }).then(json_response => {
            if (json_response.response === "ok") {
                GeneralStatus(false, false)
            }
        })
    }

    /* const connectToRoom = (token, roomName, audioinputvalue, audiooutputvalue, videoinputvalue) => {

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

                const videoChatWindowMain = document.querySelector('.video_1');
                const videoChatWindowGuest = document.querySelector('.video_2');

                createLocalVideoTrack().then(track => {
                    videoChatWindowMain.appendChild(track.attach());
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
                            videoChatWindowGuest.appendChild(track.attach());
                        }
                    });

                    participant.on('trackSubscribed', track => {
                        videoChatWindowGuest.appendChild(track.attach());
                    });


                });
            }, error => {
                console.error(`Unable to connect to Room: ${error.message}`);
            });
        })

    }; */

    const connectToRoom = (token, roomName) => {

        const {
            connect,
            createLocalTracks
        } = Video;

        createLocalTracks({
            audio: false,
            video: false
        }).then(local_tracks => {

            let connectOption = {
                name: roomName,
                tracks: local_tracks
            };

            connect(token, connectOption).then(room => {

                console.log(`Successfully joined a Room: ${room}`);

                const videoChatWindowMain = document.querySelector('.video_1');

                document.querySelector("#terminate-button").addEventListener("click", () => {
                    videoChatWindowMain.innerHTML = '<div class="layer1"><div class="tv-static animation1"></div></div><div class="layer2"><div class="tv-static animation2"></div></div>'
                    CompleteRoom()
                    room.disconnect()
                })

                room.on('participantConnected', participant => {

                    videoChatWindowMain.innerHTML = ""

                    console.log(`Participant "${participant.identity}" connected`);

                    GeneralStatus(true, true)

                    participant.tracks.forEach(publication => {
                        if (publication.isSubscribed) {
                            const track = publication.track;
                            videoChatWindowMain.appendChild(track.attach());
                        }
                    });

                    participant.on('trackSubscribed', track => {
                        videoChatWindowMain.appendChild(track.attach());
                    })

                    participant.on('reconnecting', () => {
                        console.log(`${participant.identity} is reconnecting the signaling connection to the Room!`);
                        videoChatWindowMain.innerHTML = '<div class="layer1"><div class="tv-static animation1"></div></div><div class="layer2"><div class="tv-static animation2"></div></div>'
                        GeneralStatus(true, false)

                    })

                });

                room.on('participantDisconnected', participant => {
                    videoChatWindowMain.innerHTML = '<div class="layer1"><div class="tv-static animation1"></div></div><div class="layer2"><div class="tv-static animation2"></div></div>'
                    console.log("Disconnected => ", participant)
                    GeneralStatus(true, false)
                });

            }, error => {
                console.error(`Unable to connect to Room: ${error.message}`);
            });

        })

    };
</script>

</html>