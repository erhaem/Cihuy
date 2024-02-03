<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("location: login.php");
    die;
}

if (empty($_GET['id'])) {
    http_response_code(403);
    die("id is needed");
}

$pdo = require_once __DIR__."/pdo.php";

$stmt = $pdo->prepare("SELECT `id`, `user_id` FROM `urls` WHERE `id` = :id");
$stmt->execute(["id" => $_GET['id']]);

$data = $stmt->fetch();

if (empty($data)) {
    http_response_code(404);
    die("No data");
}

/* Check if the url belongs to the active user  */
if ($data['user_id'] !== $_SESSION['user_id']) {
    http_response_code(403);
    die("You're not allowed to edit this url");
}

$stmt = $pdo->prepare("DELETE FROM `urls` WHERE `id` = :id");
$stmt->execute(["id" => $data['id']]);

header("location: dashboard.php");
die;