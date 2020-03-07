<?php

function get_request($index)
{

    if (isset($_REQUEST[$index]) && $_REQUEST[$index] !== "") {
        if (strlen($_REQUEST[$index]) > 0) {
            return $_REQUEST[$index];
        }
    }
    return null;

}


?>