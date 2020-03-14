<?php

class ContractsInvoicesUpdate
{

    public function getAllActiveInvoices()
    {
        global $database;
        try {
            $database->query("SELECT id_contract_invoice, amount, due_date, status  FROM contracts_invoices WHERE status IN (1, 2)");
            $result = $database->resultset();
            if (count($result) > 0) {
                for ($i = 0; $i < count($result); $i++) {

                    $id_contract_invoice = $result[$i]['id_contract_invoice'];
                    $status = $result[$i]['status'];
                    $due = $result[$i]['due_date'];
                    $days_diff = $this->getDatesDiff($due);

                    if ($days_diff < 0) {
                        // DEFINE FATURA COMO VENCIDA
                        $this->setStatus($id_contract_invoice, 7);
                    }

                }
            }
        } catch (Exception $exception) {
            echo $exception;
        }

    }

    private function getDatesDiff($date)
    {
        $now = time(); // or your date as well
        $your_date = strtotime($date);
        $datediff = $your_date - $now;
        return round($datediff / (60 * 60 * 24));
    }

    public function setStatus($id_contract_invoice, $status)
    {
        global $database;
        try {
            $database->query("UPDATE contracts_invoices SET status = ? WHERE id_contract_invoice = ?");
            $database->bind(1, $status);
            $database->bind(2, $id_contract_invoice);
            $database->execute();
        } catch (Exception $exception) {
            echo $exception;
        }
    }

    public function setInactive($id_contract_invoice)
    {
        global $database;
        try {
            $database->query("UPDATE contracts_invoices SET is_active = 'N' WHERE id_contract_invoice = ?");
            $database->bind(1, $id_contract_invoice);
            $database->execute();
        } catch (Exception $exception) {
            echo $exception;
        }
    }

}