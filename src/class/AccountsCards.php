<?php

class AccountsCards
{

    private $id_account_card;
    private $id_account;
    private $id_card_external;
    private $fingerprint;
    private $holder;
    private $last_digits;
    private $brand;
    private $country;
    private $expiration_date;
    private $insert_time;
    private $date_created;
    private $date_updated;
    private $is_active;
    private $is_valid;
    private $is_default;


    public function __construct($id_card = 0, $id_account = 0)
    {
        $this->load($id_card, $id_account);
    }

    public function load($id_card = 0, $id_account = 0)
    {
        global $database;
        global $text;
        global $account;
        global $numeric;
        if (!$id_account || intval($id_account) === 0) $id_account = $account->getIdAccount();
        if (not_empty($id_account) && $numeric->is_number($id_account)) {
            $database->query("SELECT * FROM accounts_cards WHERE id_account = ? AND (id_account_card = ? OR MD5(id_account_card)  = ?)");
            $database->bind(1, $id_account);
            $database->bind(2, $id_card);
            $database->bind(3, $id_card);
            $result = $database->resultsetObject();
            if ($result && count(get_object_vars($result)) > 0) {
                foreach ($result as $key => $value) {
                    $this->$key = $value;
                }
                return true;
            }
        }
        return false;
    }

    public function list($id_account = 0)
    {
        global $database;
        global $session;
        global $numeric;
        if (!$id_account || intval($id_account) === 0) $id_account = $session->getIdAccount();
        if (not_empty($id_account) && $numeric->is_number($id_account)) {
            $database->query("SELECT id_account_card, brand, last_digits, is_valid, is_default FROM accounts_cards WHERE id_account = ? AND is_active = 'Y'");
            $database->bind(1, $id_account);
            $result = $database->resultset();
            if ($result && count($result) > 0) {
                return $result;
            }
        }
        return array();
    }

    private function cardExists($number, $expire)
    {
        global $database;
        global $account;
        global $security;
        try {
            $id_account = $account->getIdAccount();
            $last_digits = substr($number, -4);
            $database->query("SELECT id_account_card, last_digits, expiration_date FROM accounts_cards WHERE id_account = ? AND is_active = 'Y'");
            $database->bind(1, $id_account);
            $result = $database->resultset();
            if (count($result) > 0) {
                for ($i = 0; $i < count($result); $i++) {
                    $db_last_digits = $result[$i]['last_digits'];
                    $db_last_digits = $security->decrypt($db_last_digits);
                    $db_expiration_date = $result[$i]['expiration_date'];
                    $db_expiration_date = $security->decrypt($db_expiration_date);
                    if ($last_digits === $db_last_digits) {
                        if ($expire === $db_expiration_date) {
                            return $result[$i]['id_account_card'];
                        }
                    }
                }
            }
        } catch (Exception $exception) {
            error_log($exception);
        }
        return false;
    }

    public function register($number, $holder, $expire_month, $expire_year, $cvv)
    {
        global $database;
        global $account;
        global $date;
        global $security;
        global $creditCard;
        $lastid = 0;
        $err = $errmsg = "";
        try {

            $id_account = $account->getIdAccount();
            $expire = $expire_month . $expire_year;

            if ($this->cardExists($number, $expire)) return $this->cardExists($number, $expire);

            // CHECK IF IS A VALID CREDIT CARD

            if (!$creditCard->validate($number, $err, $errmsg)) return array(0, $errmsg);


            $card = (array)$this->create($number, $holder, $expire, $cvv);
            $security->setIdAccount($id_account);

            if (count($card) > 0) {
                $id_card = $security->encrypt($card['id']);
                $fingerprint = $security->encrypt($card['fingerprint']);
                $holder = $security->encrypt($card['holder_name']);
                $last_digits = $security->encrypt($card['last_digits']);
                $brand = $security->encrypt($card['brand']);
                $country = $security->encrypt($card['country']);
                $expiration_date = $security->encrypt($card['expiration_date']);
                $date_created = $date->str2date($card['date_created']);
                $date_updated = $date->str2date($card['date_updated']);
                $valid = $card['valid'];

                if ($valid === 1 || $valid === "1") {
                    $valid = "Y";
                } else {
                    $valid = "N";
                }


                $database->query("INSERT INTO accounts_cards (id_account, id_card_external, fingerprint, holder, last_digits, brand, country, expiration_date, date_created, date_updated, is_valid) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
                $database->bind(1, $id_account);
                $database->bind(2, $id_card);
                $database->bind(3, $fingerprint);
                $database->bind(4, $holder);
                $database->bind(5, $last_digits);
                $database->bind(6, $brand);
                $database->bind(7, $country);
                $database->bind(8, $expiration_date);
                $database->bind(9, $date_created);
                $database->bind(10, $date_updated);
                $database->bind(11, $valid);
                $database->execute();

                $lastid = $database->lastInsertId();

            }

            return array($lastid, "Successo");

        } catch (Exception $exception) {
            error_log($exception);
        }

        return array(0, $errmsg);

    }

    private function create($number, $holder, $expire, $cvv)
    {
        global $pagarme;
        try {
            $card = $pagarme->cards()->create([
                'holder_name' => $holder,
                'number' => $number,
                'expiration_date' => $expire,
                'cvv' => $cvv
            ]);
            return $card;
        } catch (Exception $exception) {
            error_log($exception);
        }
        return array();
    }

    /**
     * @return String
     */
    public function getIdAccountCard()
    {
        return $this->id_account_card;
    }

    /**
     * @return mixed
     */
    public function getIdAccount()
    {
        return $this->id_account;
    }

    /**
     * @return mixed
     */
    public function getIdCardExternal()
    {
        return $this->id_card_external;
    }

    /**
     * @return mixed
     */
    public function getHolder()
    {
        return $this->holder;
    }

    /**
     * @return mixed
     */
    public function isActive()
    {
        return $this->is_active === "Y" ? true : false;
    }

    /**
     * @return mixed
     */
    public function getCardLastDigits()
    {
        return $this->card_last_digits;
    }

    /**
     * @return mixed
     */
    public function getCardCompany()
    {
        return $this->card_company;
    }

    /**
     * @return mixed
     */
    public function getInsertTime()
    {
        return $this->insert_time;
    }

    /**
     * @param mixed $id_card_external
     */
    public function setIdCardExternal($id_card_external)
    {
        $this->id_card_external = $id_card_external;
    }

    /**
     * @param mixed $card_holder
     */
    public function setCardHolder($card_holder)
    {
        $this->card_holder = $card_holder;
    }

    /**
     * @param mixed $card_last_digits
     */
    public function setCardLastDigits($card_last_digits)
    {
        $this->card_last_digits = $card_last_digits;
    }

    /**
     * @param mixed $card_company
     */
    public function setCardCompany($card_company)
    {
        $this->card_company = $card_company;
    }


}