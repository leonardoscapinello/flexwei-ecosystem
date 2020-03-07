<?php
require_once("../../properties/index.php");

$account->setIdAccount(1);
if ($account->resetPassword("Dds123dds", "Dds123dds")) {
    echo "reseted";
} else {
    echo "nreseted";
}