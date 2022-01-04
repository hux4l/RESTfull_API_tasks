<?php

class TaskController
{

    // ?string: $id checks $id only if exists
    public function processRequest(string $method, ?string $id) : void
    {
        if ($id === null) {

            if ($method == 'GET') {
                echo "index";
            } else if ($method == 'POST') {
                echo "create";
            } else {
                // return method not allowed
                $this->respondMethodNotAllowed("GET, POST");
            }
        } else {

            switch ($method) {

                case "GET":
                    echo "show $id";
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
}