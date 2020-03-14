<?php

class TransactionsHistory
{

    private $api_key = "ak_test_N2oaKtLC2OgCnmSvupDW4RhF5jxf7s";
    private $crypt_key = "ek_test_6UVSxBYbuOgDCp1CTJxbP2eXVICWku";


    public function execute($id_transaction)
    {
        global $database;
        try {
            if (not_empty($id_transaction)) {
                $database->query("INSERT INTO transactions_history SELECT id_transaction, id_contract_invoice, id_transaction_external, id_acquirer_external, id_customer_external, transaction_token, authorization_code, acquirer_name, invoice_amount, authorized_amount, paid_amount, refunded_amount, installments, cost, barcode, card_brand, card_holder_name, card_last_digits, status, payment_method, document_url, ip_address, response_data, postback_url, tid, nsu, insert_time, created_time, update_time, expire_date, version, is_paid FROM transactions WHERE id_transaction = ?");
                $database->bind(1, $id_transaction);
                $database->execute();

                $database->query("UPDATE transactions SET version = (version+1), update_time = CURRENT_TIMESTAMP WHERE id_transaction = ?");
                $database->bind(1, $id_transaction);
                $database->execute();
                return true;
            }
        } catch (Exception $exception) {
            error_log($exception);
        }
        return false;
    }


}