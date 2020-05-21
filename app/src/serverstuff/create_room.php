<?php

require_once 'db_functions.php';
$db = new db_functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['room_name'])) {

    // receiving the post params
    $room_name = $_POST['room_name'];

    // checks if room already exists
    if ($db->doesRoomExist($room_name)) {
        // room already exists
        $response["error"] = TRUE;
        $response["error_msg"] = "Room already exists";
        echo json_encode($response);
    } else {
        // create a new rooom
        $check = $db->storeRoom($room_name);
        if ($check) {
            // room stored successfully
            $response["error"] = FALSE;
            $response["room"]["room_name"] = $room_name;
            echo json_encode($response);
        } else {
            // user failed to store
            $response["error"] = TRUE;
            $response["error_msg"] = "Unknown error occurred in room creation!";
            echo json_encode($response);
        }
    }
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameter room_name is missing!";
    echo json_encode($post_body);
}
?>
