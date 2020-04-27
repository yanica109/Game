<?php
class db_connect {
    private $conn;
    public function connect() {
        require_once 'config.php';
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE, "3308");

        // require_once 'config.php';
        //
        // try {
        //     $conn = new PDO("mysql:host=$DB_HOST;dbname=$DB_DATABASE", $DB_USER, $DB_PASSWORD);
        //     echo "Connected to $DB_DATABASE at $DB_HOST successfully.";
        // } catch (PDOException $pe) {
        //     die("Could not connect to the database $DB_DATABASE :" . $pe->getMessage());
        // }
        return $this->conn;
    }
}
?>
