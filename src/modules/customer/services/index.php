<?php
$id_service = get_request("prm1");
if (not_empty($id_service)) $contractsServices->load($id_service);

$id_contract = $contractsServices->getIdContract();
if (not_empty($id_contract)) $contracts->loadById($id_contract);
?>

<div class="tab-header">
    <div class="container">
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-sm-12">
                <div class="tab">
                    <button data-nav="details" class="tablinks active" onclick="openTab('details')">Detalhes do Serviço</button>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="container">
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-sm-12">
            <div id="details" class="tabcontent" style="display: block;">
                <div class="content-block">
                    <h5>Detalhes do Contrato Relacionado</h5>
                    <div class="container">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-sm-4">
                                <div class="input-group">
                                    <label for="">Valor por hora do serviço</label>
                                    <input type="text" disabled readonly
                                           value="R$ <?= $numeric->money($contractsServices->getUnitaryPrice()) ?>"/>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-sm-4">
                                <div class="input-group">
                                    <label for="">Horas contratadas</label>
                                    <input type="text" disabled readonly
                                           value="<?= $numeric->numberToHoursMinutes($contractsServices->getHiredHours()) ?>"/>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-sm-4">
                                <div class="input-group">
                                    <label for="">Valor final do projeto</label>
                                    <input type="text" disabled readonly
                                           value="R$ <?= $numeric->money($contractsServices->getTotalAmount()) ?>"/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-sm-4">
                                <div class="input-group">
                                    <label for="">Documento de Origem</label>
                                    <input type="text" disabled readonly
                                           value="<?= $contracts->getDocumentKey() ?>"/>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-sm-4">
                                <div class="input-group">
                                    <label for="">Juros por atraso</label>
                                    <input type="text" disabled readonly
                                           value="<?= $numeric->percent($contracts->getTaxDailyDelay()) ?>"/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5>Detalhes do Projeto</h5>
                    <div class="container">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-sm-12">
                                <div class="input-group">
                                    <label for="">Nome do Projeto</label>
                                    <input type="text" disabled readonly
                                           value="<?= $contractsServices->getServiceTitle() ?>"/>
                                </div>
                            </div>
                            <div class="col-xl-12 col-lg-12 col-sm-12">
                                <div class="input-group">
                                    <label for="">Descrição</label>
                                    <textarea disabled readonly>
                                        <?= $contractsServices->getServiceDescription() ?>
                                    </textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>