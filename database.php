<?php

$mysqli = new mysqli('localhost', 'php_user', 'php_pass', 'news');

if($mysqli->connect_errno) {
	printf("Connection Failed: %s\n", $mysqli->connect_error);
	exit;
}
?>