<?php
$document_key = get_request("prm1");
if (not_empty($document_key)) {
    $contracts = new Contracts($document_key);
    $contractsServices = new ContractsServices();
    $contractsInvoices = new ContractsInvoices();
}

?>

<div class="tab-header">
    <div class="container">
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-sm-12">
                <div class="tab">
                    <button data-nav="request_user" class="tablinks active" onclick="openTab('request_user')">
                        Contratante
                    </button>
                    <button data-nav="document" class="tablinks" onclick="openTab('document')">Documento</button>
                    <button data-nav="charges" class="tablinks" onclick="openTab('charges')">Serviços</button>
                    <button data-nav="invoices" class="tablinks" onclick="openTab('invoices')">Faturas</button>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="container">
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-sm-12">
            <div id="request_user" class="tabcontent" style="display: block;">
                <div class="content-block">
                    <h5>Informações Pessoais</h5>
                    <div class="container">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-sm-4">
                                <div class="input-group">
                                    <label for="">Nome Completo</label>
                                    <input type="text" disabled readonly value="<?= $account->getFullName() ?>"/>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-sm-4">
                                <div class="input-group">
                                    <label for="">E-mail para Contato</label>
                                    <input type="text" disabled readonly value="<?= $account->getEmail() ?>"/>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-sm-4">
                                <div class="input-group">
                                    <label for="">Celular para Contato</label>
                                    <input type="text" disabled readonly
                                           value="<?= $account->getMaskedPhoneNumber() ?>"/>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-xl-4 col-lg-4 col-sm-4">
                                <div class="input-group">
                                    <label for="">Documento</label>
                                    <input type="text" disabled readonly value="<?= $account->getMaskedDocument() ?>"/>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-sm-4">
                                <div class="input-group">
                                    <label for="">Status do Contrato</label>
                                    <?php if ($contracts->isActive()) { ?>
                                        <span class="stamp success full-width">Vigente</span>
                                    <?php } else { ?>
                                        <span class="stamp error  full-width">Revogado</span>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h5>Endereço</h5>
                    <div class="container">
                        <div class="row">
                            <div class="col-xl-8 col-lg-8 col-sm-8">
                                <div class="input-group">
                                    <label for="">Endereço</label>
                                    <input type="text" disabled readonly value=""/>
                                </div>
                            </div>
                            <div class=" col-xl-4 col-lg-4 col-sm-4">
                                <div class="input-group">
                                    <label for="">Cidade</label>
                                    <input type="text" disabled readonly value=""/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-sm-4">
                                <div class="input-group">
                                    <label for="">Estado/Província</label>
                                    <input type="text" disabled readonly value=""/>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-sm-4">
                                <div class="input-group">
                                    <label for="">País</label>
                                    <input type="text" disabled readonly value=""/>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-sm-4">
                                <div class="input-group">
                                    <label for="">Código Postal</label>
                                    <input type="text" disabled readonly value=""/>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div id="document" class="tabcontent">
                <div class="content-block">
                    <h5>Dados Internos</h5>
                    <div class="container">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-sm-4">
                                <div class="input-group">
                                    <label for="">Documento</label>
                                    <input type="text" disabled readonly value="<?= $contracts->getDocumentKey() ?>"/>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-sm-4">
                                <div class="input-group">
                                    <label for="">Registro</label>
                                    <input type="text" disabled readonly
                                           value="<?= $date->formatDate($contracts->getInsertTime()) ?>"/>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-sm-4">
                                <div class="input-group">
                                    <label for="">Vigente até</label>
                                    <input type="text" disabled readonly
                                           value="<?= $date->formatDateOrMessage($contracts->getExpireDate(), "cancelamento") ?>"/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-sm-4">
                                <div class="input-group">
                                    <label for="">Versão do Documento</label>
                                    <input type="text" disabled readonly value="<?= $contracts->getVersion() ?>"/>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-sm-4">
                                <div class="input-group">
                                    <label for="">Última Atualização</label>
                                    <input type="text" disabled readonly
                                           value="<?= $date->formatDate($contracts->getUpdateTime()) ?>"/>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-sm-4">
                                <div class="input-group">
                                    <label for="">Data mínima para cancelamento</label>
                                    <input type="text" disabled readonly
                                           value="<?= $date->formatDateOrMessage($contracts->getCancelDateAllowed(), "sem fidelidade") ?>"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h5>FINANCEIRO</h5>
                    <div class="container">
                        <div class="row">
                            <div class="col-xl-3 col-lg-3 col-sm-3">
                                <div class="input-group">
                                    <label for="">Dia do vencimento</label>
                                    <select name="payday" id="payday" disabled>
                                        <?php for ($i = 6; $i < 28; $i++) { ?>
                                            <option value="<?= $i ?>" <?= is_selected($i, $contracts->getPayday()) ?>><?= $i < 10 ? "0" . $i : $i ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-sm-3">
                                <div class="input-group">
                                    <label for="">Próximo fechamento</label>
                                    <input type="text" disabled readonly
                                           value="<?= $contracts->getNextClosureDate() ?>"/>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-sm-3">
                                <div class="input-group">
                                    <label for="">Vencimento desse mês</label>
                                    <input type="text" disabled readonly value="<?= $contracts->getNextPayDay() ?>"/>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-sm-3">
                                <div class="input-group">
                                    <label for="">Juros por atraso (diário)</label>
                                    <input type="text" disabled readonly
                                           value="<?= $numeric->percent($contracts->getTaxDailyDelay()) ?>"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="charges" class="tabcontent">
                <div class="content-block">
                    <h5>Resumo dos Serviços Relacionados</h5>


                    <?php
                    $service_list = $contractsServices->getList($document_key);
                    if (count($service_list) > 0) {
                        ?>
                        <div class="content-list zero-padding">
                            <div class="list-header">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-xl-4 col-lg-4 col-sm-12">atividade</div>
                                        <div class="col-xl-2 col-lg-2 col-sm-12">horas exec.</div>
                                        <div class="col-xl-2 col-lg-2 col-sm-12">valor (R$)</div>
                                        <div class="col-xl-2 col-lg-2 col-sm-12">pagamento</div>
                                        <div class="col-xl-2 col-lg-2 col-sm-12">inicio</div>
                                    </div>
                                </div>
                            </div>

                            <?php
                            for ($i = 0; $i < count($service_list); $i++) {


                                ?>
                                <div class="list-item clickable"
                                     onClick="gotoPage('<?= $modules->getEncodedModuleUrlById(3) ?>', '<?= md5($service_list[$i]["id_service"]) ?>');">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-xl-4 col-lg-4 col-sm-12 line-middle">
                                                <b><?= $service_list[$i]['service_name'] ?></b>
                                                <p class="mute"></p>
                                            </div>
                                            <div class="col-xl-2 col-lg-2 col-sm-12 line-middle"><?= $service_list[$i]['hired_hours'] ?></div>
                                            <div class="col-xl-2 col-lg-2 col-sm-12 line-middle"><?= $service_list[$i]['total_amount'] ?></div>

                                            <div class="col-xl-2 col-lg-2 col-sm-12 line-middle"><?= $date->formatDate($service_list[$i]['insert_time']) ?></div>

                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                        </div>

                    <?php } else { ?>
                        <div class="container">
                            <div class="row">
                                <div class="offset-3"></div>
                                <div class="col-xl-7 col-lg-7 col-sm-12" align="center">
                                    <p>Nenhum serviço foi relacionado ao contrato. Entre em contato com o gerente
                                        responsável por sua conta para mais informações.</p>
                                </div>
                                <div class="offset-3"></div>
                            </div>
                        </div>
                    <?php } ?>

                </div>
            </div>
            <div id="invoices" class="tabcontent">
                <div class="content-block">
                    <h5>Cobranças relacionadas a este documento</h5>


                    <?php
                    $month_invoice = $contractsInvoices->getThisMonthInvoice($contracts->getIdContract());
                    $all_past_invoices = $contractsInvoices->getAllInvoices($contracts->getIdContract());
                    //print_r($all_past_invoices);
                    $exist_this_month_invoice = intval(count($month_invoice));
                    $exist_past_months_invoices = intval(count($all_past_invoices));
                    $all_docs = $exist_this_month_invoice + $exist_past_months_invoices;
                    if ($all_docs > 0) { ?>

                        <?php if ($exist_this_month_invoice > 0) { ?>
                            <div class="current-month-invoice">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-xl-6 col-lg-6 col-sm-12">
                                            <h4>Olá, <?= $account->getFirstName() ?>.</h4>
                                            <p>Sua fatura do mês
                                                de <?= $text->lowercase($date->getMonthNameFromDate($month_invoice['due_date'])) ?>
                                                já está disponível para download.</p>
                                        </div>
                                        <div class="offset-2"></div>
                                        <div class="col-xl-4 col-lg-4 col-sm-12 actions" style="text-align: right">
                                            <a href="<?= $properties->getSiteURL() ?>download/documents/<?= $month_invoice['invoice_key'] ?>"
                                               class="btn" tooltip="Baixar fatura" flow="up"><i
                                                        class="fal fa-download"></i></a>
                                            <a href="<?= $modules->getModuleUrlById(6) ?>?iv=<?= $text->base64_encode($month_invoice['invoice_key']) ?>"
                                               class="btn" tooltip="Continuar para pagamento" flow="up"><i
                                                        class="fal fa-money-check"></i></a>
                                            <a href="#" class="btn" tooltip="Preciso de ajuda, chamar o suporte"
                                               flow="up"><i
                                                        class="fal fa-headset"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <?php if ($exist_past_months_invoices > 0) { ?>


                            <div class="content-list">
                                <div class="list-header">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-xl-3 col-lg-3 col-sm-12">Documento</div>
                                            <div class="col-xl-2 col-lg-3 col-sm-12">Valor</div>
                                            <div class="col-xl-2 col-lg-2 col-sm-12">Vencimento</div>
                                            <div class="col-xl-2 col-lg-2 col-sm-12">Status</div>
                                            <div class="col-xl-2 col-lg-2 col-sm-12"></div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                for ($i = 0; $i < $exist_past_months_invoices; $i++) {
                                    $invoiceStatusProperties = $contractsInvoices->getStatusProperties($all_past_invoices[$i]['id_contract_invoice']);
                                    ?>
                                    <div class="list-item">
                                        <div class="container">
                                            <div class="row">
                                                <div class="col-xl-2 col-lg-2 col-sm-12 line-middle">
                                                    <b><?= $all_past_invoices[$i]['invoice_key'] ?></b>
                                                    <p class="mute"></p>
                                                </div>
                                                <div class="col-xl-2 col-lg-2 col-sm-12 line-middle">
                                                    R$ <?= $numeric->money($all_past_invoices[$i]['amount']) ?></div>
                                                <div class="col-xl-2 col-lg-2 col-sm-12 line-middle"><?= $date->formatDate($all_past_invoices[$i]['due_date']) ?></div>
                                                <div class="col-xl-3 col-lg-3 col-sm-12 line-middle">
                                                    <span class="stamp <?= $invoiceStatusProperties[1] ?>"><?= $invoiceStatusProperties[0] ?></span>
                                                </div>
                                                <div class="col-xl-3 col-lg-3 col-sm-12 line-middle">

                                                    <?php if ($all_past_invoices[$i]['status'] === "2" || $all_past_invoices[$i]['status'] === "4" || $all_past_invoices[$i]['status'] === "6") { ?>

                                                        <a href="<?= $modules->getModuleUrlById(7) ?>?iv=<?= $text->base64_encode($all_past_invoices[$i]['invoice_key']) ?>"
                                                           class="btn" tooltip="Continuar para pagamento" flow="up"
                                                           target="_blank"><i class="fal fa-money-check"></i></a>

                                                    <?php } ?>

                                                    <?php if ($all_past_invoices[$i]['is_rendered'] === "Y" && ($all_past_invoices[$i]['status'] !== "1" && $all_past_invoices[$i]['status'] !== "5" && $all_past_invoices[$i]['status'] !== "7")) { ?>
                                                        <a href="<?= $properties->getSiteURL() ?>download/documents/<?= urlencode($all_past_invoices[$i]['invoice_key']) ?>"
                                                           class="btn" tooltip="Baixar Fatura" flow="up"
                                                           target="_blank"><i class="fal fa-download"></i></a>
                                                    <?php } else { ?>
                                                        <a href="#" class="btn disabled"
                                                           tooltip="<?=$all_past_invoices[$i]['status'] === "5" ? "Essa fatura foi cancelada" : "Aguarde até fechamento para baixar essa fatura."?>"
                                                           flow="up"><i class="fal fa-download"></i></a>
                                                    <?php } ?>

                                                    <?php if ($all_past_invoices[$i]['status'] !== "2" && $all_past_invoices[$i]['status'] !== "4" && $all_past_invoices[$i]['status'] !== "6") { ?>
                                                        <a href="#" class="btn"
                                                           tooltip="Preciso de ajuda, chamar o suporte"
                                                           flow="up"><i class="fal fa-headset"></i></a>
                                                    <?php } ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>


                        <?php } ?>

                    <?php } else { ?>
                        <div class="container">
                            <div class="row">
                                <div class="offset-3"></div>
                                <div class="col-xl-7 col-lg-7 col-sm-12" align="center">
                                    <p>Nenhum documento de cobrança foi gerado até o momento, considere próximo ao dia
                                        <b><?= $contracts->getNextClosureDate() ?></b> para que sua fatura esteja
                                        visível ou entre em contato com nosso atendimento financeiro para mais detalhes:
                                        <b>Menu superior</b> &raquo; <b>Atendimento</b> &raquo; <b>Faturas</b></p>
                                </div>
                                <div class="offset-3"></div>
                            </div>
                        </div>
                    <?php } ?>


                </div>
            </div>

        </div>
    </div>
</div>