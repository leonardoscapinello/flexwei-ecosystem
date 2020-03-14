<?php
require_once("../../properties/index.php");

$id_transaction = get_request("id");
$event = get_request("event");
$old_status = get_request("old_status");
$desired_status = get_request("desired_status");
$current_status = get_request("current_status");
$transactionObj = get_request("transaction");

$transactions = new Transactions();

$transactions->updateStatus($id_transaction, $current_status, $transactionObj);