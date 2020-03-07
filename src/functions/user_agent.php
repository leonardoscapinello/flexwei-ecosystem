<?php

function get_user_agent()
{
    $iPod = strpos($_SERVER['HTTP_USER_AGENT'], "iPod");
    $iPhone = strpos($_SERVER['HTTP_USER_AGENT'], "iPhone");
    $iPad = strpos($_SERVER['HTTP_USER_AGENT'], "iPad");
    $android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");
    //file_put_contents('./public/upload/install_log/agent', $_SERVER['HTTP_USER_AGENT']);
    if ($iPad || $iPhone || $iPod) {
        return 'ios';
    } else if ($android) {
        return 'android';
    } else {
        return 'pc';
    }
}