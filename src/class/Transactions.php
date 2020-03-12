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


    /* REGISTER ON PAGAR.ME */


    public function register($id_contract_invoice, $credit_card = false)
    {
        global $pagarme;
        try {
            if ($credit_card) {
                $object = $this->createCreditCardObject($id_contract_invoice, $credit_card);
            } else {
                $object = $this->createBilletObject($id_contract_invoice);
            }
            if ($object !== null) {
                $result = $pagarme->transactions()->create($object);
                $this->store($id_contract_invoice, $result);
                return true;
            }
        } catch (Exception $exception) {
            echo $exception;
            error_log($exception);
        }
        return false;
    }


    /* ============================= */


    public function getTotalPaid($id_contract_invoice)
    {
        global $database;
        if (not_empty($id_contract_invoice)) {
            $database->query("SELECT SUM(paid_amount) AS paid_amount FROM transactions WHERE id_contract_invoice = ? AND status = 'paid'");
            $database->bind(1, $id_contract_invoice);
            $result = $database->resultset();
            if ($result && count($result) > 0) {
                return $result[0]['paid_amount'];
            }
        }
        return array();
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
        global $contractsInvoices;
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


                $this->updateStatusForPaid($id_contract_invoice);

            }
        } catch (Exception $exception) {
            echo($exception);
            error_log($exception);
        }
    }


    public function updateStatusForPaid($id_contract_invoice)
    {
        global $numeric;
        global $database;
        try {
            if ($numeric->isIdentity($id_contract_invoice)) {
                $loadedInvoice = new ContractsInvoices($id_contract_invoice);
                $paid_amount = $this->getTotalPaid($id_contract_invoice);
                $total2pay = ($loadedInvoice->getSumTotalPastDebits() + $loadedInvoice->getAmount()) - $paid_amount;
                if (intval($total2pay) === 0) {
                    $status = 3;
                } elseif ($total2pay > 0 && $paid_amount > 0) {
                    $status = 4;
                } else {
                    $status = 2;
                }

                $database->query("UPDATE contracts_invoices SET status = ? WHERE id_contract_invoice = ?");
                $database->bind(1, $status);
                $database->bind(2, $id_contract_invoice);
                $database->execute();

            }
        } catch (Exception $exception) {
            echo $exception;
            error_log($exception);
        }
        return false;
    }


    private function createTransactionToken()
    {
        global $token;
        global $text;
        return $text->uppercase($text->removeSpace("FWTR" . $token->tokenNumeric(6) . "-" . $token->tokenNumeric(2)));
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


    public function getPaidAmountForInvoice($invoice_key)
    {
        global $database;
        try {
            $database->query("SELECT IFNULL(SUM(paid_amount), 0) AS paid_amount FROM transactions WHERE id_contract_invoice = (SELECT id_contract_invoice FROM contracts_invoices WHERE invoice_key = ?)");
            $database->bind(1, $invoice_key);
            $result = $database->resultset();
            if (count($result) > 0) {
                return $result[0]['paid_amount'];
            }
        } catch (Exception $exception) {
            error_log($exception);
        }
        return 0;
    }


    /* ========== OBJECTS TREATMENT */


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
                $customer = new Accounts($contractsInvoices->getIdCustomer());
                $contracts->loadById($contractsInvoices->getIdContract());
                $postback = $this->createPostbackURL($id_contract_invoice);


                $object = [
                    "amount" => $numeric->removeEverythingNotNumber($contractsInvoices->getTotalToPay()),
                    "payment_method" => "boleto",
                    "async" => false,
                    "boleto_instructions" => $this->instructions,
                    "boleto_expiration_date" => $date->str2date($contractsInvoices->getDueDate()),
                    "boleto_fine" => $date->str2date($contractsInvoices->getDueDate()),
                    "boleto_fine[amount]" => $numeric->removeEverythingNotNumber((($contractsInvoices->getAmount() * 0.02) * 100)),
                    "boleto_interest[amount]" => $numeric->removeEverythingNotNumber((($contractsInvoices->getAmount() * $contracts->getTaxDailyDelay()) * 100)),
                    "postback_url" => $postback,
                    "customer" => [
                        "external_id" => $objects->fastStr($customer->getIdAccount()),
                        "name" => $objects->fastStr($customer->getFullName()),
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

    private function createCreditCardObject($id_contract_invoice, $id_account_card = null)
    {
        global $pagarme;
        global $objects;
        global $date;
        global $numeric;
        global $contracts;
        global $contractsInvoices;
        global $security;

        try {
            if ($contractsInvoices->load($id_contract_invoice)) {

                $customer = new Accounts($contractsInvoices->getIdCustomer());
                $customerAddress = new AccountsAddress($contractsInvoices->getIdCustomer());
                $contracts->loadById($contractsInvoices->getIdContract());
                $postback = $this->createPostbackURL($id_contract_invoice);
                $security->setIdAccount($contractsInvoices->getIdCustomer());

                $newCard = new AccountsCards($id_account_card, $contractsInvoices->getIdCustomer());

                $id_external_card = $newCard->getIdCardExternal();
                $id_card = $security->decrypt($id_external_card);


                $object = [
                    "amount" => $numeric->removeEverythingNotNumber($contractsInvoices->getTotalToPay()),
                    "payment_method" => "credit_card",
                    "async" => false,
                    "card_id" => $id_card,
                    'card_holder_name' => $objects->fastStr($security->decrypt($newCard->getHolder())),
                    "postback_url" => $postback,
                    "customer" => [
                        "external_id" => $objects->fastStr($customer->getIdAccount()),
                        "name" => $objects->fastStr($customer->getFullName()),
                        "type" => "individual",
                        "country" => "br",
                        "documents" => [
                            [
                                "type" => "cpf",
                                "number" => $objects->fastStr($customer->getDocument())
                            ]
                        ],
                        "phone_numbers" => ["+" . $objects->fastStr($customer->getPhoneNumber())],
                        "email" => $objects->fastStr($customer->getEmail())
                    ],
                    "billing" => [
                        "name" => $customer->getFullName(),
                        "address" => [
                            "country" => $objects->fastStr($customerAddress->getCountrySingle()),
                            "street" => $objects->fastStr($customerAddress->getStreet()),
                            "street_number" => $objects->fastStr($customerAddress->getNumber()),
                            "state" => $objects->fastStr($customerAddress->getState()),
                            "city" => $objects->fastStr($customerAddress->getCity()),
                            "neighborhood" => $objects->fastStr($customerAddress->getNeighborhood()),
                            "zipcode" => $objects->fastStr($numeric->removeEverythingNotNumber($customerAddress->getZipcode()))
                        ]
                    ],
                    'shipping' => [
                        'name' => $customer->getFullName(),
                        'fee' => 0,
                        'delivery_date' => date("Y-m-d"),
                        'expedited' => true,
                        'address' => [
                            "country" => $objects->fastStr($customerAddress->getCountrySingle()),
                            "street" => $objects->fastStr($customerAddress->getStreet()),
                            "street_number" => $objects->fastStr($customerAddress->getNumber()),
                            "state" => $objects->fastStr($customerAddress->getState()),
                            "city" => $objects->fastStr($customerAddress->getCity()),
                            "neighborhood" => $objects->fastStr($customerAddress->getNeighborhood()),
                            "zipcode" => $objects->fastStr($numeric->removeEverythingNotNumber($customerAddress->getZipcode()))
                        ]
                    ],
                    "items" => [
                        [
                            'id' => '1',
                            'title' => 'Serviço de Teste',
                            'unit_price' => 300,
                            'quantity' => 1,
                            'tangible' => true
                        ],
                    ]
                ];
                print_r($object);
                echo "<hr>";
                return $object;
            }
        } catch (Exception $exception) {
            error_log($exception);
        }
        return null;
    }

    /* ========== GETTERS */


    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->api_key;
    }

    /**
     * @return string
     */
    public function getCryptKey(): string
    {
        return $this->crypt_key;
    }

    /**
     * @return mixed
     */
    public function getIdTransaction()
    {
        return $this->id_transaction;
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
    public function getIdTransactionExternal()
    {
        return $this->id_transaction_external;
    }

    /**
     * @return mixed
     */
    public function getIdAcquirerExternal()
    {
        return $this->id_acquirer_external;
    }

    /**
     * @return mixed
     */
    public function getIdDocumentExternal()
    {
        return $this->id_document_external;
    }

    /**
     * @return mixed
     */
    public function getIdCustomerExternal()
    {
        return $this->id_customer_external;
    }

    /**
     * @return mixed
     */
    public function getTransactionToken()
    {
        return $this->transaction_token;
    }

    /**
     * @return mixed
     */
    public function getAuthorizationCode()
    {
        return $this->authorization_code;
    }

    /**
     * @return mixed
     */
    public function getAcquirerName()
    {
        return $this->acquirer_name;
    }

    /**
     * @return mixed
     */
    public function getInvoiceAmount()
    {
        return $this->invoice_amount;
    }

    /**
     * @return mixed
     */
    public function getAuthorizedAmount()
    {
        return $this->authorized_amount;
    }

    /**
     * @return mixed
     */
    public function getPaidAmount()
    {
        return $this->paid_amount;
    }

    /**
     * @return mixed
     */
    public function getRefundedAmount()
    {
        return $this->refunded_amount;
    }

    /**
     * @return mixed
     */
    public function getInstallments()
    {
        return $this->installments;
    }

    /**
     * @return mixed
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @return mixed
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * @return mixed
     */
    public function getCardBrand()
    {
        return $this->card_brand;
    }

    /**
     * @return mixed
     */
    public function getCardHolderName()
    {
        return $this->card_holder_name;
    }

    /**
     * @return mixed
     */
    public function getCardLastDigits()
    {
        return $this->card_last_digits;
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
    public function getPaymentMethod()
    {
        return $this->payment_method;
    }

    /**
     * @return mixed
     */
    public function getDocumentUrl()
    {
        return $this->document_url;
    }

    /**
     * @return mixed
     */
    public function getIpAddress()
    {
        return $this->ip_address;
    }

    /**
     * @return mixed
     */
    public function getResponseData()
    {
        return $this->response_data;
    }

    /**
     * @return mixed
     */
    public function getPostbackUrl()
    {
        return $this->postback_url;
    }

    /**
     * @return mixed
     */
    public function getTid()
    {
        return $this->tid;
    }

    /**
     * @return mixed
     */
    public function getNsu()
    {
        return $this->nsu;
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
    public function getCreatedTime()
    {
        return $this->created_time;
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
    public function getExpireDate()
    {
        return $this->expire_date;
    }

    /**
     * @return mixed
     */
    public function getIsPaid()
    {
        return $this->is_paid;
    }

    /**
     * @return string
     */
    public function getInstructions(): string
    {
        return $this->instructions;
    }

}