<?php
$host = "127.0.0.1";  // or "localhost"
$user = "root";       // default user
$pass = "";           // default password is empty
$db   = "website";  // database you created

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>