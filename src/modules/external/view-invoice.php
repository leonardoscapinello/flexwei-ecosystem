<?php
if (!$contractsInvoices->loadByURLToken($url_token)) {
    die;
}
$id_customer = $contractsInvoices->getIdCustomer();
$contracts->loadById($contractsInvoices->getIdContract());
$customer = new Accounts($id_customer);
$paid_amount = $transactions->getTotalPaid($contractsInvoices->getIdContractInvoice());

$pastdebits = $contractsInvoices->getSumTotalPastDebits();
$thisamount = $contractsInvoices->getAmount();
$total2pay = (($pastdebits + $thisamount) - $paid_amount);

?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Flexwei: Sua fatura de <?= $text->lowercase($date->getMonthNameFromDate($contractsInvoices->getDueDate())) ?> chegou.</title>
    <link href="../../../public/stylesheet/invoice-print.css?v=<?= date("dmHis") ?>"
          rel="stylesheet" type="text/css">
    <link href="../../../public/fonts/gilroy/Gilroy.css" rel="stylesheet">
    <link href="../../../public/stylesheet/container.css" rel="stylesheet">
    <link href="../../../public/stylesheet/fontawesome.all.min.css" rel="stylesheet">

    <script type="text/javascript">
        //document.addEventListener('contextmenu', event => event.preventDefault());
    </script>
</head>
<body>
<div class="page">
    <div class="header">
        <img src="../../../public/images/invoices/invoice-header.png" class="fullwidth">
    </div>
    <div class="section-welcome" style="margin-top: 75">
        <h1 class="pink" style="line-height: .8;margin-top: -140;margin-bottom:80">
            Olá, <?= $customer->getFirstName() ?>.<br/>
            Esta é a sua fatura
            de <?= $text->lowercase($date->getMonthNameFromDate($contractsInvoices->getDueDate())) ?>, no valor de
            <br>R$ <?= $numeric->money($contractsInvoices->getAmount()) ?>.
        </h1>

        <div class="welcome-main-box">
            <div class="box">
                <img src="../../../public/images/invoices/hands.png">
                <p style="line-height: 1">Nós estamos crescendo e te agradecemos por fazer parte dessa evolução.</p>
            </div>
            <div class="box">
                <img src="../../../public/images/invoices/support.png">
                <p style="line-height: 1">Se você precisar de ajuda, mande-nos um e-mail com sua solicitação:
                    suporte@flexwei.com</p>
            </div>
            <div class="box">
                <img src="../../../public/images/invoices/stars.png">
                <p style="line-height: 1">Queremos garantir uma experiência 5 estrelas, se você tiver alguma sugestão,
                    estamos prontos a te
                    ouvir.</p>
            </div>
        </div>
    </div>
</div>
<div class="page">
    <div class="header">
        <div class="header-image">
            <img src="../../../public/images/invoices/invoice-header-smallest.png">
        </div>
        <div class="header-customer" style="margin-right: 115">
            <p><?= $customer->getFullName() ?><span
                        style="font-weight:300;margin-left:3px;"><?= $customer->getDocument() ?></span></p>
            <p>FATURA <span><?= $contractsInvoices->getSmallDueDate() ?></span> EMISSÃO
                <span><?= $contractsInvoices->getSmallInsertTime() ?></span></p>
        </div>
    </div>
    <div class="section-resume">
        <div class="resume-table-header">
            <div class="header-resume">
                <p style="position:relative; top: 5">RESUMO</p>
                <p class="right" style="margin-bottom: 30">VALOR EM R$</p>
            </div>
        </div>
        <div class="resume-table-content">
            <div class="inside">
                <div class="line" style="height: 14">
                    <div class="f1">Débitos anteriores</div>
                    <div class="f2"><?= $numeric->money($pastdebits) ?></div>
                </div>
                <div class="line" style="height: 14">
                    <div class="f1">Descontos</div>
                    <div class="f2"><?= $numeric->money(0) ?></div>
                </div>
                <div class="line" style="height: 14">
                    <div class="f1">Fatura atual</div>
                    <div class="f2"><?= $numeric->money($thisamount) ?></div>
                </div>
                <div class="line" style="height: 14">
                    <div class="f1">Pagamentos recebidos</div>
                    <?php if ($paid_amount > 0) { ?>
                        <div class="f2 green"><?= $numeric->money($paid_amount) ?>
                        </div>
                    <?php } else { ?>
                        <div class="f2"><?= $numeric->money($paid_amount) ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="resume-table-content total-amount">
            <div class="inside">
                <div class="line" style="height: 14">
                    <div class="f1"></div>
                    <div class="f2">TOTAL A PAGAR <h3 class="pink">R$ <?= $numeric->money($total2pay) ?></h3></div>
                </div>
            </div>
        </div>
        <div class="resume-table-content">
            <div class="inside-terms">
                <p>Esta fatura consolida todos os eventuais débitos anteriores</p>
                <p>Se o valor total não for pago, serão cobrados juros e IOF sobre a diferença entre o valor total e
                    valor pago, além de multa.</p>
                <p>Para pagar a fatura antes do vencimento, entre em contato com nosso time de atendimento e defina o
                    quanto deseja pagar. Pagamentos antecipados, com solicitação antes da emissão do boleto de pagamento
                    são emitidos com 5% de desconto.</p>
                <p>Para acesso ao documento completo, com descritivo detalhado dos débitos, entre em contato com nosso
                    time financeiro: <b>financeiro@flexwei.com</b> ou abra um chamado em <b>suporte.flexwei.com</b>
                </p>
            </div>
        </div>
    </div>
    <div class="footer">
        <div class="footer-customer">
            <p><b>FLEXWEI DIGITAL</b></p>
            <p>36.320.921-9/0001-83</p>
            <p>Parque São Vicente, Mauá</p>
            <p>São Paulo - Brasil</p>
        </div>
        <div class="footer-customer right">
            <p>Encargos e Custo Efetivo Total (CET) válidos para o próximo período.</p>
            <p>Autorização.Flexwei: <?= substr($url_token, 0, 32) ?></p>
        </div>
    </div>
    <div style="clear: both"></div>
</div>
<?php
$services = $contractsServices->getServiceListByDocumentKey($contracts->getDocumentKey());
for ($x = 0; $x < count($services); $x++) {
    ?>
    <div class="page">
        <div class="header">
            <div class="header-image">
                <img src="../../../public/images/invoices/invoice-header-smallest.png">
            </div>
            <div class="header-customer" style="margin-right: 115">
                <p><?= $customer->getFullName() ?><span
                            style="font-weight:300;margin-left:3px;"><?= $customer->getDocument() ?></span></p>
                <p>FATURA <span><?= $contractsInvoices->getSmallDueDate() ?></span> EMISSÃO
                    <span><?= $contractsInvoices->getSmallInsertTime() ?></span></p>
            </div>
        </div>
        <div class="section-resume">
            <div class="resume-table-header">
                <div class="header-resume">
                    <p>SERVIÇOS RELACIONADOS <span
                                style="font-weight:300;">(#<?= substr(md5($services[$x]['id_contract_service']), 0, 6) ?>)</span>
                    </p>
                </div>
            </div>
            <div class="resume-table-content">
                <div class="inside">
                    <div class="line" style="height: 5">
                        <div class="f1"
                             style="font-weight: 600;text-transform: uppercase;"><?= $text->utf8($services[$x]['service_name']) ?></div>
                        <div class="f2">
                            R$ <?= $numeric->money(($services[$x]['total_amount'] / $contracts->getInstallments())) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="resume-table-content">
                <div class="inside-terms" style="margin-top: 30">
                    <p style="font-size:1em;text-transform: uppercase;font-weight: 700"><?= $text->utf8($services[$x]['service_title']) ?></p>

                    <div class="inside_content_svdesc">
                        <?= $text->utf8($services[$x]['service_description']) ?>
                    </div>

                    </p>
                </div>
            </div>
        </div>
        <div class="footer">
            <div class="footer-customer">
                <p><b>FLEXWEI DIGITAL</b></p>
                <p>36.320.921-9/0001-83</p>
                <p>Parque São Vicente, Mauá</p>
                <p>São Paulo - Brasil</p>
            </div>
            <div class="footer-customer right">
                <p>Encargos e Custo Efetivo Total (CET) válidos para o próximo período.</p>
                <p>Autorização.Flexwei: <?= substr($url_token, 0, 32) ?></p>
            </div>
        </div>
    </div>
<?php } ?>
</body></html>
