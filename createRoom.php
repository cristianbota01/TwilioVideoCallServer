<?php

require __DIR__ . '/vendor/autoload.php';

use Twilio\Rest\Client;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VideoGrant;

define("AUTH_ID", "12345");

if (isset($_POST["auth_id"])) {
    if ($_POST["auth_id"] === AUTH_ID) {

        try {
            $sid = "AC73f3373587faca2c63a59f50e3e5b7b1";
            $token = "29d2665ee35503f682b302391a22e1cf";
            $twilio = new Client($sid, $token);

            $room = $twilio->video->v1->rooms->create(["uniqueName" => "educational_room", "emptyRoomTimeout" => 1]);
        } catch (\Throwable $th) {
            //
        }

        echo json_encode(["response" => "ok"]);
    }
}
