<?php

require dirname(__DIR__) . "/vendor/autoload.php";

// htaccsess redirects all to this index.php from address /api

// error handler function
set_error_handler("ErrorHandler::handleError");

/// exception handler enable
set_exception_handler("ErrorHandler::handleException");

//load database access 
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

header("Content-type: application/json; charset=UTF-8");