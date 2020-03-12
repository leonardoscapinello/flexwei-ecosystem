<?php

class ContractsInvoices
{

    private $id_contract_invoice;
    private $id_contract;
    private $id_customer;
    private $id_account;
    private $invoice_key;
    private $installment_number;
    private $amount;
    private $due_date;
    private $insert_time;
    private $is_active;
    private $status;

    public function __construct($id_contract_invoice = 0)
    {
        $this->load($id_contract_invoice);
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

    private function createUniqueKey()
    {
        global $token;
        global $text;
        global $numeric;
        $day = date("d");
        $month = date("m");
        $year = date("Y");
        $hour = date("H");
        $minute = date("i");
        $second = date("s");
        $key = $token->tokenAlphanumeric(40);
        $key .= $day . $month;
        $key .= $token->tokenAlphanumeric(40);
        $key .= $year;
        $key .= $token->tokenAlphanumeric(40);
        $key .= $hour;
        $key .= $token->tokenAlphanumeric(40);
        $key .= $minute;
        $key .= $token->tokenAlphanumeric(40);
        $key .= $second;
        $key .= $token->tokenAlphanumeric(41);
        return $text->uppercase($key);
    }

    private function installmentExists($id_contract, $installment_number)
    {
        global $database;
        try {
            $database->query("SELECT id_contract FROM contracts_invoices WHERE id_contract = ? AND installment_number = ? AND is_active = 'Y'");
            $database->bind(1, $id_contract);
            $database->bind(2, $installment_number);
            $result = $database->resultset();
            if (count($result) > 0) return true;
        } catch (Exception $exception) {
            error_log($exception);
        }
        return false;
    }


    public function invoiceDocumentAlreadyRendered($invoice_key)
    {
        $file = DIRNAME . "../public/documents/" . $invoice_key . ".pdf";
        return file_exists($file);
    }


    public function load($id_contract_invoice)
    {
        global $database;
        global $text;
        global $numeric;
        try {
            if (not_empty($id_contract_invoice)) {
                $database->query("SELECT * FROM contracts_invoices ci LEFT JOIN contracts ct ON ct.id_contract = ci.id_contract WHERE (ci.id_contract_invoice = :invoice OR MD5(ci.id_contract_invoice) = :invoice) OR ci.invoice_key = :invoice");
                $database->bind(":invoice", $id_contract_invoice);
                $result = $database->resultsetObject();
                if ($result && count(get_object_vars($result)) > 0) {
                    foreach ($result as $key => $value) {
                        $this->$key = $text->utf8($value);
                    }
                    return true;
                }
            }
        } catch (Exception $exception) {
            error_log($exception);
        }
        return false;
    }

    public function loadByURLToken($url_token)
    {
        global $database;
        global $text;
        if (not_empty($url_token)) {
            $database->query("SELECT ci.id_contract_invoice, ci.id_contract, ci.id_account, ci.invoice_key, ci.installment_number, ci.amount, ci.due_date, ci.insert_time, ci.is_active, ct.id_customer, ct.installments, ct.payday, ct.document_key FROM contracts_invoices ci LEFT JOIN contracts ct ON ct.id_contract = ci.id_contract WHERE url_token = ?");
            $database->bind(1, $url_token);
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

    public function getInvoicePastDebits($id_contract_invoice = 0)
    {
        global $database;
        try {
            if (!$id_contract_invoice || intval($id_contract_invoice) === 0) $id_contract_invoice = $this->getIdContractInvoice();
            $database->query("SELECT ci.id_contract_invoice, ci.invoice_key, ci.due_date, ci.amount, IFNULL(tr.paid_amount, 0) AS invoice_paid, (ci.amount - IFNULL(tr.paid_amount, 0)) AS debits FROM contracts_invoices ci LEFT JOIN (SELECT id_contract_invoice, IFNULL(SUM(paid_amount), 0) paid_amount FROM transactions) tr ON tr.id_contract_invoice = ci.id_contract_invoice WHERE ci.id_contract = (SELECT id_contract FROM contracts_invoices WHERE id_contract_invoice = ?) AND ci.due_date < (SELECT due_date FROM contracts_invoices WHERE id_contract_invoice = ?) AND ci.is_active = 'Y' AND (ci.amount - IFNULL(tr.paid_amount, 0)) > 0");
            $database->bind(1, $id_contract_invoice);
            $database->bind(2, $id_contract_invoice);
            $result = $database->resultset();
            if (count($result) > 0) {
                return $result;
            }
        } catch (Exception $exception) {

        }
    }

    public function getSumTotalPastDebits($id_contract_invoice = 0)
    {
        global $database;
        try {
            if (!$id_contract_invoice || intval($id_contract_invoice) === 0) $id_contract_invoice = $this->getIdContractInvoice();
            $database->query("SELECT SUM(ci.amount - IFNULL(tr.paid_amount, 0)) AS debits FROM contracts_invoices ci LEFT JOIN (SELECT id_contract_invoice, IFNULL(SUM(paid_amount), 0) paid_amount FROM transactions) tr ON tr.id_contract_invoice = ci.id_contract_invoice WHERE ci.id_contract = (SELECT id_contract FROM contracts_invoices WHERE id_contract_invoice = ?) AND ci.due_date < (SELECT due_date FROM contracts_invoices WHERE id_contract_invoice = ?) AND ci.is_active = 'Y' AND (ci.amount - IFNULL(tr.paid_amount, 0)) > 0");
            $database->bind(1, $id_contract_invoice);
            $database->bind(2, $id_contract_invoice);
            $result = $database->resultset();
            if (count($result) > 0) {
                return $result[0]['debits'];
            }
        } catch (Exception $exception) {
            error_log($exception);
        }
        return 0;
    }


    public function getAllContractsHasNotCreatedAllInvoicesAndDo()
    {
        global $database;
        global $contracts;
        global $contractsServices;
        try {

            $database->query("SELECT ct.installments, IFNULL(ci.rendered_invoices, 0) rendered_invoices, ct.document_key FROM contracts ct LEFT JOIN (SELECT COUNT(id_contract_invoice) rendered_invoices, id_contract FROM contracts_invoices  GROUP BY id_contract) ci ON ci.id_contract = ct.id_contract WHERE created_invoices = 'N'");
            $resultSet = $database->resultset();
            if (count($resultSet) > 0) {
                for ($i = 0; $i < count($resultSet); $i++) {
                    $document_key = $resultSet[$i]['document_key'];
                    $installments = $resultSet[$i]['installments'];
                    $rendered_invoices = $resultSet[$i]['rendered_invoices'];
                    if (intval($rendered_invoices) >= intval($installments)) {
                        $this->setContractInvoicesCreated($document_key, true);
                    } else {
                        $this->massiveRegister($document_key);
                    }
                }
            }

        } catch (Exception $exception) {
            echo $exception;
            error_log($exception);
        }
    }


    private function massiveRegister($document_key)
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
                        $url_token = $this->createUniqueKey();
                        $installment_number = $i;
                        if (!$this->installmentExists($id_contract, $installment_number)) {
                            $partial_amount = (intval($total_amount) / intval($installments));
                            $due_date = $this->createDueDate($id_contract, $installment_number);
                            $database->query("INSERT INTO contracts_invoices (id_contract, id_account, invoice_key, installment_number, amount, due_date, url_token) VALUES (?,?,?,?,?,?,?)");
                            $database->bind(1, $id_contract);
                            $database->bind(2, $id_account);
                            $database->bind(3, $invoice_key);
                            $database->bind(4, $installment_number);
                            $database->bind(5, $partial_amount);
                            $database->bind(6, $due_date);
                            $database->bind(7, $url_token);
                            $database->execute();
                        }
                    }
                }


            }

        } catch (Exception $exception) {
            error_log($exception);
        }
    }

    private function setContractInvoicesCreated($document_key, $boolean = true)
    {
        global $database;
        try {
            $database->query("UPDATE contracts SET created_invoices = ? WHERE document_key = ?");
            $resultset = $database->resultset();
            if (count($resultset) > 0) {
                for ($i = 0; $i < count($resultset); $i++) {
                    $document_key = $resultset[$i]['document_key'];
                    $installments = $resultset[$i]['installments'];
                    $rendered_invoices = $resultset[$i]['rendered_invoices'];
                    if (intval($installments) >= intval($rendered_invoices)) {
                        $this->setContractInvoicesCreated($document_key, true);
                    } else {
                        $this->massiveRegister($document_key);
                    }
                }
            }
        } catch (Exception $exception) {
            error_log($exception);
        }
    }

    public function getThisMonthInvoice($id_contract = 0)
    {
        global $database;
        global $numeric;
        try {
            if ($numeric->isIdentity($id_contract)) {
                $database->query("SELECT * FROM contracts_invoices WHERE id_contract = ? AND (due_date >= NOW() + INTERVAL 2 DAY AND due_date < NOW() + INTERVAL 15 DAY) AND is_active = 'Y' ORDER BY id_contract_invoice DESC LIMIT 1");
                $database->bind(1, $id_contract);
                $result = $database->resultset();
                if (count($result) > 0) {
                    return $result[0];
                }
            }
        } catch (Exception $exception) {
            error_log($exception);
        }
        return array();
    }

    public function getAllPastInvoices($id_contract = 0)
    {
        global $database;
        global $numeric;
        try {
            if ($numeric->isIdentity($id_contract)) {
                $database->query("SELECT * FROM contracts_invoices WHERE id_contract = ? AND is_active = 'Y' AND id_contract_invoice != (SELECT id_contract_invoice FROM contracts_invoices WHERE id_contract = ? AND (due_date >= NOW() + INTERVAL 2 DAY AND due_date < NOW() + INTERVAL 15 DAY) ORDER BY id_contract_invoice DESC LIMIT 1) ORDER BY due_date ASC");
                $database->bind(1, $id_contract);
                $database->bind(2, $id_contract);
                $result = $database->resultset();
                if (count($result) > 0) {
                    return $result;
                }
            }
        } catch (Exception $exception) {
            error_log($exception);
        }
        return array();
    }


    public function getAllInvoices($id_contract = 0)
    {
        global $database;
        global $numeric;
        try {
            if ($numeric->isIdentity($id_contract)) {
                $database->query("SELECT * FROM contracts_invoices WHERE id_contract = ? AND is_active = 'Y' ORDER BY due_date ASC");
                $database->bind(1, $id_contract);
                $result = $database->resultset();
                if (count($result) > 0) {
                    return $result;
                }
            }
        } catch (Exception $exception) {
            error_log($exception);
        }
        return array();
    }

    public function render($url_token, $invoice_key)
    {
        global $database;
        global $properties;
        try {
            $url = $properties->getSiteURL() . "e/i/render/" . $url_token . "?filename=" . $invoice_key;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
            curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $output = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($httpcode === 200) {
                $database->query("UPDATE contracts_invoices SET is_rendered = 'Y', status = 2 WHERE is_rendered = 'N' AND invoice_key = ?");
                $database->bind(1, $invoice_key);
                $database->execute();
            }
        } catch (Exception $exception) {
            error_log($exception);
        }
    }

    public function getStatusProperties($id_contract_invoice = 0)
    {
        global $database;
        global $text;
        // array(caption, css class);
        $res = array("Não Processado", "");
        try {
            if (!$id_contract_invoice || intval($id_contract_invoice) === 0) $id_contract_invoice = $this->getIdContractInvoice();
            $database->query("SELECT value_caption, value_class FROM cust_values WHERE list_name = 'status' AND value_key = (SELECT status FROM contracts_invoices WHERE id_contract_invoice = ?)");
            $database->bind(1, $id_contract_invoice);
            $result = $database->resultset();
            if (count($result) > 0) {
                $res = array($text->utf8($result[0]['value_caption']), $result[0]['value_class']);
            }
        } catch (Exception $exception) {
            error_log($exception);
        }
        return $res;
    }

    public function isPaid()
    {
        global $numeric;
        global $transactions;
        try {
            $id_contract_invoice = $this->getIdContractInvoice();
            if ($numeric->isIdentity($id_contract_invoice)) {
                $paid_amount = $transactions->getTotalPaid($id_contract_invoice);
                $total2pay = ($this->getSumTotalPastDebits() + $this->getAmount()) - $paid_amount;
                if (intval($total2pay) === 0) return true;
            }
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

    public function getTotalToPay()
    {
        global $transactions;
        $paid_amount = $transactions->getTotalPaid($this->getIdContractInvoice());
        $total2pay = ($this->getSumTotalPastDebits() + $this->getAmount()) - $paid_amount;
        return $total2pay * 100; // convert to cents
    }

    /**
     * @return mixed
     */
    public function getDueDate()
    {
        return $this->due_date;
    }

    public function getSmallDueDate()
    {
        global $date;
        $month_name = $date->getMonthNameFromDate($this->due_date);
        $month_name = substr($month_name, 0, 3);
        return $month_name . " " . date("Y", strtotime($this->due_date));
    }

    /**
     * @return mixed
     */
    public function getInsertTime()
    {
        return $this->insert_time;
    }


    public function getSmallInsertTime()
    {
        global $date;
        $month_name = $date->getMonthNameFromDate($this->insert_time);
        $month_name = substr($month_name, 0, 3);
        return date("d", strtotime($this->insert_time)) . " " . $month_name . " " . date("Y", strtotime($this->insert_time));
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
    public function getIdCustomer()
    {
        return $this->id_customer;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }


}