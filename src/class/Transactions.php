<?php

class Transactions
{

    private $api_key = "ak_test_N2oaKtLC2OgCnmSvupDW4RhF5jxf7s";
    private $crypt_key = "ek_test_6UVSxBYbuOgDCp1CTJxbP2eXVICWku";

    private $id_transaction;
    private $id_contract_invoice;
    private $id_transaction_external;
    private $id_acquirer_external;
    private $id_document_external;
    private $id_customer_external;
    private $transaction_token;
    private $authorization_code;
    private $acquirer_name;
    private $invoice_amount;
    private $authorized_amount;
    private $paid_amount;
    private $refunded_amount;
    private $installments;
    private $cost;
    private $barcode;
    private $card_brand;
    private $card_holder_name;
    private $card_last_digits;
    private $status;
    private $payment_method;
    private $document_url;
    private $ip_address;
    private $response_data;
    private $postback_url;
    private $tid;
    private $nsu;
    private $insert_time;
    private $created_time;
    private $update_time;
    private $expire_date;
    private $is_paid;

    private $instructions = "Após o vencimento cobrar muta de 2% e juros de 1% ao mês";

    private function createBilletObject($id_contract_invoice)
    {
        global $pagarme;
        global $objects;
        global $date;
        global $numeric;
        global $contracts;
        global $contractsInvoices;

        try {
            if ($contractsInvoices->load($id_contract_invoice)) {
                $customer = new Accounts($contractsInvoices->getIdCustomers());
                $contracts->loadById($contractsInvoices->getIdContract());
                $postback = $this->createPostbackURL($id_contract_invoice);

                echo $postback;

                $object = [
                    "amount" => $numeric->removeEverythingNotNumber($contractsInvoices->getAmount()),
                    "payment_method" => "boleto",
                    "async" => false,
                    "boleto_instructions" => $this->instructions,
                    "boleto_expiration_date" => $date->str2date($contractsInvoices->getDueDate()),
                    "postback_url" => $postback,
                    "customer" => [
                        "external_id" => $objects->fastStr($customer->getIdAccount()),
                        "name" => $objects->fastStr($customer->getFullName()),
                        "boleto_fine" => $date->str2date($contractsInvoices->getDueDate()),
                        "boleto_fine[amount]" => $numeric->removeEverythingNotNumber((($contractsInvoices->getAmount() * 0.02) * 100)),
                        "boleto_interest[amount]" => $numeric->removeEverythingNotNumber((($contractsInvoices->getAmount() * $contracts->getTaxDailyDelay()) * 100)),
                        "type" => "individual",
                        "country" => "br",
                        "documents" => [
                            [
                                "type" => "cpf",
                                "number" => $objects->fastStr($customer->getDocument())
                            ]
                        ],
                        "phone_numbers" => [$objects->fastStr($customer->getPhoneNumber())],
                        "email" => $objects->fastStr($customer->getEmail())
                    ]
                ];
                return $object;
            }
        } catch (Exception $exception) {
            error_log($exception);
        }
        return null;
    }

    private function isTransactionAlreadyCreated($id_contract_invoce)
    {
        global $database;
        try {
            $database->query("SELECT id_transaction FROM transactions WHERE id_contract = ?");
            $database->bind(1, $id_contract_invoce);
            $result = $database->resultset();
            if (count($result) > 0) return true;
        } catch (Exception $exception) {
            error_log($exception);
        }
        return false;
    }

    private function store($id_contract_invoice, $transaction_result)
    {
        global $database;
        global $numeric;
        global $date;
        global $token;
        try {
            if ($transaction_result !== null && $transaction_result !== "") {

                $sess = $transaction_result;

                $response_data = json_encode($sess, true);

                $id_transaction_external = $sess->id;
                $id_acquirer_external = $sess->acquirer_id;
                $id_customer_external = $sess->customer->id;
                $transaction_token = $this->createTransactionToken();
                $postback_key = $token::v4();
                $invoice_amount = $numeric->placeDecimalDigits($sess->amount);
                $paid_amount = $numeric->placeDecimalDigits($sess->paid_amount);
                $authorized_amount = $numeric->placeDecimalDigits($sess->authorized_amount);
                $cost = $numeric->placeDecimalDigits($sess->cost);
                $document_url = $sess->boleto_url;
                $ip_address = $sess->ip;
                $barcode = $sess->boleto_barcode;
                $created_time = $date->str2date($sess->date_created);
                $expire_date = $date->str2date($sess->boleto_expiration_date);
                $update_time = $date->str2date($sess->date_updated);
                $tid = $sess->tid;
                $nsu = $sess->nsu;
                $status = $sess->status;
                $acquirer_name = $sess->acquirer_name;
                $authorization_code = $sess->authorization_code;
                $refunded_amount = $sess->refunded_amount;
                $installments = $sess->installments;
                $card_holder_name = $sess->card_holder_name;
                $card_last_digits = $sess->card_last_digits;
                $card_brand = $sess->card_brand;
                $postback_url = $sess->postback_url;
                $payment_method = $sess->payment_method;

                $database->query("INSERT INTO transactions (id_contract_invoice, id_transaction_external, id_acquirer_external, id_customer_external, transaction_token, authorization_code, acquirer_name, invoice_amount, authorized_amount, paid_amount, refunded_amount, installments, cost, barcode, card_brand, card_holder_name, card_last_digits, status, payment_method, document_url, ip_address, response_data, postback_url, tid, nsu, created_time, update_time, expire_date, is_paid) VALUES( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $database->bind(1, $id_contract_invoice);
                $database->bind(2, $id_transaction_external);
                $database->bind(3, $id_acquirer_external);
                $database->bind(4, $id_customer_external);
                $database->bind(5, $transaction_token);
                $database->bind(6, $authorization_code);
                $database->bind(7, $acquirer_name);
                $database->bind(8, $invoice_amount);
                $database->bind(9, $authorized_amount);
                $database->bind(10, $paid_amount);
                $database->bind(11, $refunded_amount);
                $database->bind(12, $installments);
                $database->bind(13, $cost);
                $database->bind(14, $barcode);
                $database->bind(15, $card_brand);
                $database->bind(16, $card_holder_name);
                $database->bind(17, $card_last_digits);
                $database->bind(18, $status);
                $database->bind(19, $payment_method);
                $database->bind(20, $document_url);
                $database->bind(21, $ip_address);
                $database->bind(22, $response_data);
                $database->bind(23, $postback_url);
                $database->bind(24, $tid);
                $database->bind(25, $nsu);
                $database->bind(26, $created_time);
                $database->bind(27, $update_time);
                $database->bind(28, $expire_date);
                $database->bind(29, "N");
                $database->execute();


            }
        } catch (Exception $exception) {

        }
    }

    private function createTransactionToken()
    {
        global $token;
        global $text;
        return $text->uppercase($text->removeSpace("FWTR" . $token->tokenNumeric(6) . "-" . $token->tokenNumeric(2)));
    }


    public function registerBillet($id_contract_invoice)
    {
        global $pagarme;
        try {
            $object = $this->createBilletObject($id_contract_invoice);
            if ($object !== null) {
                $result = $pagarme->transactions()->create($object);
                $this->store($id_contract_invoice, $result);
            }
        } catch (Exception $exception) {
            error_log($exception);
        }
    }

    public function createPostbackURL($id_contract_invoice)
    {
        global $database;
        global $properties;
        try {
            $database->query("SELECT invoice_key FROM contracts_invoices WHERE id_contract_invoice = ? ORDER BY id_contract_invoice DESC LIMIT 1");
            $database->bind(1, $id_contract_invoice);
            $result = $database->resultset();
            if (count($result) > 0) {
                return $properties->getSiteURL() . "p/update/" . md5(base64_encode($result[0]['invoice_key'])) . "/" . md5($id_contract_invoice);
            }

        } catch (Exception $exception) {

        }
    }

}