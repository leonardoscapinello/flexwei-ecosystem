<?php
$invoice = get_request("iv");
$transaction = get_request("tr");

$blt = get_request("blt");
$id_module = 9;
if ($blt !== null) $id_module = 10;

header("refresh:4;url=" . $modules->getModuleUrlById($id_module) . "?iv=" . $invoice . "&proc=" . $transaction);

?>


<div class="container">
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-sm-12">

            <div class="payment-loader">
                <div class="pad">
                    <div class="chip"></div>
                    <div class="line line1"></div>
                    <div class="line line2"></div>
                </div>
            </div>

            <div class="loader-text">
                <h3>Processando Transação</h3>
                <p>Por favor, aguarde enquanto concluímos.</p>
            </div>


        </div>
    </div>
</div>
