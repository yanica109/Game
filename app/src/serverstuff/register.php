<?php

require_once 'db_functions.php';
$db = new db_functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['username']) && isset($_POST['password'])) {

    // receiving the post params
    $username = $_POST['username'];
    $password = $_POST['password'];

    // checks if user already exists
    if ($db->doesUserExist($username)) {
        // user already exists
        $response["error"] = TRUE;
        $response["error_msg"] = "User already exists";
        echo json_encode($response);
    } else {
        // create a new user
        $check = $db->storeUser($username, $password);
        if ($check) {
            // user stored successfully
            $response["error"] = FALSE;
            $response["user"]["username"] = $username;
            echo json_encode($response);
        } else {
            // user failed to store
            $response["error"] = TRUE;
            $response["error_msg"] = "Unknown error occurred in registration!";
            echo json_encode($response);
        }
    }
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters (username or password) is missing!";
    echo json_encode($post_body);
}
?>
