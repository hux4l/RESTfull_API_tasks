<?php
// will decide what to do base on task
class TaskGateway
{
    // filed for connection
    private PDO $conn;

    // constructor that will accept database connection as param
    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    // get all records from MySQL
    public function getAllForUser(int $user_id): array
    {
        // sql statement
        $sql = "
            SELECT *
            FROM task
            WHERE user_id = :user_id
            ORDER BY name";

        // execute statement on PDO object
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);

        $stmt->execute();

        // create empty array
        $data = [];

        // return fetched data all data
        // return $stmt->fetchAll(PDO::FETCH_ASSOC); 
        // fetch rows
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // convert to bool true or false
            $row['is_completed'] = (bool) $row['is_completed'];

            $data[] = $row;
        }
        return $data;
    }

    // get single record
    public function getForUser(int $user_id, string $id): array | false
    {
        $sql = "SELECT *
                    FROM task
                    WHERE id = :id
                    AND user_id = :user_id";

        // to prevent sql injection we make statement
        $stmt = $this->conn->prepare($sql);

        // bind param to id
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);

        // execute statement
        $stmt->execute();

        // will return array or false if not found
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data !== false) {
            $data['is_completed'] = (bool) $data['is_completed'];
        }

        return $data;
    }

    // create task
    public function createForUser(int $user_id, array $data): string
    {
        $sql = "INSERT INTO task (name, priority, is_completed, user_id) 
                    VALUES (:name, :priority, :is_completed, :user_id)";

        $stmt = $this->conn->prepare($sql);

        // bind parameters
        $stmt->bindValue(":name", $data["name"], PDO::PARAM_STR);

        if (empty($data['priority'])) {

            $stmt->bindValue(":priority", null, PDO::PARAM_NULL);

        } else {

            $stmt->bindValue(":priority", $data["priority"]);

        }

        $stmt->bindValue(":is_completed", $data["is_completed"] ?? false, PDO::PARAM_BOOL);

        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);

        $stmt->execute();

        return $this->conn->lastInsertId();
    }

    public function updateForUser(int $user_id, string $id, array $data): int
    {
        $fields = [];

        // check values if we need to include them in sql
        if (!empty($data["name"])) {
            $fields["name"] = [
                $data["name"],
                PDO::PARAM_STR
            ];
        }

        // need to check for key name, null or false is as empty value
        if (array_key_exists("priority", $data)) {
            $fields["priority"] = [
                $data["priority"],
                $data["priority"] === null ? PDO::PARAM_NULL : PDO::PARAM_INT
            ];
        }

        if (array_key_exists("is_completed", $data)) {
            $fields["is_completed"] = [
                $data["is_completed"],
                PDO::PARAM_BOOL
            ];
        }

        // if no fields, do nothing
        if (empty($fields)) {
            return 0;
        } else {
            // create key array from array of keys
        $sets = array_map(function($value) {
            return "$value = :$value";
        }, array_keys($fields));

        // create sql
        $sql = "UPDATE task"
                . " SET " . implode(", ", $sets)
                . " WHERE id = :id"
                . " AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($sql);

        $stmt-> bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);

        // bind param for each fields in array
        foreach ($fields as $name => $values) {

            $stmt->bindValue(":$name", $values[0], $values[1]);

        }

        $stmt->execute();

        return $stmt->rowCount();
        }        
    }

    public function deleteForUser(int $user_id, string $id): int
    {
        $sql = "DELETE FROM task
                    WHERE id = :id
                    AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":user:id", $user_id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }
}