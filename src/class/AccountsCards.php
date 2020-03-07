<?php

class AccountsCards
{

    private $id_account_card;
    private $id_account;
    private $id_card_external;
    private $card_holder;
    private $card_last_digits;
    private $card_company;
    private $insert_time;
    private $is_active;

    public function __construct($id_account = 0)
    {
        $this->load($id_account);
    }

    public function load($id_card = 0, $id_account = 0)
    {
        global $database;
        global $text;
        global $session;
        global $numeric;
        if (!$id_account || intval($id_account) === 0) $id_account = $session->getIdAccount();
        if (not_empty($id_account) && $numeric->is_number($id_account)) {
            $database->query("SELECT * FROM accounts_cards WHERE id_account = ? AND id_card = ?");
            $database->bind(1, $id_account);
            $database->bind(2, $id_card);
            $result = $database->resultsetObject();
            if ($result && count(get_object_vars($result)) > 0) {
                foreach ($result as $key => $value) {
                    $this->$key = $text->base64_decode($value);
                }
                return true;
            }
        }
        return false;
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
    public function getCardHolder()
    {
        return $this->card_holder;
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