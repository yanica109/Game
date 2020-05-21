<?php
class db_functions {

    private $conn;

    function __construct() {
        require_once 'db_connect.php';
        $db = new db_connect();
        $this->conn = $db->connect();
    }

    function __destruct() {
    }

    public function storeUser($username, $password) {
       $hash = $this->hashSSHA($password);
       $encrypted_password = $hash["encrypted"]; // encrypted password
       $salt = $hash["salt"]; // salt

       $stmt = $this->conn->prepare("INSERT INTO users(username, encrypted_password, salt) VALUES(?, ?, ?)");
       $stmt->bind_param("sss", $username, $encrypted_password, $salt);
       $result = $stmt->execute();
       $stmt->close();

       // check for successful store
       if ($result) {
           return true;
       } else {
           return false;
       }
    }

    public function getUserByUsernameAndPassword($username, $password) {

        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");

        $stmt->bind_param("s", $username);

        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            // verifying user password
            $salt = $user['salt'];
            $encrypted_password = $user['encrypted_password'];
            $hash = $this->checkhashSSHA($salt, $password);
            // check for password equality
            if ($encrypted_password == $hash) {
                // user auth details are correct
                return $user;
            }
        } else {
            return NULL;
        }
    }

      public function getRoomByRoomName($room_name) {

          $stmt = $this->conn->prepare("SELECT * FROM rooms WHERE room_name = ?");

          $stmt->bind_param("s", $room_name);

          if ($stmt->execute()) {
              $room = $stmt->get_result()->fetch_assoc();
              $stmt->close();
              return $room;

          } else {
              return NULL;
          }
      }

    public function doesUserExist($username) {
      error_reporting(E_ALL);
      ini_set('display_errors', 1);

        $stmt = $this->conn->prepare("SELECT username from users WHERE username = ?");
      echo $this->conn->error;
        $stmt->bind_param("s", $username);

        $stmt->execute();

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // user exists
            $stmt->close();
            return true;
        } else {
            // user doesnt exists
            $stmt->close();
            return false;
        }
    }

    //encrypting password
    public function hashSSHA($password) {

        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }

    //decrypting password
    public function checkhashSSHA($salt, $password) {

        $hash = base64_encode(sha1($password . $salt, true) . $salt);

        return $hash;
    }


    public function doesRoomExist($room_name){
      error_reporting(E_ALL);
      ini_set('display_errors', 1);

        $stmt = $this->conn->prepare("SELECT room_name from rooms WHERE room_name = ?");
        echo $this->conn->error;
        $stmt->bind_param("s", $room_name);

        $stmt->execute();

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // room exists
            $stmt->close();
            return true;
        } else {
            // room doesn't exist
            $stmt->close();
            return false;
        }
    }

    public function storeRoom($room_name){

      $stmt = $this->conn->prepare("INSERT INTO rooms(room_name) VALUES(?)");
      $stmt->bind_param("s", $room_name);
      $result = $stmt->execute();
      $stmt->close();

      // check for successful store
      if ($result) {
          return true;
      } else {
          return false;
      }
    }
}

?>
