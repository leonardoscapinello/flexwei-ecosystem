<?php

class ContractsServices
{

    private $id_contract_service;
    private $id_contract;
    private $id_account;
    private $id_service;
    private $total_amount;
    private $unitary_price;
    private $hired_hours;
    private $service_title;
    private $service_description;
    private $is_active;
    private $insert_time;

    public function __construct($id_contract_service = 0)
    {
        global $database;
        global $text;
        if (not_empty($id_contract_service)) {
            $database->query("SELECT * FROM contracts_services WHERE (id_contract_service = ? OR MD5(id_contract_service) = ?)");
            $database->bind(1, $id_contract_service);
            $database->bind(2, $id_contract_service);
            $result = $database->resultsetObject();
            if ($result && count(get_object_vars($result)) > 0) {
                foreach ($result as $key => $value) {
                    $this->$key = $text->utf8($value);
                }
            }
        }
    }

    /**
     * @return mixed
     */
    public function getIdContractService()
    {
        return $this->id_contract_service;
    }

    /**
     * @return mixed
     */
    public function getIdContract()
    {
        return $this->id_contract;
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
    public function getIdService()
    {
        return $this->id_service;
    }

    /**
     * @return mixed
     */
    public function getTotalAmount()
    {
        return $this->total_amount;
    }

    /**
     * @return mixed
     */
    public function getUnitaryPrice()
    {
        return $this->unitary_price;
    }

    /**
     * @return mixed
     */
    public function getHiredHours()
    {
        return $this->hired_hours;
    }

    /**
     * @return mixed
     */
    public function getServiceTitle()
    {
        return $this->service_title;
    }

    /**
     * @return mixed
     */
    public function getServiceDescription()
    {
        return $this->service_description;
    }

    /**
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->is_active;
    }

    /**
     * @return mixed
     */
    public function getInsertTime()
    {
        return $this->insert_time;
    }

    public function getList($document_key)
    {
        global $database;
        $database->query("SELECT sv.id_service, sv.service_name, cs.total_amount, cs.hired_hours, cs.unitary_price, cs.service_title, cs.service_description, cs.is_active, cs.insert_time FROM contracts_services cs LEFT JOIN services sv ON sv.id_service = cs.id_service WHERE id_contract = (SELECT id_contract FROM contracts WHERE document_key = ?) AND is_active = 'Y'");
        $database->bind(1, $document_key);
        $resultset = $database->resultset();
        if (count($resultset) > 0) {
            return $resultset;
        }
        return array();
    }

    public function getServiceListByDocumentKey($document_key)
    {
        global $database;
        global $text;
        if (not_empty($document_key)) {
            $database->query("SELECT * FROM contracts_services cs LEFT JOIN services sv ON sv.id_service = cs.id_service WHERE cs.id_contract IN (SELECT id_contract FROM contracts WHERE document_key = ?)  AND is_active = 'Y'");
            $database->bind(1, $document_key);
            $result = $database->resultset();
            if (count($result) > 0) {
                return $result;
            }
        }
        return array();
    }


}