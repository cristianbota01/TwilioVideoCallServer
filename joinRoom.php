<?php

require __DIR__ . '/vendor/autoload.php';

use Twilio\Rest\Client;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VideoGrant;

if ($_SERVER["REQUEST_METHOD"] === "GET") {

    $sid = "AC73f3373587faca2c63a59f50e3e5b7b1";
    $token = "29d2665ee35503f682b302391a22e1cf";
    $twilio = new Client($sid, $token);

    $rooms = $twilio->video->v1->rooms->read(["status" => "in-progress"], 1);

    if (count($rooms) > 0) {

        $unique_name = $rooms[0]->uniqueName;

        $token = new AccessToken(
            "AC73f3373587faca2c63a59f50e3e5b7b1",
            "SK6dcbe465cc1456a176eb84a718f4f513",
            "jOimthtHcMBwj4kMrz8OEEksa4pe0mPV",
            3600,
            $_GET["identity"]
        );

        $videoGrant = new VideoGrant();
        $videoGrant->setRoom($unique_name);

        $token->addGrant($videoGrant);

        echo json_encode(["response" => "ok", "token" => $token->toJWT(), "room_name" => $unique_name]);
    } else {
        echo json_encode(["response" => "nok rooms"]);
    }
}
