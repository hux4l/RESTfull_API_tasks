<?php

class TaskController
{
    public function __construct(private TaskGateway $gateway)
    {
        
    }

    // ?string: $id checks $id only if exists
    public function processRequest(string $method, ?string $id) : void
    {
        if ($id === null) {

            if ($method == 'GET') {
                // call getAll method
                echo json_encode($this->gateway->getAll());

            } elseif ($method == 'POST') {
                
                // decode data as json
                $data = (array) json_decode(file_get_contents("php://input"), true);

                // call create function
                $id = $this->gateway->create($data);

                $this->respondCreated($id);


            } else {
                // return method not allowed
                $this->respondMethodNotAllowed("GET, POST");
            }
        } else {

            // get tasks with id
            $task = $this->gateway->get($id);

            // check if task exists
            if ($task === false) {

                // if not found return 404
                $this->respondNotFound($id);
                return;
            }

            switch ($method) {

                case "GET":
                    echo json_encode($task);
                    break;

                case "PATCH":
                    echo "update $id";
                    break;

                case "DELETE":
                    echo "delete $id";
                    break;

                default:
                    $this->respondMethodNotAllowed("GET, PATCH, DELETE");
                    break;
            }
        }
    }

    // function that return allowed methods on url
    private function respondMethodNotAllowed(string $allowed_methods): void
    {
        http_response_code(405);
        header("Allow: $allowed_methods");
    }

    private function respondNotFound(string $id): void
    {
        http_response_code(404);
        echo json_encode(["message" => "Task with ID $id not found"]);
    }

    private function respondCreated(string $id): void
    {
        http_response_code(201);
        echo json_encode(["message" => "Task created", "id" => $id]);
    }
}