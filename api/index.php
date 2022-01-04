<?php

// enable strict mode
declare(strict_types=1);
// enable displaying errors
//ini_set("display_errors", "On");

require dirname(__DIR__) . "/vendor/autoload.php";

// htaccsess redirects all to this index.php from address /api

/// exception handler enable
set_exception_handler("ErrorHandler::handleException");

//load database access 
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

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

header("Content-type: application/json; charset=UTF-8");

$database = new Database($_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASS"]);

$database->getConnection();

// object of task gateway
$task_gateway = new TaskGateway($database);

// create controller
$controller = new TaskController($task_gateway);

$controller->processRequest($_SERVER['REQUEST_METHOD'], $id);