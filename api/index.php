<?php

// enable strict mode
declare(strict_types=1);
require __DIR__ . "/bootstrap.php";
// enable displaying errors
//ini_set("display_errors", "On");

// parse url behind address
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// split to parts separated by /
$parts = explode("/", $path);

// get first part 
$resource = $parts[3];

// second part to get id, if not exists set to null
$id = $parts[4] ?? null;

// get error if other than /tasks or /tasks/id given
if ($resource != "tasks") {

    // if invalid url return 404 header
    //header("{$_SERVER['SERVER_PROTOCOL']} 404 Not Found!");
    // response code
    http_response_code(404);
    exit;
}


$database = new Database($_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASS"]);

$user_gateway = new UserGateway($database);

$auth = new Auth($user_gateway);

if (! $auth->authenticateAPIKey()) {
    exit;
}

// get user id and store it
$user_id = $auth->getUserID();


// object of task gateway
$task_gateway = new TaskGateway($database);

// create controller
$controller = new TaskController($task_gateway, $user_id);

$controller->processRequest($_SERVER['REQUEST_METHOD'], $id);