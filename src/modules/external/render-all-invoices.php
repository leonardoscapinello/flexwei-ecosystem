<?php

require_once("../../properties/index.php");

try {
    $database->query("SELECT url_token, invoice_key FROM contracts_invoices WHERE is_active = 'Y' AND is_rendered = 'N' AND (due_date >= NOW() + INTERVAL 2 DAY AND due_date < NOW() + INTERVAL 15 DAY)");
    $result = $database->resultset();
    if (count($result) > 0) {
        for ($i = 0; $i < count($result); $i++) {
            $url_token = $result[$i]['url_token'];
            $invoice_key = $result[$i]['invoice_key'];
            $contractsInvoices->render($url_token, $invoice_key);
        }
    }
} catch (Exception $exception) {
    echo $exception;
}


