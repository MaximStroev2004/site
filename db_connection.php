<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'cx03897_max');
define('DB_PASSWORD', 'yfcnjqxbdjcnm');
define('DB_NAME', 'cx03897_max');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
