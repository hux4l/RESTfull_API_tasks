<?php

use Dotenv\Dotenv;

require __DIR__ . "/vendor/autoload.php";

// api_key 5d0b6dc307fb3687a601e2a833ba7d1b

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $database = new Database($_ENV["DB_HOST"],
                                                $_ENV["DB_NAME"],
                                                $_ENV["DB_USER"],
                                                $_ENV["DB_PASS"]);

    $conn = $database->getConnection();

    // sql
    $sql = "INSERT INTO user (name, username, password_hash, api_key)
                 VALUES (:name, :username, :password_hash, :api_key)";

    // prepare statement
    $stmt = $conn->prepare(($sql));

    // hash the password by default algorithm
    $password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // generate api_key
    $api_key = bin2hex((random_bytes(16)));

    // bind values
    $stmt->bindValue(":name", $_POST["name"]. PDO::PARAM_STR);
    $stmt->bindValue(":username", $_POST["username"], PDO::PARAM_STR);
    $stmt->bindValue(":password_hash", $password_hash, PDO::PARAM_STR);
    $stmt->bindValue(":api_key", $api_key, PDO::PARAM_STR);

    echo "Thank you for registering. Your API key is ", $api_key;
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>

    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@latest/css/pico.min.css">
</head>
<body>
    
    <main class="container">
    <h1>Register</h1>
    <form method="POST">

        <label for="name">Name
            <input name="name" id="name">
        </label>

        <label for="username">Username
            <input name="username" id="username">
        </label>

        <label for="password">
            Password
            <input type="password" name="password" id="password">
        </label>

        <button>Register</button>
    </main>

    </form>
</body>
</html>