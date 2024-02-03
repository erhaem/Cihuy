<?php

if (empty($_GET['url'])) {
    http_response_code(403);
    die("403 Forbidden");
}

$pdo = require_once __DIR__."/pdo.php";

$stmt = $pdo->prepare(<<<SQL
    SELECT `destination_url`, `slug_url`, `hits`
    FROM  `urls` WHERE `slug_url` = :slug_url
    LIMIT 1
SQL);
$stmt->execute(["slug_url" => $_GET['url']]);

$result = $stmt->fetch();

if (empty($result)) {
    http_response_code(404);
    die("404 Not Found");
}

$stmt = $pdo->prepare(<<<SQL
    UPDATE `urls` SET `hits` = :hits
    WHERE `slug_url` = :slug_url
SQL);
$stmt->execute([
    "hits" => $result['hits'] + 1, 
    "slug_url" => $result['slug_url']
]);

header("location: {$result['destination_url']}");

?>