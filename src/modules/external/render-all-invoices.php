<?php

require_once("../../properties/index.php");

try {
    $database->query("SELECT url_token, invoice_key FROM contracts_invoices WHERE is_active = 'Y' AND is_rendered = 'N' AND (due_date >= NOW() + INTERVAL 2 DAY AND due_date < NOW() + INTERVAL 10 DAY)");
    $result = $database->resultset();
    if (count($result) > 0) {

        for ($i = 0; $i < count($result); $i++) {

            echo $result[$i]['invoice_key'];

            $url = $properties->getSiteURL() . "e/i/render/" . $result[$i]['url_token'] . "?filename=" . $result[$i]['invoice_key'];
//
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
            curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $output = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpcode === 200) {
                $database->query("UPDATE contracts_invoices SET is_rendered = 'Y' WHERE is_rendered = 'N' AND invoice_key = ?");
                $database->bind(1, $result[$i]['invoice_key']);
                $database->execute();
            }


        }
    }
} catch (Exception $exception) {
    echo $exception;
}


