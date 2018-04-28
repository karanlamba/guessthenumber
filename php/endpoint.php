<?php
// CHECK REQUEST METHOD
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(["status"=>"Method Not Allowed", "code"=>"405", "message"=>"Method Not Allowed"]);
}

// SETUP
session_start();
date_default_timezone_set('America/New_York');

// MY CLASSES
require_once("db.php");
require_once("game.php");

// CONFIG FILE (DATABASE CREDENTIALS)
// Would normally place login info in enviernment vars
$config_file = __DIR__ . "/config.json";

try {

    if (file_exists($config_file)){
        // GET CONFIG DATA
        $string = file_get_contents($config_file);
        $config = json_decode($string, true);

        //INIT CLASSES
        $db = new DBHandler($config);
        $game = new Game($db);

        //GET REQUEST DATA
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body, true);

        //DO REQUESTED ACTION
        switch ($data["action"]) {
            case "check": // CHECK ACTIVE GAME
                echo json_encode(["status"=>"checked", "code"=>"200", "message"=>$game->getGameData()]);
                break;

            case "start": // START NEW GAME
                if( $game->Start((int)$data["data"]) ){
                    echo json_encode(["status"=>"started", "code"=>"200", "message"=>""]);
                }
                break;

            case "guess": // GUESS THE NUMBER
                $result = $game->Guess((int)$data["data"]);
                if ( $result ) {
                    echo json_encode(["status"=>$result, "code"=>"200", "message"=>""]);
                }
                break;

            case "end": // ABANDON
                $game->End();
                echo json_encode(["status"=>"ended", "code"=>"200", "message"=>""]);
                break;
            
            case "stats": // GET STATISTICS
                $result = $game->getGameStats();
                echo json_encode(["status"=>"stats", "code"=>"200", "message"=>$result]);
                break;
            
            default:
                throw new Exception( "Sorry looks like I forgot something." );
        }

    } else {
        throw new Exception( "Sorry looks like I forgot something." );
    }

}
catch( Exception $e ) {
    echo json_encode(["status"=>"error", "code"=>"500", "message"=>$e->getMessage()]);
}

exit();