<?php
require_once("../../properties/index.php");
$url_token = get_request("url_token");
$filename = get_request("filename");
ob_start();

require_once("view-invoice.php");


if (get_request("disableRender") !== "Y") {

    $html = ob_get_clean();
    $html = preg_replace('/>\s+</', "><", $html);

    $dompdf = new \Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $pdf_gen = $dompdf->output();

    if (!file_put_contents(DIRNAME . "../../public/documents/$filename.pdf", $pdf_gen)) {
        http_response_code(500);
    } else {
        http_response_code(200);
    }
}
?>