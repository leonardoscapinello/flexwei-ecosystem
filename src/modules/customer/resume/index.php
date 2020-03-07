<!-- TABS LIST -->
<div class="tab-header">
    <div class="container">
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-sm-12">
                <div class="tab">
                    <button data-nav="contracts" class="tablinks active" onclick="openTab('contracts')">Contratos
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!$address->isAddressRegistered()) { ?>
    <div class="container">
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-sm-12">
                <div class="alert alert-dark fade show" role="alert">
                    <div class="alert-icon"><i class="la la-user-edit"></i></div>
                    <div class="alert-text">
                        Complete seu perfil preenchendo seu endereço residencial ou comercial. <a
                                href="<?= $modules->getModuleUrlById(4) ?>#v:address">Clique aqui para
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
<?php } ?>

<div class="container">
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-sm-12">
            <div id="request_user" class="tabcontent" style="display: block;">
                <div class="content-list">
                    <div class="list-header">
                        <div class="container">
                            <div class="row">
                                <div class="col-xl-3 col-lg-3 col-sm-12">Documento</div>
                                <div class="col-xl-3 col-lg-3 col-sm-12">responsável</div>
                                <div class="col-xl-2 col-lg-2 col-sm-12">registro</div>
                                <div class="col-xl-2 col-lg-2 col-sm-12">vigente até</div>
                                <div class="col-xl-2 col-lg-2 col-sm-12">status</div>
                            </div>
                        </div>
                    </div>
                    <?php
                    $contract_list = $contracts->getAuthenticatedUserContracts();
                    for ($i = 0; $i < count($contract_list); $i++) {
                        ?>
                        <div class="list-item clickable"
                             onClick="gotoPage('<?= $modules->getEncodedModuleUrlById(2) ?>', '<?= $contract_list[$i]["document_key"] ?>');">
                            <div class="container">
                                <div class="row">
                                    <div class="col-xl-3 col-lg-3 col-sm-12 line-middle">
                                        <b><?= $contract_list[$i]['document_key'] ?></b>
                                        <p class="mute"></p>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-sm-12 line-middle"><?= $contract_list[$i]['first_name'] ?></div>
                                    <div class="col-xl-2 col-lg-2 col-sm-12 line-middle"><?= $date->formatDate($contract_list[$i]['insert_time']) ?></div>
                                    <div class="col-xl-2 col-lg-2 col-sm-12 line-middle"><?= $date->formatDateOrMessage($contract_list[$i]['expire_date'], "cancelamento") ?></div>
                                    <div class="col-xl-2 col-lg-2 col-sm-12 line-middle">
                                        <?php if ($contract_list[$i]['is_active'] === "Y") { ?>
                                            <span class="stamp success full-width">Vigente</span>
                                        <?php } else { ?>
                                            <span class="stamp error  full-width">Revogado</span>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
