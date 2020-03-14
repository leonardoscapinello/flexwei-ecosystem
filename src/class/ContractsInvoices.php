<?php

class ContractsInvoices
{

    private $id_contract_invoice;
    private $id_contract;
    private $id_account;
    private $id_customer;
    private $id_transaction_paid;
    private $url_token;
    private $invoice_key;
    private $installment_number;
    private $amount;
    private $status;
    private $due_date;
    private $paid_date;
    private $insert_time;
    private $is_active;
    private $is_rendered;

    public function __construct($id_contract_invoice_or_key = 0)
    {
        global $database;
        global $text;
        global $numeric;
        try {
            if (not_empty($id_contract_invoice_or_key)) {
                $database->query("SELECT * FROM contracts_invoices ci LEFT JOIN contracts ct ON ct.id_contract = ci.id_contract WHERE (ci.id_contract_invoice = :invoice) OR ci.invoice_key = :invoice OR url_token = :invoice");
                $database->bind(":invoice", $id_contract_invoice_or_key);
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
            $database->query("SELECT ci.id_contract_invoice, ci.id_contract, ci.id_account, ci.invoice_key, ci.installment_number, ci.amount, ci.due_date, ci.insert_time, ci.is_active, ct.id_customer, ct.installments, ct.payday, ct.document_key, ci.id_transaction_paid, ci.paid_date FROM contracts_invoices ci LEFT JOIN contracts ct ON ct.id_contract = ci.id_contract WHERE url_token = ?");
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

    private function getAllPastInvoices($id_contract = 0)
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

    public function getStatusProperties($id_contract_invoice = 0)
    {
        global $database;
        global $text;
        // array(caption, css class);
        $res = array("Não Processado", "");
        try {
            if (!$id_contract_invoice || intval($id_contract_invoice) === 0) $id_contract_invoice = $this->getIdContractInvoice();
            $database->query("SELECT value_caption, value_class FROM cust_values WHERE list_name = 'finance_status' AND value_key = (SELECT status FROM contracts_invoices WHERE id_contract_invoice = ?)");
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

    public function getPastDebitsAmount()
    {
        global $database;
        try {
            $id_contract_invoice = $this->getIdContractInvoice();
            $database->query("SELECT IFNULL(SUM(ci.amount - IFNULL(tr.paid_amount, 0)),0) AS debits FROM contracts_invoices ci LEFT JOIN (SELECT id_contract_invoice, IFNULL(SUM(paid_amount), 0) paid_amount FROM transactions) tr ON tr.id_contract_invoice = ci.id_contract_invoice WHERE ci.id_contract = (SELECT id_contract FROM contracts_invoices WHERE id_contract_invoice = ?) AND ci.due_date < (SELECT due_date FROM contracts_invoices WHERE id_contract_invoice = ?) AND ci.is_active = 'Y' AND (ci.amount - IFNULL(tr.paid_amount, 0)) > 0");
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

    public function getPastInvoices()
    {
        global $database;
        try {
            $id_contract_invoice = $this->getIdContractInvoice();
            $database->query("SELECT ci.id_contract_invoice FROM contracts_invoices ci LEFT JOIN (SELECT id_contract_invoice, IFNULL(SUM(paid_amount), 0) paid_amount FROM transactions) tr ON tr.id_contract_invoice = ci.id_contract_invoice WHERE ci.id_contract = (SELECT id_contract FROM contracts_invoices WHERE id_contract_invoice = ?) AND ci.due_date < (SELECT due_date FROM contracts_invoices WHERE id_contract_invoice = ?) AND ci.is_active = 'Y' AND (ci.amount - IFNULL(tr.paid_amount, 0)) > 0");
            $database->bind(1, $id_contract_invoice);
            $database->bind(2, $id_contract_invoice);
            $result = $database->resultset();
            if (count($result) > 0) {
                return $result;
            }
        } catch (Exception $exception) {
            error_log($exception);
        }
        return array();
    }


    public function getTaxAmount($custom_tax = 0.06)
    {
        return $this->getPastDebitsAmount() * $custom_tax;
    }

    public function getAmount2Pay()
    {
        global $transactions;
        $invoice_key = $this->getInvoiceKey();
        $paid = $transactions->getPaidAmountForInvoice($invoice_key);
        $past = $this->getPastDebitsAmount();
        $tax = $this->getTaxAmount();
        $amount = $this->getAmount();
        return (($past + $tax) + $amount) - $paid;
    }

    public function getAmountSumPast()
    {
        global $transactions;
        $paid = $transactions->getPaidAmountForInvoice($this->getInvoiceKey());
        $past = $this->getPastDebitsAmount();
        $tax = $this->getTaxAmount();
        $amount = $this->getAmount();
        return (($past + $tax) + $amount);
    }

    public function getSmallInsertTime()
    {
        global $date;
        $month_name = $date->getMonthNameFromDate($this->insert_time);
        $month_name = substr($month_name, 0, 3);
        return date("d", strtotime($this->insert_time)) . " " . $month_name . " " . date("Y", strtotime($this->insert_time));
    }


    public function getSmallDueDate()
    {
        global $date;
        $month_name = $date->getMonthNameFromDate($this->due_date);
        $month_name = substr($month_name, 0, 3);
        return $month_name . " " . date("Y", strtotime($this->due_date));
    }

    public function isPaidInFutureInvoice()
    {
        global $database;
        try {
            $database->query("SELECT ci.id_contract_invoice, IFNULL(tr.tr_qt, 0) tr_qt FROM contracts_invoices ci LEFT JOIN (SELECT IFNULL(COUNT(id_contract_invoice), 0) AS tr_qt, id_contract_invoice  FROM transactions GROUP BY id_contract_invoice) tr ON tr.id_contract_invoice = ci.id_contract_invoice WHERE ci.invoice_key = ? AND ci.status = 3");
            $database->bind(1, $this->getInvoiceKey());
            $result = $database->resultset();
            if (count($result) > 0) {
                // SE HOUVE TRANSAÇÃO PARA ESSA FATURA, ENTÃO NÃO HOUVE PAGAMENTO EM OUTRA FATURA.
                if ($result[0]['tr_qt'] > 0) return false;
            }
        } catch (Exception $exception) {
            error_log($exception);
        }
        return true;
    }

    public function isPaid()
    {
        if ($this->getAmount2Pay() < 1) return true;
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
    public function getUrlToken()
    {
        return $this->url_token;
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
    public function getStatus()
    {
        return $this->status;
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
    public function getIsRendered()
    {
        return $this->is_rendered;
    }

    /**
     * @return mixed
     */
    public function getIdCustomer()
    {
        return $this->id_customer;
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

    public function invoicePDFExists($invoice_key)
    {
        $file = DIRNAME . "../../public/documents/" . $invoice_key . ".pdf";
        return file_exists($file);
    }

    /**
     * @return mixed
     */
    public function getIdTransactionPaid()
    {
        return $this->id_transaction_paid;
    }

    /**
     * @return mixed
     */
    public function getPaidDate()
    {
        return $this->paid_date;
    }



}
