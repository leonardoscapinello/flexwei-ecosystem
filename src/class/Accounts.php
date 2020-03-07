<?php

class Accounts
{

    private $id_account;
    private $username;
    private $email;
    private $password;
    private $first_name;
    private $last_name;
    private $document;
    private $phone_number;
    private $insert_time;
    private $is_active;
    private $is_customer;

    public function __construct($id_account = 0)
    {
        global $database;
        global $text;
        global $session;
        global $numeric;
        if (!$id_account || intval($id_account) === 0) $id_account = $session->getIdAccount();
        if (not_empty($id_account) && $numeric->is_number($id_account)) {
            $database->query("SELECT * FROM accounts WHERE id_account = ?");
            $database->bind(1, $id_account);
            $result = $database->resultsetObject();
            if ($result && count(get_object_vars($result)) > 0) {
                foreach ($result as $key => $value) {
                    $this->$key = $text->utf8($value);
                }
            }
        }
    }

    public function resetPassword($new, $confirm_new)
    {
        global $database;
        global $numeric;
        global $security;
        global $session;
        try {
            if (strlen($new) > 5 && strlen($confirm_new) > 5) {
                if ($new === $confirm_new) {
                    if (notempty($this->getIdAccount())) {
                        if ($numeric->isIdentity($this->getIdAccount())) {
                            $security->setIdAccount($this->getIdAccount());
                            $pw = $security->hash($confirm_new, false);
                            $pw = $security->encrypt($pw);
                            $database->query("UPDATE accounts SET password = ? WHERE id_account = ?");
                            $database->bind(1, $pw);
                            $database->bind(2, $this->getIdAccount());
                            $database->execute();
                            $session->logoutFromAllSessions($this->getIdAccount());
                            return true;
                        }
                    }
                }
            }
        } catch (Exception $exception) {
            error_log($exception);
        }
        return false;
    }

    public function isCustomer()
    {
        if ($this->is_customer === "Y") return true;
        return false;
    }

    public function getIdAccount()
    {
        return $this->id_account;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getFirstName()
    {
        return $this->first_name;
    }

    public function getLastName()
    {
        return $this->last_name;
    }

    public function getFullName()
    {
        return $this->first_name . " " . $this->last_name;
    }

    public function getNameFirstLetter()
    {
        return substr($this->first_name, 0, 1);
    }

    public function getDocument()
    {
        return $this->document;
    }

    public function getMaskedDocument()
    {
        global $numeric;
        global $text;
        $document = $numeric->zeroFill($this->document, 11);
        $document = $text->mask($document, "###.###.###-##");
        return $document;
    }

    public function getPhoneNumber()
    {
        if (substr($this->phone_number, 0, 2) === "55") {
            return $this->phone_number;
        } else {
            return "55" . $this->phone_number;
        }
    }

    public function getMaskedPhoneNumber()
    {
        global $text;
        return $text->mask($this->getPhoneNumber(), "+## (##) #.####-####");
    }

    public function getInsertTime()
    {
        return $this->insert_time;
    }

    public function isActive()
    {
        return $this->is_active === "Y" ? true : false;
    }

    /**
     * @param mixed $id_account
     */
    public function setIdAccount($id_account)
    {
        $this->id_account = $id_account;
    }



}