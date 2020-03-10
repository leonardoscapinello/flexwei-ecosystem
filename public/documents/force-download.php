<?php
if (isset($_GET['file']) && $_GET['file'] !== null && $_GET['file'] !== "") {
    $filename = $_GET['file'];
    header("Content-Transfer-Encoding: Binary");
    header('Content-Type: application/pdf');
    header('Content-disposition: attachment; filename=' . $filename);
    readfile($filename);
}