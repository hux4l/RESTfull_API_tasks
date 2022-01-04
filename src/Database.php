<?php

class Database
{
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
        // domain server name
        $dsn = "mysql:host={$this->host};dbname={$this->name};charset=utf8";
        
        // return new PDO object
        return new PDO($dsn, $this->user, $this->password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }
}