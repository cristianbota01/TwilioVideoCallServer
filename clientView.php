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
        <div class="footer-main-div">
            <button class="button" onclick="JoinRoom()">
                Inizia chiamata
            </button>
            <button class="button" id="terminate-button">
                Esci dalla chiamata
            </button>
        </div>
        <div class="video-container">
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
        <div class="control-panel-main-div">
            <div class="control-panel">
                <p>Seleziona microfono</p>
                <select name="audioinput" id="selectaudioinput">

                </select>
                <p>Seleziona webcam</p>
                <select name="videoinput" id="selectvideoinput">

                </select>
                <p>Seleziona altoparlante</p>
                <select name="audiooutput" id="selectaudiooutput">

                </select>
            </div>
        </div>
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
                    connectToRoom(json_response.token, json_response.room_name, document.querySelector("#selectaudioinput").value, document.querySelector("#selectvideoinput").value, document.querySelector("#selectaudiooutput").value)
                } else {
                    alert("La stanza non esiste")
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

        const GetDevices = () => {

            navigator.mediaDevices.enumerateDevices().then(devices => {
                devices.forEach(device => {
                    if (device.kind === "audioinput") {
                        let option = document.createElement("option")
                        option.value = device.deviceId
                        option.innerText = device.label
                        document.querySelector("#selectaudioinput").appendChild(option)
                    }
                    if (device.kind === "videoinput") {
                        let option = document.createElement("option")
                        option.value = device.deviceId
                        option.innerText = device.label
                        document.querySelector("#selectvideoinput").appendChild(option)
                    }
                    if (device.kind === "audiooutput") {
                        let option = document.createElement("option")
                        option.value = device.deviceId
                        option.innerText = device.label
                        document.querySelector("#selectaudiooutput").appendChild(option)
                    }
                })

            })

        }

        GetDevices()

        const connectToRoom = (token, roomName, audioinputvalue, videoinputvalue, audiooutputvalue) => {

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

                    document.querySelector("#terminate-button").addEventListener("click", () => {
                        room.disconnect()
                    })

                    console.log(`Successfully joined a Room: ${room}`);

                    const videoChatWindowMain = document.querySelector('.video_1');

                    videoChatWindowMain.innerHTML = ""

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

                    room.on('disconnected', (room, error) => {
                        console.log(error)
                    });

                }, error => {
                    console.error(`Unable to connect to Room: ${error.message}`);
                });
            })

        };
    </script>
</body>

</html>