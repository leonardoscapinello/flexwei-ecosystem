<?php
$invoice = get_request("iv");
$transaction = get_request("tr");
header("refresh:5;url=" . $modules->getModuleUrlById(9) . "?iv=" . $invoice . "&proc=" . $transaction);

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
