<?php
require_once("../../properties/index.php");
$url_token = get_request("url_token");
if (!$contractsInvoices->loadByURLToken($url_token)) {
    die;
}
$id_customer = $contractsInvoices->getIdCustomer();
$customer = new Accounts($id_customer);

?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="<?= $properties->getSiteURL() ?>public/stylesheet/invoice-print.css?v=<?= date("dmHis") ?>"
          rel="stylesheet" type="text/css">
    <link href="<?= $properties->getSiteURL() ?>public/fonts/gilroy/Gilroy.css" rel="stylesheet">
    <link href="<?= $properties->getSiteURL() ?>public/stylesheet/container.css" rel="stylesheet">
</head>
<body>


<div class="page">
    <div class="header">
        <img src="<?= $properties->getSiteURL() ?>public/images/invoices/invoice-header.png" class="fullwidth">
    </div>
    <div class="section-welcome">
        <h1 class="pink">
            Olá, <?= $customer->getFirstName() ?>.<br/>
            Esta é a sua fatura
            de <?= $text->lowercase($date->getMonthNameFromDate($contractsInvoices->getDueDate())) ?>, no valor de
            <br>R$ <?= $numeric->money($contractsInvoices->getAmount()) ?>.
        </h1>

        <div class="welcome-main-box">
            <div class="box">
                <img src="<?= $properties->getSiteURL() ?>public/images/invoices/hands.png">
                <p>Nós estamos crescendo e te agradecemos por fazer parte dessa evolução.</p>
            </div>
            <div class="box">
                <img src="<?= $properties->getSiteURL() ?>public/images/invoices/support.png">
                <p>Se você precisar de ajuda, mande-nos um e-mail com sua solicitação: suporte@flexwei.com</p>
            </div>
            <div class="box">
                <img src="<?= $properties->getSiteURL() ?>public/images/invoices/stars.png">
                <p>Queremos garantir uma experiência 5 estrelas, se você tiver alguma sugestão, estamos prontos a te
                    ouvir.</p>
            </div>
        </div>
    </div>
</div>


<div class="page">
    <div class="header">
        <div class="container">
            <div class="row">
                <div class="col-xl-4 col-lg-4 col-sm-4" style="text-align: left">
                    <img src="<?= $properties->getSiteURL() ?>public/images/invoices/invoice-header-smallest.png">
                </div>
                <div class="col-xl-8 col-lg-8 col-sm-8" style="text-align: right">
                    <div class="header-customer">
                        <p><?= $customer->getFullName() ?></p>
                        <p>FATURA <span><?= $contractsInvoices->getSmallDueDate() ?></span>&nbsp;&nbsp;&nbsp;EMISSÃO <span><?= $contractsInvoices->getSmallInsertTime() ?></span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="section-resume">
        <h1 class="pink">
            Olá, <?= $customer->getFirstName() ?>.<br/>
            Esta é a sua fatura
            de <?= $text->lowercase($date->getMonthNameFromDate($contractsInvoices->getDueDate())) ?>, no valor de
            <br>R$ <?= $numeric->money($contractsInvoices->getAmount()) ?>.
        </h1>

        <div class="welcome-main-box">
            <div class="box">
                <img src="<?= $properties->getSiteURL() ?>public/images/invoices/hands.png">
                <p>Nós estamos crescendo e te agradecemos por fazer parte dessa evolução.</p>
            </div>
            <div class="box">
                <img src="<?= $properties->getSiteURL() ?>public/images/invoices/support.png">
                <p>Se você precisar de ajuda, mande-nos um e-mail com sua solicitação: suporte@flexwei.com</p>
            </div>
            <div class="box">
                <img src="<?= $properties->getSiteURL() ?>public/images/invoices/stars.png">
                <p>Queremos garantir uma experiência 5 estrelas, se você tiver alguma sugestão, estamos prontos a te
                    ouvir.</p>
            </div>
        </div>
    </div>
</div>


<div class="page"></div>

</body>
</html>
