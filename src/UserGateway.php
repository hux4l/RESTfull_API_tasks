<?php

class UserGateway
{
    // filed for connection
    private PDO $conn;

    // constructor that will accept database connection as param
    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    public function getByAPIKey(string $key): array | false
    {
        $sql = "SELECT *
                    FROM user
                    WHERE api_key = :api_key";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":api_key", $key, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}