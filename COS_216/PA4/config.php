<?php

class DatabaseConfig {
    public $host = "localhost"; 
    public $user = "u23545527"; 
    public $pass = "5F5VMVAZH6RQUI4HOSYOKBCCNRNCE3M7"; 
    public $dbname = "u23545527_products"; 
}


class DatabaseConnection {
    private static $instance = null;
    private $connection;

    public function __construct(DatabaseConfig $config) {
        $this->connection = new mysqli(
            $config->host,
            $config->user,
            $config->pass,
            $config->dbname
        );

        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            $config = new DatabaseConfig();
            self::$instance = new DatabaseConnection($config);
        }

        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
}
?>
