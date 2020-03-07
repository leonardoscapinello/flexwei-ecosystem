<?php

function is_selected_by_request($field_value, $request_index)
{
    $return = "";
    try {
        if (isset($_REQUEST[$request_index]) && $_REQUEST[$request_index] !== "") {
            if (strlen($_REQUEST[$request_index]) > 0) {
                if ($field_value === $_REQUEST[$request_index]) {
                    $return = "selected=\"selected\"";
                }
            }
        }
    } catch (Exception $exception) {
        error_log($exception);
    } finally {
        return $return;
    }
}

function is_selected($field_value, $external_value)
{
    $return = "";

    if ($field_value == $external_value) {
        $return = "selected=\"selected\"";
    }
    return $return;
}

?>