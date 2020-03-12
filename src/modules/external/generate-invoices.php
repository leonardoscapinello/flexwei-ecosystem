<?php
require_once("../../properties/index.php");

$document = get_request("d");
//if ($document === null) die("Unable to load");


ignore_user_abort(1); // run script in background
$contractsInvoices->getAllContractsHasNotCreatedAllInvoicesAndDo();
