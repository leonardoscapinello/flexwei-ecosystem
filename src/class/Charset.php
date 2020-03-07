<?php

class Charset
{

    private $str;

    public function setString($string)
    {
        $this->str = $string;
    }

    public function utf8()
    {
        $string = $this->str;
        if ($this->isString()) {
            $string = iconv(mb_detect_encoding($string, "UTF-8,ISO-8859-1"), "UTF-8", $string);
            return $string;
            $encoding = mb_detect_encoding($string, mb_detect_order(), false);
            if ($encoding == "UTF-8") {
                $string = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
            }
            $out = iconv(mb_detect_encoding($string, mb_detect_order(), false), "UTF-8//IGNORE", $string);
            return $out;
        }
        return null;
    }

    public function singleUTF8($string)
    {
        $this->setString($string);
        return htmlentities($this->utf8());
    }

    public function uppercase()
    {
        $string = $this->str;
        if ($this->isString()) {
            $string = mb_convert_case($string, MB_CASE_UPPER, "UTF-8");
            return $string;
        }
        return null;
    }

    public function lowecase()
    {
        $string = $this->str;
        if ($this->isString()) {
            $string = mb_convert_case($string, MB_CASE_LOWER, "UTF-8");
            return $string;
        }
        return null;
    }

    public function isString()
    {
        if ($this->str !== null) {
            if (is_string($this->str)) {
                if (strlen($this->str) > 0) {
                    return true;
                }
            }
        }
        return false;
    }

}