<?php

// enable strict mode
declare(strict_types=1);
// enable displaying errors
//ini_set("display_errors", "On");

require dirname(__DIR__) . "\src\TaskController.php";
require dirname(__DIR__) . "\src\ErrorHandler.php";
require dirname(__DIR__) . "\src\Database.php";

// htaccsess redirects all to this index.php from address /api

/// exception handler enable
set_exception_handler("ErrorHandler::handleException");

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

$database = new Database("localhost", "api_db", "hux4l", "Moncici1234.");

$database->getConnection();

$controller = new TaskController;

$controller->processRequest($_SERVER['REQUEST_METHOD'], $id);