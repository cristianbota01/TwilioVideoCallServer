<?php

require __DIR__ . '/vendor/autoload.php';

use Twilio\Rest\Client;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VideoGrant;

define("AUTH_ID", "12345");

if (isset($_POST["auth_id"])) {
    if ($_POST["auth_id"] === AUTH_ID) {

        try {
           

            $room = $twilio->video->v1->rooms->create(["uniqueName" => "educational_room", "emptyRoomTimeout" => 1]);
        } catch (\Throwable $th) {
            //
        }

        echo json_encode(["response" => "ok"]);
    }
}
