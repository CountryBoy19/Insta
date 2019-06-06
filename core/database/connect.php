<?php

/* Establishing the connection */
$database = new mysqli(
    $config['database_server'],
    $config['database_username'],
    $config['database_password'],
    $config['database_name']
);

/* Debugging */
if($database->connect_error) {
    die('The connection to the database failed ! Please edit the "core/config/config.php" file and make sure your database connection details are correct!');
}

/* Encoding */
//$database->set_charset('utf8mb4');

/* Initiate the Database Class */
Database::$database = $database;
