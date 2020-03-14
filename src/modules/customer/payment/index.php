<?php
$invoice = get_request("iv");
$invoice = $text->base64_decode($invoice);

$contractsInvoices = new ContractsInvoices($invoice);


$contracts = new Contracts($contractsInvoices->getIdContract());

if ($contracts->getIdCustomer() !== $account->getIdAccount()) {
    die("Você não pode visualizar elementos de outros usuários");
}

?>


<?php if (!$address->isAddressRegistered()) { ?>
    <div class="container">
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-sm-12">
                <div class="alert alert-dark fade show" role="alert">
                    <div class="alert-icon"><i class="la la-user-edit"></i></div>
                    <div class="alert-text">
                        Complete seu perfil preenchendo seu endereço residencial ou comercial. <a
                                href="<?= $modules->getModuleUrlById(4) ?>?continue=6&iv=<?= $invoice ?>#v:address">Clique
                            aqui para
                            cadastrar</a>
                    </div>
                    <div class="alert-close">
                        <button type="button" class="alert-close close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true"><i class="la la-close"></i></span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
<?php } else { ?>

    <div class="container">
    <div class="row">

        <div class="col-xl-7 col-lg-7 col-sm-12">

            <div id="invoices">
                <div class="content-block">
                    <h5>DETALHES DA COBRANÇA</h5>


                    <div class="container">
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-sm-12">
                                <div class="input-group">
                                    <label for="">Documento Respectivo</label>
                                    <input type="text" disabled readonly
                                           value="<?= $contracts->getDocumentKey() ?>"/>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-sm-12">
                                <div class="input-group">
                                    <label for="">Fatura Respectiva</label>
                                    <input type="text" disabled readonly
                                           value="<?= $contractsInvoices->getInvoiceKey() ?>"/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-sm-12">
                                <div class="input-group">
                                    <label for="">Vencimento</label>
                                    <input type="text" disabled readonly
                                           value="<?= $date->formatDate($contractsInvoices->getDueDate()) ?>"/>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-sm-12">
                                <div class="input-group">
                                    <label for="">Fatura atual</label>
                                    <input type="text" disabled readonly
                                           value="R$ <?= $numeric->money($contractsInvoices->getAmount()) ?>"/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-sm-12">
                                <div class="input-group">
                                    <label for="">Débitos Anteriores</label>
                                    <input type="text" disabled readonly
                                           value="R$ <?= $numeric->money($contractsInvoices->getPastDebitsAmount()) ?>"/>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-sm-12">
                                <div class="input-group">
                                    <label for="">Pagamentos Recebidos</label>
                                    <input type="text" disabled readonly
                                           value="R$ <?= $numeric->money($transactions->getPaidAmountForInvoice($invoice)) ?>"/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-sm-12">
                                <div class="input-group">
                                    <label for="">Cobranças por atraso</label>
                                    <input type="text" disabled readonly
                                           value="R$ <?= $numeric->money($contractsInvoices->getTaxAmount()) ?>"/>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-sm-12">
                                <div class="input-group">
                                    <label for="">Valor final para pagamento</label>
                                    <input type="text" disabled readonly
                                           value="R$ <?= $numeric->money($contractsInvoices->getAmount2Pay()) ?>"/>
                                </div>
                            </div>
                        </div>
                        <br/>
                        <br/>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-sm-12">
                                <a href="<?= $properties->getSiteURL() ?>download/documents/<?= $invoice ?>"
                                   class="btn" tooltip="Baixar Fatura (espelho) sem código de barras" flow="up"><i
                                            class="fal fa-download"></i> Fazer Download da Fatura</a>
                            </div>
                        </div>


                    </div>
                </div>
            </div>

        </div>

        <div class="col-xl-5 col-lg-5 col-sm-12">
            <div class="content-block">
                <h5>FINALIZAR PAGAMENTO USANDO</h5>


                <div class="choose-payment">

                    <a href="<?= $modules->getModuleUrlById(9) ?>?c&iv=<?= $text->base64_encode($invoice) ?>">
                        <div class="button-block">
                            <div class="left-side">
                                <div class="card">
                                    <div class="card-line"></div>
                                    <div class="card-buttons"></div>
                                </div>
                            </div>
                            <div class="right-side">
                                <div class="new">Cartão de Crédito</div>
                            </div>
                        </div>
                    </a>

                    <a href="<?= $modules->getModuleUrlById(10) ?>?c&iv=<?= $text->base64_encode($invoice) ?>">
                        <div class="button-block">
                            <div class="left-side billet">
                                <div class="billet-el">
                                    <div class="billet-line"></div>
                                    <div class="billet-line"></div>
                                    <div class="billet-line"></div>
                                    <div class="billet-line"></div>
                                    <div class="billet-line"></div>
                                </div>
                            </div>
                            <div class="right-side">
                                <div class="new">Boleto Bancário</div>
                            </div>
                        </div>
                    </a>

                </div>

            </div>

        </div>
    </div>

<?php } ?>