<?php
$invoice = get_request("iv");
$paywith = get_request("paywith");
$processed_transaction_id = get_request("proc");
$invoice = $text->base64_decode($invoice);

$contractsInvoices = new ContractsInvoices($invoice);


if ($paywith !== null) {

    $card_loaded = $accountsCards->load($paywith);
    if (!$card_loaded) die("Você não pode acessar essa informação");
    $id_transaction = $transactions->register($contractsInvoices->getIdContractInvoice(), $accountsCards->getIdAccountCard());
    header("location: " . $modules->getModuleUrlById(11) . "?iv=" . $text->base64_encode($invoice) . "&tr=" . $text->base64_encode($id_transaction));
    die;

}

$transaction_status = "";
if ($processed_transaction_id !== null) {
    $transactions->load($text->base64_decode($processed_transaction_id));
    $transaction_status = $transactions->getStatus();
}



$transactions->setThisAndPastAsPaid(8, 2224.80);

?>
<div class="container">
    <div class="row">
        <div class="col-xl-7 col-lg-7 col-sm-12">

            <div class="content-block">

                <?php if ($transaction_status === "refused") { ?>
                    <div class="alert alert-danger fade show" role="alert">
                        <div class="alert-icon"><i class="fal fa-shield"></i></div>
                        <div class="alert-text">
                            Seu cartão foi recusado, portanto não foi possível concluir a transação. Tente novamente.
                        </div>
                        <div class="alert-close">
                            <button type="button" class="alert-close close" data-dismiss="alert"
                                    aria-label="Close">
                                <span aria-hidden="true"><i class="la la-close"></i></span>
                            </button>
                        </div>
                    </div>
                <?php } elseif ($transaction_status === "paid") { ?>

                    <div class="alert alert-success fade show" role="alert">
                        <div class="alert-icon"><i class="fal fa-badge-check"></i></div>
                        <div class="alert-text">
                            Deu tudo certo! Seu pagamento foi aprovado.
                        </div>
                        <div class="alert-close">
                            <button type="button" class="alert-close close" data-dismiss="alert"
                                    aria-label="Close">
                                <span aria-hidden="true"><i class="la la-close"></i></span>
                            </button>
                        </div>
                    </div>
                <?php } ?>


                <h5 style="padding: 10px 0;">CONFIRME O PAGAMENTO</h5>

                <table width="100%">
                    <tr>
                        <td>Documento</td>
                        <td align="right"><?= $contractsInvoices->getInvoiceKey() ?></td>
                    </tr>
                    <tr>
                        <td>Débitos Anteriores</td>
                        <td align="right">R$ <?= $numeric->money($contractsInvoices->getPastDebitsAmount()) ?></td>
                    </tr>
                    <tr>
                        <td>Cobranças adicionais por atraso</td>
                        <td align="right">R$ <?= $numeric->money($contractsInvoices->getTaxAmount()) ?></td>
                    </tr>
                    <tr>
                        <td>Fatura Atual</td>
                        <td align="right">R$ <?= $numeric->money($contractsInvoices->getAmount()) ?></td>
                    </tr>
                    <tr>
                        <td>Total para pagamento</td>
                        <td align="right">
                            R$ <?= $numeric->money($contractsInvoices->getAmountSumPast()) ?></td>
                    </tr>

                    <tr>
                        <td>Pagamentos Recebidos</td>
                        <td align="right">
                            <span class="green">
                            R$ <?= $numeric->money($transactions->getPaidAmountForInvoice($contractsInvoices->getInvoiceKey())) ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td>Total restante</td>
                        <td align="right"><b>R$ <?= $numeric->money($contractsInvoices->getAmount2Pay()) ?></b></td>
                    </tr>
                </table>

                <?php if (!$contractsInvoices->isPaid()) { ?>

                    <h5 style="padding: 10px 0;">Selecione um dos seus cartões de crédito</h5>


                    <?php

                    $cards = $accountsCards->list();
                    for ($i = 0; $i < count($cards); $i++) {

                        $id_account_card = $cards[$i]['id_account_card'];
                        $brand = $cards[$i]['brand'];
                        $last_digits = $cards[$i]['last_digits'];
                        $is_valid = $cards[$i]['is_valid'];
                        $is_default = $cards[$i]['is_default'];

                        if ($is_valid === "Y") {
                            ?>

                            <div class="cards_inner__card <?= $is_valid === "Y" ? "" : "disabled" ?> <?= $security->decrypt($brand) ?>"
                                 onClick="gotoPage('<?= $modules->getEncodedModuleUrlById(9, array("iv" => $text->base64_encode($invoice), "paywith" => md5($id_account_card))) ?>', '');">
                                <div class='logo'></div>
                                <div class="card_digits">**** <?= $security->decrypt($last_digits) ?></div>
                            </div>

                        <?php } ?>
                    <?php } ?>


                    <div class="cards_inner__card plus"
                         onClick="gotoPage('<?= $modules->getEncodedModuleUrlById(4, 'finance') ?>', '');">
                        <div class='fal fa-plus'
                             style="width: 60px;height: 60px;font-size:30px;font-weight: bold;text-align:center;line-height: 60px;position:  absolute;top: 50%;left: 50%;-webkit-transform: translate(-50%,-50%);-moz-transform: translate(-50%,-50%);-ms-transform: translate(-50%,-50%);-o-transform: translate(-50%,-50%);transform: translate(-50%,-50%);"></div>
                    </div>

                <?php } else { ?>
                    <?php if ("" === $transaction_status) { ?>
                        <div class="alert alert-success fade show" role="alert">
                            <div class="alert-icon"><i class="fal fa-smile-wink"></i></div>
                            <div class="alert-text">
                                Por aqui, tudo certo! Você já fez o pagamento e recebemos o valor de
                                <b>R$ <?= $numeric->money($transactions->getPaidAmountForInvoice($contractsInvoices->getInvoiceKey())) ?>
                                    .</b>
                            </div>
                            <div class="alert-close">
                                <button type="button" class="alert-close close" data-dismiss="alert"
                                        aria-label="Close">
                                    <span aria-hidden="true"><i class="la la-close"></i></span>
                                </button>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>

            </div>


        </div>

        <div class="offset-1"></div>

        <div class="col-xl-4 col-lg-4 col-sm-12">
            <div class="content-block">
                <h5 style="padding: 10px 0;">Cadê meu cartão?</h5>

                <p style="text-align: justify">Se você não encontrou seu cartão de crédito cadastrado ao lado, existem
                    algumas razões para isso:</p>
                <ul class="bullet">
                    <li>O cartão de crédito ainda não foi cadastrado em seu perfil, portanto você pode acessa-lo
                        <a href="#"
                           onClick="gotoPage('<?= $modules->getEncodedModuleUrlById(4, 'finance') ?>', '');">clicando
                            aqui.</a>
                    </li>
                    <li>O cartão de crédito cadastrado não pôde ser verificado, portanto não pode ser usado em
                        pagamentos.
                    </li>
                    <li>O cartão de crédito foi recusado inumeras vezes e então desabilitado.
                    </li>
                </ul>
            </div>
        </div>

    </div>
</div>