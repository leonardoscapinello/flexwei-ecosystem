<?php
require_once("../../properties/index.php");

$document = get_request("d");
if ($document === null) die("Unable to load");

$contractsInvoices->massiveRegister($document);
