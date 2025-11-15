<?php
$host = "localhost";
$user = "root";
$pass = "root";     
$port = 3306;

// Let op: hier laten we $db weg
$mysqli = new mysqli($host, $user, $pass, "", $port);

if ($mysqli->connect_error) {
    die("Connectie mislukt: " . $mysqli->connect_error);
}
?>