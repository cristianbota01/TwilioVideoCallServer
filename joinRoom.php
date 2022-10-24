<?php

require __DIR__ . '/vendor/autoload.php';

use Twilio\Rest\Client;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VideoGrant;

if ($_SERVER["REQUEST_METHOD"] === "GET") {

    

    $rooms = $twilio->video->v1->rooms->read(["status" => "in-progress"], 1);

    if (count($rooms) > 0) {

        $unique_name = $rooms[0]->uniqueName;

        

        $videoGrant = new VideoGrant();
        $videoGrant->setRoom($unique_name);

        $token->addGrant($videoGrant);

        echo json_encode(["response" => "ok", "token" => $token->toJWT(), "room_name" => $unique_name]);
    } else {
        echo json_encode(["response" => "nok"]);
    }
}
