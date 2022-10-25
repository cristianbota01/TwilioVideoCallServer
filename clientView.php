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

                <button class="button" id="audio-button-switch">
                    Audio
                </button>
                <button class="button" id="video-button-switch">
                    Video
                </button>
                <button class="button" onclick="JoinRoom()">
                    Inizia chiamata
                </button>
                <button class="button" id="terminate-button">
                    Esci dalla chiamata
                </button>

                <!-- <canvas id="canvas"></canvas> -->

                <div class="pids-wrapper">
                    <div class="pid"></div>
                    <div class="pid"></div>
                    <div class="pid"></div>
                    <div class="pid"></div>
                    <div class="pid"></div>
                    <div class="pid"></div>
                    <div class="pid"></div>
                    <div class="pid"></div>
                    <div class="pid"></div>
                    <div class="pid"></div>
                    <div class="pid"></div>
                    <div class="pid"></div>
                    <div class="pid"></div>
                    <div class="pid"></div>
                    <div class="pid"></div>
                    <div class="pid"></div>
                    <div class="pid"></div>
                    <div class="pid"></div>
                    <div class="pid"></div>
                    <div class="pid"></div>
                </div>

            </div>
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
    </div>
    <script>
        const Video = Twilio.Video;
        var video_track = null,
            video_on = false,
            audio_on = false

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

                    console.log(room.localParticipant)

                    window.addEventListener('beforeunload', () => {
                        room.disconnect();
                    });

                    document.querySelector("#terminate-button").addEventListener("click", () => {
                        disableCamera()
                        room.disconnect()
                    })

                    document.querySelector("#audio-button-switch").addEventListener("click", () => {
                        room.localParticipant.audioTracks.forEach(publication => {
                            publication.track.disable()
                        });
                    })

                    const enableCamera = () => {
                        video_on = true

                        room.localParticipant.videoTracks.forEach(publication => {
                            publication.track.enable()
                        })

                        createLocalVideoTrack().then(track => {
                            video_track = track
                            videoChatWindowMain.appendChild(track.attach());
                        });

                    }

                    const disableCamera = () => {
                        video_on = false

                        room.localParticipant.videoTracks.forEach(publication => {
                            publication.track.disable()
                        })

                        setTimeout(() => {
                            var detachedElements = video_track.detach();
                            detachedElements.forEach(function(el) {
                                el.remove();
                            });
                        }, 5000)



                    }

                    document.querySelector("#video-button-switch").addEventListener("click", () => {

                        /* room.localParticipant.videoTracks.forEach(publication => {
                            publication.track.disable()
                            //publication.track.stop()
                            console.log(publication.track.detach())
                            
                        }); */

                        /* room.localParticipant.videoTracks.forEach(track => {
                            const attachedElements = track.detach();
                            attachedElements.forEach(element => element.remove());
                        }); */

                        /* console.log("sas")

                        console.log(local_tracks)
                        console.log(room.localParticipant.videoTracks)

                        local_tracks[1].disable()
                        local_tracks[1].detach().forEach(element => element.remove());

                        console.log(local_tracks[1].detach()) */

                        if (video_on == true) {
                            disableCamera()
                        } else {
                            enableCamera()
                        }

                        /* room.localParticipant.videoTracks.forEach(publication => {
                            publication.unpublish();
                            publication.track.stop();
                        }); */

                    })

                    console.log(`Successfully joined a Room: ${room}`);

                    const videoChatWindowMain = document.querySelector('.video_1');

                    enableCamera()

                    videoChatWindowMain.innerHTML = ""

                    room.on('trackSubscribed', track => {

                        if (track.kind === 'audio') {
                            const audioElement = track.attach();
                            audioElement.setSinkId(audiooutputvalue).then(() => {
                                document.body.appendChild(audioElement);
                            });
                        }
                    });

                    room.on('disconnected', (room, error) => {
                        console.log("disconnected => ", error)
                        disableCamera()
                        room.disconnect();
                    });

                }, error => {
                    console.error(`Unable to connect to Room: ${error.message}`);
                });
            })

        };

        console.log(document.querySelector("#selectaudioinput").value)

        /* navigator.mediaDevices.getUserMedia({
            audio: true
        }).then(function(stream) {
            audioViz2(stream)
        }) */

        function audioViz2(stream) {

            const audioContext = new AudioContext();
            const analyser = audioContext.createAnalyser();
            const microphone = audioContext.createMediaStreamSource(stream);
            const scriptProcessor = audioContext.createScriptProcessor(2048, 1, 1);

            analyser.smoothingTimeConstant = 0.8;
            analyser.fftSize = 1024;

            microphone.connect(analyser);
            analyser.connect(scriptProcessor);
            scriptProcessor.connect(audioContext.destination);

            scriptProcessor.onaudioprocess = function() {

                const array = new Uint8Array(analyser.frequencyBinCount);
                analyser.getByteFrequencyData(array);
                const arraySum = array.reduce((a, value) => a + value, 0);
                const average = arraySum / array.length;
                //console.log(Math.round(average));
                colorPids(average);
            };

        }


        function colorPids(vol) {

            console.log(vol)

            const allPids = [...document.querySelectorAll('.pid')];
            const numberOfPidsToColor = Math.round(vol / 5);
            const pidsToColor = allPids.slice(0, numberOfPidsToColor);

            for (const pid of allPids) {
                pid.style.backgroundColor = "#e6e7e8";
            }

            for (const pid of pidsToColor) {
                // console.log(pid[i]);
                if (vol > 0 && vol < 40) {
                    pid.style.backgroundColor = "#69ce2b";
                }

                if (vol > 40 && vol < 70) {
                    pid.style.backgroundColor = "#CEC52B";
                }

                if (vol > 70) {
                    pid.style.backgroundColor = "#CE402B";
                }

            }

        }

        function audioViz(stream) {

            var context = new AudioContext();
            var analyser = context.createAnalyser();

            var microphone = context.createMediaStreamSource(stream);

            var canvas = document.getElementById("canvas");
            canvas.width = 50;
            canvas.height = 50;
            var ctx = canvas.getContext("2d");

            microphone.connect(analyser);
            analyser.connect(context.destination);

            analyser.fftSize = 256;

            var bufferLength = analyser.frequencyBinCount;

            var dataArray = new Uint8Array(bufferLength);

            var WIDTH = canvas.width;
            var HEIGHT = canvas.height;

            var barWidth = (WIDTH / bufferLength) * 2.5;
            var barHeight;
            var x = 0;

            function renderFrame() {

                requestAnimationFrame(renderFrame);

                x = 0;

                analyser.getByteFrequencyData(dataArray);

                ctx.fillStyle = "#000";
                ctx.fillRect(0, 0, WIDTH, HEIGHT);

                for (var i = 0; i < bufferLength; i++) { //bufferLeght => 128

                    barHeight = dataArray[i];

                    /* var r = barHeight + (25 * (i / bufferLength));
                    var g = barHeight + (25 * (i / bufferLength));
                    var b = barHeight + (25 * (i / bufferLength)); */

                    var r = 650 * (i / bufferLength);
                    var g = barHeight + (100 * (i / bufferLength));
                    var b = 0;

                    ctx.fillStyle = "rgb(" + r + "," + g + "," + b + ")";
                    ctx.fillRect(x, HEIGHT - barHeight, barWidth, barHeight);
                    /* ctx.fillRect(x, HEIGHT - barHeight, barWidth, barHeight); */
                    /* ctx.fillRect(x, 0, barWidth, HEIGHT); */

                    x += barWidth;
                }
            }

            renderFrame();
        }
    </script>
</body>

</html>