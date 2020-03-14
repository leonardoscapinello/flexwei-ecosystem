<?php
require_once("../../src/properties/index.php");

$allow = true;


// EXTERNAL DOWNLOAD
if (get_request("f") !== null) {

    $file = $text->base64_decode(get_request("f"));

    header("location: " . $file);

// INTERNAL DOWNLOAD
} elseif (get_request("file")) {

    $filename = get_request("file");
    $filename_ini = substr($filename, 0, -4); /// remove .pdf

    $contractsInvoices = new ContractsInvoices($filename_ini);

    if (!$contractsInvoices->invoicePDFExists($filename_ini)) {
        $url_token = $contractsInvoices->getUrlToken();
        $contractsInvoices->render($url_token, $filename_ini);
    }


    if ($allow) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($filename));
        // header("Content-Encoding: gzip");
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header("Content-Length: " . filesize($filename));
        header('Content-Transfer-Encoding: binary');
        header('Connection: Keep-Alive');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=1, pre-check=1');
        ob_get_clean();
        readfile($filename);
        exit;
    }
}