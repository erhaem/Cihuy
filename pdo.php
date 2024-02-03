<?php

ini_set("display_errors", 1);

function getPDO() {
	$username = "root";
	$password = "";
	$dbname = "dbcihuy";
	
	$options = [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
	];

	$pdo = new PDO("mysql:host=localhost;dbname={$dbname};charset=utf8mb4", $username, $password, $options);
	return $pdo;
}

return getPDO();