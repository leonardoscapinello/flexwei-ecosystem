<?php

class Security
{

    private $key;
    private $system_key = "7077169566";
    private $id_account;


    private function getSystemKey()
    {
        return $this->system_key;
    }

    private function getUserKey()
    {
        global $database;
        $id_account = $this->getIdAccount();
        if (not_empty($id_account)) {
            $database->query("SELECT insert_time FROM accounts WHERE id_account = ?");
            $database->bind(1, $id_account);
            $resultset = $database->resultset();
            if (count($resultset) > 0) {
                $time = $resultset[0]['insert_time'];
                $register_day = date("d", strtotime($time));
                $register_month = date("m", strtotime($time));
                $day_token = $this->getDayToken($register_day);
                $month_token = $this->getMonthToken($register_month);
                $this->key = $day_token . "-" . $this->system_key . "/" . $month_token;
                return $this->key;
            }
        }
        return null;
    }

    private function getIdAccount()
    {
        global $session;
        global $numeric;
        $id_account_sess = $session->getIdAccount();
        if (not_empty($this->id_account) && $numeric->is_number($this->id_account)) return $this->id_account;
        if (not_empty($id_account_sess) && $numeric->is_number($id_account_sess)) return $id_account_sess;
    }

    public function setIdAccount($id_account)
    {
        $this->id_account = $id_account;
    }

    public function encrypt($string)
    {
        if (not_empty($string) && not_empty($this->getUserKey())) {
            $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
            $iv = openssl_random_pseudo_bytes($ivlen);
            $ciphertext_raw = openssl_encrypt($string, $cipher, $this->getUserKey(), $options = OPENSSL_RAW_DATA, $iv);
            $hmac = hash_hmac('sha256', $ciphertext_raw, $this->getUserKey(), $as_binary = true);
            $ciphertext = base64_encode($iv . $hmac . $ciphertext_raw);
            return $ciphertext;
        }
        return null;
    }


    public function decrypt($string)
    {
        if (not_empty($string) && not_empty($this->getUserKey())) {
            $c = base64_decode($string);
            $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
            $iv = substr($c, 0, $ivlen);
            $hmac = substr($c, $ivlen, $sha2len = 32);
            $ciphertext_raw = substr($c, $ivlen + $sha2len);
            $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $this->getUserKey(), $options = OPENSSL_RAW_DATA, $iv);
            $calcmac = hash_hmac('sha256', $ciphertext_raw, $this->getUserKey(), $as_binary = true);
            if (hash_equals($hmac, $calcmac))//PHP 5.6+ timing attack safe comparison
            {
                return $original_plaintext;
            }
        }
        return null;
    }

    public function hash($string, $encrypt = true)
    {
        if (not_empty($string)) {
            $md5 = md5($string);
            $sha1 = sha1($md5);
            $sha256 = hash("sha256", $sha1);
            $reverse = strrev($sha256);
            $pw_hash = $encrypt ? $this->encrypt($reverse) : $reverse;
            return $pw_hash;
        }
        return null;
    }

    public function random($length = 8)
    {
        $salt = array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9));
        $maxIndex = count($salt) - 1;

        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $index = mt_rand(0, $maxIndex);
            $result .= $salt[$index];
        }
        return $result;
    }

    private function getDayToken($day)
    {
        global $numeric;
        if ($numeric->is_number($day) && $day > 0 && $day < 32) {
            $num = $filled_int = sprintf("%04d", $day);
            $num = md5($num);
            return substr($num, 0, 16);
        }
    }

    private function getMonthToken($month)
    {
        global $numeric;
        if ($numeric->is_number($month) && $month > 0 && $month < 32) {
            $num = $filled_int = sprintf("%04d", $month);
            $num = md5($num);
            return substr($num, 0, 16);
        }
    }


}
