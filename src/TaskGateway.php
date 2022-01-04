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
    public function getAll(): array
    {
        // sql statement
        $sql = "
            SELECT *
            FROM task
            ORDER BY name";

        // execute statement on PDO object
        $stmt = $this->conn->query($sql);

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
    public function get(string $id): array | false
    {
        $sql = "SELECT *
                    FROM task
                    WHERE id = :id";

        // to prevent sql injection we make statement
        $stmt = $this->conn->prepare($sql);

        // bind param to id
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

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
    public function create(array $data): string
    {
        $sql = "INSERT INTO task (name, priority, is_completed) 
                    VALUES (:name, :priority, :is_completed)";

        $stmt = $this->conn->prepare($sql);

        // bind parameters
        $stmt->bindValue(":name", $data["name"], PDO::PARAM_STR);
        if (empty($data['priority'])) {

            $stmt->bindValue(":priority", null, PDO::PARAM_NULL);

        } else {

            $stmt->bindValue(":priority", $data["priority"]);

        }

        $stmt->bindValue(":is_completed", $data["is_completed"] ?? false, PDO::PARAM_BOOL);

        $stmt->execute();

        return $this->conn->lastInsertId();
    }
}