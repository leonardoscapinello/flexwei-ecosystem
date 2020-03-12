<?php

class AccountsAddress
{

    private $id_account_address;
    private $id_account;
    private $street;
    private $neighborhood;
    private $city;
    private $state;
    private $country;
    private $zipcode;
    private $house_number;
    private $complement;
    private $insert_time;
    private $update_time;


    public function __construct($id_account = 0)
    {
        $this->load($id_account);
    }

    public function load($id_account = 0)
    {
        global $database;
        global $text;
        global $session;
        global $numeric;
        if (!$id_account || intval($id_account) === 0) $id_account = $session->getIdAccount();
        if (not_empty($id_account) && $numeric->is_number($id_account)) {
            $database->query("SELECT * FROM accounts_address WHERE id_account = ?");
            $database->bind(1, $id_account);
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


    public function isAddressRegistered_NotLoad($id_account = 0)
    {
        global $database;
        global $account;
        global $numeric;
        if (!$id_account || intval($id_account) === 0) $id_account = $account->getIdAccount();
        if (not_empty($id_account) && $numeric->is_number($id_account)) {
            $database->query("SELECT * FROM accounts_address WHERE id_account = ?");
            $database->bind(1, $id_account);
            $result = $database->resultsetObject();
            if ($result && count(get_object_vars($result)) > 0) {
                return true;
            }
        }
        return false;
    }

    public function save()
    {
        global $database;
        global $account;
        global $text;
        try {
            $id_account = $account->getIdAccount();
            if ($this->isAddressRegistered_NotLoad()) {
                $database->query("UPDATE accounts_address SET street = ?, neighborhood = ?, city = ?, state = ?, country = ?, zipcode = ?, complement = ?, house_number = ?, update_time = CURRENT_TIMESTAMP WHERE id_account = ?");
            } else {
                $database->query("INSERT INTO accounts_address (street, neighborhood, city,state, country, zipcode, complement, house_number, id_account) VALUES (?,?,?,?,?,?,?,?,?)");
            }
            $database->bind(1, $text->base64_encode($this->getStreet()));
            $database->bind(2, $text->base64_encode($this->getNeighborhood()));
            $database->bind(3, $text->base64_encode($this->getCity()));
            $database->bind(4, $text->base64_encode($this->getState()));
            $database->bind(5, $text->base64_encode($this->getCountry()));
            $database->bind(6, $text->base64_encode($this->getZipcode()));
            $database->bind(7, $text->base64_encode($this->getComplement()));
            $database->bind(8, $text->base64_encode($this->getNumber()));
            $database->bind(9, $id_account);
            $database->execute();
            return true;
        } catch (Exception $exception) {
            error_log($exception);
        }
        return false;
    }

    public function isAddressRegistered()
    {
        return $this->load();
    }

    /**
     * @return mixed
     */
    public function getIdAccountAddress()
    {
        return $this->id_account_address;
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
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @return mixed
     */
    public function getNeighborhood()
    {
        return $this->neighborhood;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    public function getCountrySingle()
    {
        return $this->country === "Brasil" ? "br" : $this->country;
    }

    /**
     * @return mixed
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }

    public function getMaskedZipcode()
    {

        global $numeric;
        global $text;
        $zipcode = str_replace("-", "", $text->removeSpace($this->zipcode));
        $zipcode = $numeric->zeroFill($zipcode, 8);
        return $text->mask($zipcode, "##.###-###");
    }

    /**
     * @return mixed
     */
    public function getInsertTime()
    {
        return $this->insert_time;
    }

    /**
     * @return mixed
     */
    public function getUpdateTime()
    {
        return $this->update_time;
    }

    /**
     * @return mixed
     */
    public function getNumber()
    {
        return $this->house_number;
    }

    /**
     * @return mixed
     */
    public function getComplement()
    {
        return $this->complement;
    }

    public function update($columns = array())
    {
        global $database;
        try {
            foreach ($columns AS $key => $value) {
                echo $key . "." . $value;
            }
        } catch (Exception $exception) {

        }
    }

    /**
     * @param mixed $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * @param mixed $neighborhood
     */
    public function setNeighborhood($neighborhood)
    {
        $this->neighborhood = $neighborhood;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @param mixed $zipcode
     */
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;
    }

    /**
     * @param mixed $number
     */
    public function setNumber($number)
    {
        $this->house_number = $number;
    }

    /**
     * @param mixed $complement
     */
    public function setComplement($complement)
    {
        $this->complement = $complement;
    }


}