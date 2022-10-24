<?php

require __DIR__ . '/vendor/autoload.php';

use Twilio\Rest\Client;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VideoGrant;

define("AUTH_ID", "12345");

if (isset($_POST["auth_id"]) && isset($_POST["type"])) {

    

    try {

        if ($_POST["auth_id"] === AUTH_ID && $_POST["type"] === "create") {
            $room = $twilio->video->v1->rooms->create(["uniqueName" => "educational_room", "emptyRoomTimeout" => 1]);
            echo json_encode(["response" => "ok", "sid" => $room->sid]);
        }

    } catch (\Throwable $th) {

        $room_sid = $twilio->video->v1->rooms("educational_room")->fetch()->sid;
        echo json_encode(["response" => "ok", "sid" => $room_sid]);

    }

    try {

        if ($_POST["auth_id"] === AUTH_ID && $_POST["type"] === "complete" && isset($_POST["sid"])) {
            $twilio->video->v1->rooms($_POST["sid"])->update("completed");
            echo json_encode(["response" => "ok"]);
        }
    } catch (\Throwable $th) {
        echo json_encode(["response" => "nok"]);
    }
}
