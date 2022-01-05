<?php

class Database
{
    // empty PDO object to cache connection
    private ?PDO $conn = null;
    // constructor Database class
    public function __construct(
        private string $host,
        private string $name,
        private string $user,
        private string $password
    ) {
    }

    // get connection
    public function getConnection(): PDO
    {
        if ($this->conn === null) {
            // domain server name
            $dsn = "mysql:host={$this->host};dbname={$this->name};charset=utf8";
        
            // return new PDO object
            $this->conn = new PDO($dsn, $this->user, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_STRINGIFY_FETCHES => false,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        }

        return $this->conn;
    }
}