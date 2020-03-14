<?php
$invoice = get_request("iv");
$paywith = get_request("paywith");
$processed_transaction_id = get_request("proc");
$invoice = $text->base64_decode($invoice);

if (!$contractsInvoices->load($invoice)) die("Fatura não reconhecida");


$invoice_loaded = $contractsInvoices->load($text->base64_decode($invoice));
if (!$invoice_loaded) die("Você não pode acessar essa informação");


$paid_amount = $transactions->getTotalPaid($contractsInvoices->getIdContractInvoice());
$total2pay = ($contractsInvoices->getSumTotalPastDebits() + $contractsInvoices->getAmount()) - $paid_amount;


if ($paywith !== null) {

    $card_loaded = $accountsCards->load($paywith);
    if (!$card_loaded) die("Você não pode acessar essa informação");

    $id_transaction = $transactions->register($contractsInvoices->getIdContractInvoice(), $accountsCards->getIdAccountCard());
    header("location: " . $modules->getModuleUrlById(11) . "?iv=" . $text->base64_encode($invoice) . "&tr=" . $text->base64_encode($id_transaction));
    die;

}

$transaction_status = "";


?>
<div class="container">
    <div class="row">
        <div class="col-xl-7 col-lg-8 col-sm-12">

            <div class="content-block">

                <h5 style="padding: 10px 0;">CONFIRME O PAGAMENTO</h5>

                <table width="100%">
                    <tr>
                        <td>Documento</td>
                        <td align="right"><?= $contractsInvoices->getInvoiceKey() ?></td>
                    </tr>
                    <tr>
                        <td>Débitos Anteriores</td>
                        <td align="right">R$ <?= $numeric->money($contractsInvoices->getSumTotalPastDebits()) ?></td>
                    </tr>
                    <tr>
                        <td>Pagamentos Recebidos</td>
                        <td align="right">R$ <?= $numeric->money($paid_amount) ?></td>
                    </tr>
                    <tr>
                        <td>Fatura Atual</td>
                        <td align="right">R$ <?= $numeric->money($contractsInvoices->getAmount()) ?></td>
                    </tr>
                    <tr>
                        <td>Total para Pagamento</td>
                        <td align="right"><b>R$ <?= $numeric->money($total2pay) ?></b></td>
                    </tr>
                </table>

                <h5 style="padding: 10px 0;">Você deseja concluir para o pagamento com boleto bancário?</h5>

                <?php if (!$contractsInvoices->isPaid()) { ?>

                    <?php

                    $billets = $transactions->getAllBilletsOfInvoice($contractsInvoices->getIdContractInvoice());
                    if (count($billets) > 0) {
                        for ($ix = 0; $ix < count($billets); $ix++) { ?>

                            <a href="<?=$properties->getSiteURL()?>download/external?f=<?=$text->base64_encode($billets[$ix]['document_url'])?>" target="_blank">
                                <div class="billet-download-block">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-xl-2 col-lg-2 col-sm-12">
                                                <div class="icon-max">
                                                    <i class="fal fa-barcode"></i>
                                                </div>
                                            </div>
                                            <div class="col-xl-7 col-lg-7 col-sm-12">
                                                <div class="content-block">
                                                    <p><b><?= ($billets[$ix]['barcode']) ?></b></p>
                                                    <p>
                                                        Vencimento: <?= $date->formatDate($billets[$ix]['expire_date']) ?>
                                                        | Valor:
                                                        R$ <?= $numeric->money($billets[$ix]['invoice_amount']) ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>

                        <?php }
                    } else { ?>

                        <button class="btn">Sim, emitir boleto bancário</button>

                    <?php } ?>


                <?php } else { ?>
                    <?php


                    if ("" === $transaction_status) { ?>
                        <div class="alert alert-success fade show" role="alert">
                            <div class="alert-icon"><i class="fal fa-smile-wink"></i></div>
                            <div class="alert-text">
                                Por aqui, tudo certo! Você já fez o pagamento e recebemos o valor de
                                <b>R$ <?= $numeric->money($paid_amount) ?>.</b>
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
                <h5 style="padding: 10px 0;">Boleto Bancário</h5>

                <p style="text-align: justify">Alguns pontos importantes para pagamento com boleto bancário:</p>
                <ul class="bullet">
                    <li>O tempo de compensação do pagamento fica em torno de 48 à 72 horas úteis, contando a partir do
                        próximo dia útil após o pagamento.
                    </li>
                    <li>Após a data de vencimento não é possível concluir o pagamento na mesma via do boleto, sendo
                        necessário emissão de uma nova folha, qual acarretará cobranças adicionais por atraso.
                    </li>
                </ul>
            </div>
        </div>

    </div>
</div>