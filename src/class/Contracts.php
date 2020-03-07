<?php

class Contracts
{

    private $id_contract;
    private $id_account;
    private $id_customer;
    private $document_key;
    private $payday;
    private $installments;
    private $tax_daily_delay;
    private $version;
    private $insert_time;
    private $update_time;
    private $expire_date;
    private $cancel_date_allowed;
    private $is_active;
    private $is_recurrent;
    private $created_invoices;


    public function getAuthenticatedUserContracts()
    {
        global $account;
        global $database;
        try {
            $id_customer = $account->getIdAccount();
            $database->query("SELECT ct.document_key, ct.payday, ct.version, ct.insert_time, ct.expire_date, ct.update_time, ct.is_active, ac.first_name, ac.last_name FROM contracts ct LEFT JOIN accounts ac ON ac.id_account = ct.id_account WHERE ct.id_customer = ? ORDER BY is_active ASC, insert_time");
            $database->bind(1, $id_customer);
            return ($database->resultset());
        } catch (Exception $exception) {
            error_log($exception);
        }
        return array();
    }

    public function load($document_key)
    {
        global $database;
        global $text;
        if (not_empty($document_key)) {
            $database->query("SELECT * FROM contracts WHERE document_key = ?");
            $database->bind(1, $document_key);
            $result = $database->resultsetObject();
            if ($result && count(get_object_vars($result)) > 0) {
                foreach ($result as $key => $value) {
                    $this->$key = $text->utf8($value);
                }
                return true;
            }
        }
        return false;
    }

    public function loadById($id_contract)
    {
        global $database;
        global $text;
        if (not_empty($id_contract)) {
            $database->query("SELECT * FROM contracts WHERE id_contract = ?");
            $database->bind(1, $id_contract);
            $result = $database->resultsetObject();
            if ($result && count(get_object_vars($result)) > 0) {
                foreach ($result as $key => $value) {
                    $this->$key = $text->utf8($value);
                }
            }
        }
    }

    public function getNextClosureDate()
    {
        global $date;
        $payday = $this->getPayday();
        $date_closure = date("Y-m-") . $payday;
        return $date->subtractDateBusinessDay($date_closure, 5);
    }

    public function getNextPayDay()
    {
        global $date;
        $payday = $this->getPayday();
        $date_closure = date("Y-m-") . $payday;
        return $date->getNextBusinessDay($date_closure);
    }

    /**
     * @return mixed
     */
    public function getIdContract()
    {
        return $this->id_contract;
    }

    /**
     * @param mixed $id_contract
     */
    public function setIdContract($id_contract)
    {
        $this->id_contract = $id_contract;
    }

    /**
     * @return mixed
     */
    public function getIdAccount()
    {
        return $this->id_account;
    }

    /**
     * @param mixed $id_account
     */
    public function setIdAccount($id_account)
    {
        $this->id_account = $id_account;
    }

    /**
     * @return mixed
     */
    public function getIdCustomer()
    {
        return $this->id_customer;
    }

    /**
     * @param mixed $id_customer
     */
    public function setIdCustomer($id_customer)
    {
        $this->id_customer = $id_customer;
    }

    /**
     * @return mixed
     */
    public function getDocumentKey()
    {
        return $this->document_key;
    }

    /**
     * @param mixed $document_key
     */
    public function setDocumentKey($document_key)
    {
        $this->document_key = $document_key;
    }

    /**
     * @return mixed
     */
    public function getPayday()
    {
        return $this->payday;
    }

    /**
     * @param mixed $payday
     */
    public function setPayday($payday)
    {
        $this->payday = $payday;
    }

    /**
     * @return mixed
     */
    public function getTaxDailyDelay()
    {
        return $this->tax_daily_delay;
    }

    /**
     * @param mixed $tax_daily_delay
     */
    public function setTaxDailyDelay($tax_daily_delay)
    {
        $this->tax_daily_delay = $tax_daily_delay;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param mixed $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return mixed
     */
    public function getInsertTime()
    {
        return $this->insert_time;
    }

    /**
     * @param mixed $insert_time
     */
    public function setInsertTime($insert_time)
    {
        $this->insert_time = $insert_time;
    }

    /**
     * @return mixed
     */
    public function getUpdateTime()
    {
        return $this->update_time;
    }

    /**
     * @param mixed $update_time
     */
    public function setUpdateTime($update_time)
    {
        $this->update_time = $update_time;
    }

    /**
     * @return mixed
     */
    public function getExpireDate()
    {
        return $this->expire_date;
    }

    /**
     * @param mixed $expire_date
     */
    public function setExpireDate($expire_date)
    {
        $this->expire_date = $expire_date;
    }

    /**
     * @return mixed
     */
    public function getCancelDateAllowed()
    {
        return $this->cancel_date_allowed;
    }

    /**
     * @param mixed $cancel_date_allowed
     */
    public function setCancelDateAllowed($cancel_date_allowed)
    {
        $this->cancel_date_allowed = $cancel_date_allowed;
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
    public function getInstallments()
    {
        return intval($this->installments) < 1 ? 1 : intval($this->installments);
    }

    /**
     * @return mixed
     */
    public function isRecurrent()
    {
        return $this->is_recurrent === "Y" ? true : false;;
    }

    /**
     * @return mixed
     */
    public function isCreatedInvoices()
    {
        return $this->created_invoices === "Y" ? true : false;;
    }


}