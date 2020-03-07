<?php
if (!$session->isLogged()) {
    header("location: " . $properties->getLoginURL());
    die;
}