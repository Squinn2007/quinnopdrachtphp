<?php
$servername = "mysql";
$username = "root";
$password = "password";
$dbname = "MyDB";

$conn = new mysqli($servername, $username, $password, $dbname);


// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully";

?>