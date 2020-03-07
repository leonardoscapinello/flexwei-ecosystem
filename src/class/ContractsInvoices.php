<?php

class ContractsInvoices
{

    private $id_contract_invoice;
    private $id_contract;
    private $id_customers;
    private $id_account;
    private $invoice_key;
    private $installment_number;
    private $amount;
    private $due_date;
    private $insert_time;
    private $is_active;

    public function load($id_contract_invoice)
    {
        global $database;
        global $text;
        if (not_empty($id_contract_invoice)) {
            $database->query("SELECT * FROM contracts_invoices ci LEFT JOIN contracts ct ON ct.id_contract = ci.id_contract WHERE (ci.id_contract_invoice = ? OR MD5(ci.id_contract_invoice) = ?)");
            $database->bind(1, $id_contract_invoice);
            $database->bind(2, $id_contract_invoice);
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

    public function massiveRegister($document_key)
    {
        global $database;
        global $contracts;
        global $contractsServices;
        try {
            if ($contracts->load($document_key)) {

                $id_contract = $contracts->getIdContract();
                $id_account = $contracts->getIdAccount();
                $installments = $contracts->getInstallments();
                $is_recurrent = $contracts->isRecurrent();


                $services = $contractsServices->getServiceListByDocumentKey($document_key);


                $total_amount = 0;
                for ($i = 0; $i < count($services); $i++) {
                    $amount = intval($services[$i]['total_amount']);
                    $total_amount = ($total_amount + $amount);
                }

                if ($total_amount > 0) {

                    for ($i = 1; $i <= $installments; $i++) {
                        $invoice_key = $this->createInvoiceKey();
                        $installment_number = $i;
                        if (!$this->installmentExists($id_contract, $installment_number)) {
                            $partial_amount = (intval($total_amount) / intval($installments));
                            $due_date = $this->createDueDate($id_contract, $installment_number);
                            $database->query("INSERT INTO contracts_invoices (id_contract, id_account, invoice_key, installment_number, amount, due_date) VALUES (?,?,?,?,?,?)");
                            $database->bind(1, $id_contract);
                            $database->bind(2, $id_account);
                            $database->bind(3, $invoice_key);
                            $database->bind(4, $installment_number);
                            $database->bind(5, $partial_amount);
                            $database->bind(6, $due_date);
                            $database->execute();
                        }
                    }
                }


            }

        } catch (Exception $exception) {
            error_log($exception);
        }
    }

    private function createDueDate($id_contract, $installment_number)
    {
        global $contracts;
        global $date;
        try {
            $contracts->loadById($id_contract);
            $date->setCustomDateFormat("Y-m-d");
            $payday = $contracts->getPayday();
            $now = date("Y-m-d H:i:s");
            $payday_fulldate = date("Y-m-$payday H:i:s");
            $difference = $date->getDaysOfDifference($now, $payday_fulldate);

            $months2add = ($installment_number - 1);

            if ($difference < 7) {
                // DIFFERENCE BIGGER THAN 7 DAYS, START INVOICE TO THIS MONTH
                $months2add = 1 + $installment_number;
            }

            $due_date = $date->sumDateMonths($payday_fulldate, $months2add);
            $due_date = $date->getNextBusinessDay($due_date);


            return $due_date;
        } catch (Exception $exception) {
            error_log($exception);
        }
    }

    private function createInvoiceKey()
    {
        global $token;
        global $text;
        return $text->uppercase($text->removeSpace("FW" . $token->tokenNumeric(8) . "-" . $token->tokenNumeric(2)));
    }

    private function installmentExists($id_contract, $installment_number)
    {
        global $database;
        try {
            $database->query("SELECT id_contract FROM contracts_invoices WHERE id_contract = ? AND installment_number = ?");
            $database->bind(1, $id_contract);
            $database->bind(2, $installment_number);
            $result = $database->resultset();
            if (count($result) > 0) return true;
        } catch (Exception $exception) {
            error_log($exception);
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getIdContractInvoice()
    {
        return $this->id_contract_invoice;
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
    public function getInvoiceKey()
    {
        return $this->invoice_key;
    }

    /**
     * @return mixed
     */
    public function getInstallmentNumber()
    {
        return $this->installment_number;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return mixed
     */
    public function getDueDate()
    {
        return $this->due_date;
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
    public function isActive()
    {
        return $this->is_active === "Y" ? true : false;
    }

    /**
     * @return mixed
     */
    public function getIdCustomers()
    {
        return $this->id_customers;
    }




}