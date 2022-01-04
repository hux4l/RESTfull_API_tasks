<?php

use function PHPSTORM_META\type;

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

                // validate data
                $errors = $this->getValidationErrors($data);

                if (!empty($errors)) {

                    $this->respondUnprocessableEntity($errors);
                    return;

                }

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

                    $data = (array) json_decode(file_get_contents("php://input"), true);

                    // validate data, we pass false so the task is not new so name will not be required
                    $errors = $this->getValidationErrors($data, false);

                    if (!empty($errors)) {

                        $this->respondUnprocessableEntity($errors);
                        return;
                    
                    }

                    $rows = $this->gateway->update($id, $data);
                    echo json_encode(["message" => "Task updated", "rows" => $rows]);
                    break;

                case "DELETE":
                    $rows = $this->gateway->delete($id);
                    echo json_encode(["message" => "Task with id $id deleted", "rows" => $rows]);
                    break;

                default:
                    $this->respondMethodNotAllowed("GET, PATCH, DELETE");
                    break;
            }
        }
    }

    // if data is not valid
    private function respondUnprocessableEntity(array $errors): void
    {
        http_response_code(404);
        echo json_encode(["errors" => $errors]);
    }

    // function that return allowed methods on url
    private function respondMethodNotAllowed(string $allowed_methods): void
    {
        http_response_code(405);
        header("Allow: $allowed_methods");
    }

    // return error message when task with $id was not found
    private function respondNotFound(string $id): void
    {
        http_response_code(404);
        echo json_encode(["message" => "Task with ID $id not found"]);
    }

    // return response that task was created
    private function respondCreated(string $id): void
    {
        http_response_code(201);
        echo json_encode(["message" => "Task created", "id" => $id]);
    }

    // validate data
    private function getValidationErrors(array $data, bool $is_new = true): array
    {
        $errors = [];

        // check if name is given
        if ($is_new && empty($data["name"])) {
            $errors[] = "name is required";
        }

        // check if priority is not empty and is valid integer
        if (!empty($data["priority"]) && filter_var($data["priority"], FILTER_VALIDATE_INT) === false) {
                $errors [] = "priority must be an integer";
        }

        return $errors;
    }
}