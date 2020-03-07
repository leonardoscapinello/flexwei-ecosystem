<?php
require_once("../../src/properties/index.php");
if ($session->isLogged()) {
    header("location: " . $modules->getHome());
    die;
}