<?php

class Objects
{

    public function json_encode($str)
    {
        return json_encode($str);
    }

    public function stringEncapsulate($str)
    {
        return "'" . $str . "'";
    }

    public function fastStr($str)
    {
        return $str;
    }

}