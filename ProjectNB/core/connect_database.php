<?php
$servername = "localhost";
$username   = "std6730202084";
$password   = "D1w!uYqF";
$dbname     = "it_std6730202084";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Database connection is ready in $conn for includes. Do not run queries here.
?>