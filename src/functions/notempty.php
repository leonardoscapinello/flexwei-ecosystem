<?php

function not_empty($str)
{
    if ($str !== null) {
        if ($str !== "" && strlen($str) > 0) {
            if (strlen(trim($str)) > 0) {
                return $str;
            }
        }
    }
    return false;
}

function notempty($str)
{
    return not_empty($str);
}