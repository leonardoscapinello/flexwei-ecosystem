<?php
require_once("../properties/index.php");
$username = get_request("username");
$password = get_request("password");

$session->setUsername($username);
$session->setPassword($password);

if ($session->isLogged()) {
    http_response_code(200);
    die;
} else {
    if ($session->createSession()) {
        http_response_code(200);
    } else {
        http_response_code(401);
    }
}