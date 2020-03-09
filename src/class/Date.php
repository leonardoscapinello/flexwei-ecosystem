<?php

class Date
{

    private $additional_time;
    private $date;

    private $date_format = "d/m/Y";
    private $datetime_format = "d/m/Y H:i";

    public function formatDate($date)
    {
        return date($this->date_format, strtotime($date));
    }

    public function setCustomDateFormat($format)
    {
        $this->date_format = $format;
    }

    public function formatDateOrMessage($date, $message)
    {
        if (not_empty($date)) return date($this->date_format, strtotime($date));
        return $message;
    }

    public function formatDatetime($date)
    {
        return date($this->date_format, strtotime($date));
    }

    public function getDate()
    {
        return date($this->date_format);
    }

    public function getDatetime()
    {
        return date($this->datetime_format);
    }

    public function subtractDate($date, $number_of_days)
    {
        return date($this->date_format, strtotime("-" . $number_of_days . " days", strtotime($date)));
    }

    public function subtractDateBusinessDay($date, $number_of_days)
    {
        $newdate = date("Y-m-d", strtotime("-" . $number_of_days . " days", strtotime($date)));
        return $this->getNextBusinessDay($newdate);
    }

    public function sumDateDays($date, $number_of_days)
    {
        return date($this->date_format, strtotime("+" . $number_of_days . " days", strtotime($date)));
    }

    public function sumDateMonths($date, $number_of_months)
    {
        return date($this->date_format, strtotime("+" . $number_of_months . " months", strtotime($date)));
    }

    public function getNextBusinessDay($date)
    {
        $holiday_leave = $this->getDaysToLeaveHoliday($date);
        $left_holiday_date = date("Y-m-d", strtotime("$date +$holiday_leave days"));
        $next_workday = $this->getDaysToNextWorkday($left_holiday_date);
        $left_weekends = date($this->date_format, strtotime("$left_holiday_date +$next_workday days"));
        return $left_weekends;
    }


    public function getDaysOfDifference($date1, $date2)
    {
        $return = $this->getDifference($date1, $date2, "D");
        if ($date1 > $date2) {
            $return = ($return * -1);
        }
        return $return;
    }

    public function str2date($str, $format = "Y-m-d H:i:s")
    {
        return date($format, strtotime($str));
    }

    public function getMonthNameFromDate($date)
    {
        try {
            if ($date !== null) {
                $month = date("m", strtotime($date));
                $months = array(
                    '01' => 'Janeiro',
                    '02' => 'Fevereiro',
                    '03' => 'Março',
                    '04' => 'Abril',
                    '05' => 'Maio',
                    '06' => 'Junho',
                    '07' => 'Julho',
                    '08' => 'Agosto',
                    '09' => 'Setembro',
                    '10' => 'Outubro',
                    '11' => 'Novembro',
                    '12' => 'Dezembro'
                );
                return $months[$month];
            }
        } catch (Exception $exception) {
            error_log($exception);
        }
        return null;
    }

    /*
     * $return_type = array("Y", "M", "D", "H", "I", "S");
     * Y = Year | M = Month | D = Day | H = Hour | I = Minutes | S = Seconds
     */
    private function getDifference($date1, $date2, $return_type = "D")
    {


        $date1 = strtotime($date1);
        $date2 = strtotime($date2);
        $diff = abs($date2 - $date1);
        $years = floor($diff / (365 * 60 * 60 * 24));
        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
        $hours = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24) / (60 * 60));
        $minutes = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60) / 60);
        $seconds = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60 - $minutes * 60));


        $return = $days;
        if ($return_type === "Y") $return = $years;
        if ($return_type === "M") $return = $months;
        if ($return_type === "D") $return = $days;
        if ($return_type === "H") $return = $hours;
        if ($return_type === "I") $return = $minutes;
        if ($return_type === "S") $return = $seconds;

        return $return;
    }


    private function getDaysToNextWorkday($date)
    {
        $add_day = 0;
        $weekday = date('w', strtotime($date));
        if ($weekday == 6 || $weekday = 0) {
            do {
                $add_day++;
                $new_date = date('Y-m-d', strtotime("$date +$add_day Days"));
                $new_day_of_week = date('w', strtotime($new_date));
            } while ($new_day_of_week == 6 || $new_day_of_week == 0);
        }
        return $add_day;
    }

    private function getDaysToLeaveHoliday($date, $ignore_weekends = false)
    {
        $add_day = 0;
        while ($this->isHoliday($date)) {
            $date = date('Y-m-d', strtotime("$date +1 days"));
            $day_of_week = date('w', strtotime($date));
            if ($ignore_weekends && ($day_of_week != 6 || $day_of_week != 0)) {
                $add_day++;
            } else if (!$ignore_weekends) {
                $add_day++;
            }
        }
        return $add_day;
    }

    private function isHoliday($date)
    {
        $year = date("Y");
        $nextYear = $year + 1;
        $holidays = array("$year-01-01", "$year-01-02", "$year-04-21", "$year-05-01", "$year-06-11", "$year-09-07", "$year-10-12", "$year-11-02", "$year-11-15", "$year-12-24", "$year-12-25", "$year-12-26", "$year-12-27", "$year-12-28", "$year-12-29", "$year-12-30", "$year-12-31", "$nextYear-01-01");
        foreach ($holidays as $key) {
            if ($date === $key) {
                return true;
            }
        }
        return false;
    }

}
