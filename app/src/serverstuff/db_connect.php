<?php
class db_connect {
    private $conn;
    public function connect() {
        require_once 'config.php';
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE, "3308");
        return $this->conn;
    }
}
?>
