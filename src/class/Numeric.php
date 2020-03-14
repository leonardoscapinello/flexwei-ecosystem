<?php
setlocale(LC_MONETARY, 'en_US');

class Numeric
{

    private $number = 0;

    public function is_number($number)
    {
        if (preg_match('/^[0-9,.]+$/', $number)) {
            return true;
        }
        return false;
    }

    public function cents($number)
    {
        return $number * 100;
    }

    public function money($number)
    {
        if ($this->is_number($number)) {
            return number_format($number, 2, ",", ".");
        }
        return number_format(0, 2, ",", ".");
    }

    public function noDecimal($number)
    {
        if ($this->is_number($number)) {
            return number_format($number, 0, ",", ".");
        }
        return number_format(0, 0, ",", ".");
    }

    public function isIdentity($number)
    {
        if ($number !== null && $number !== "") {
            if ($this->is_number($number)) {
                if ($number > 0) {
                    return true;
                }
            }
        }
    }

    public function weight($number)
    {
        if ($number !== null && $number !== "") {
            if ($this->is_number($number)) {
                if ($number < 1000) {
                    return round($number) . "g";
                } else {
                    $weight = ($number / 1000);

                    return round($weight, 1) . "kg";

                }
            }
        }
    }

    public function biggerZero($number)
    {
        if ($number !== null && $number !== "") {
            if ($this->is_number($number)) {
                if ($number > 0) {
                    return true;
                }
            }
        }
    }

    public function percent($number)
    {
        return round($number, 2) . "%";
    }

    public function numberToHoursMinutes($time, $format = '%02d:%02d')
    {
        if ($time < 1) {
            return;
        }
        $hours = floor($time / 60);
        $minutes = ($time % 60);
        return sprintf($format, $hours, $minutes);
    }

    public function removeEverythingNotNumber($number)
    {
        return (preg_replace("/[^0-9]/", "", $number));
    }

    public function placeDecimalDigits($number)
    {
        $number = sprintf("%03d", $number);
        return sprintf('%.2f', $number / 100);;
    }

    public function zeroFill($number, $quantity)
    {
        $number = sprintf("%0" . $quantity . "d", $number);
        return $number;
    }

}