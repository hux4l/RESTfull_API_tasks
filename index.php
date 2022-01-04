<?php

if ( ! empty($_GET['name'])) {

    $response = file_get_contents("https://api.agify.io/?name={$_GET['name']}");
    
    $data = json_decode($response, true);

    $age = $data["age"];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

<?php if (isset($age)): ?>

    Age: <?= $age ?>

<?php endif; ?>
    <form action="">
        <label for="name">Name</label>
        <input type="text" id="name" name="name">

        <button>Guess age</button>
    </form>
</body>
</html>