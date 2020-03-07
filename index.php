<?php
require_once("./src/properties/index.php");
require_once("./src/session/validate.php");
?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $properties->getSiteName() ?></title>
    <?= $stylesheet->inlineCSS(DIRNAME . "../../public/stylesheet/stylesheet.min.css") ?>
</head>
<body>
<div id="wrapper">

    <header>
        <div id="header" class="header">
            <div class="container">
                <div class="row">
                    <div class="col-xl-3 col-lg-3 col-sm-3">
                        <?php if ($account->isCustomer()) { ?>
                            <h1><?= $properties->getSiteName() ?></h1>
                        <?php } else { ?>
                            <h1 class="admin"><?= $properties->getSiteName() ?></h1>
                        <?php } ?>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-sm-6">
                        <nav>
                            <div id="navigation" class="navigation">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-xl-12 col-lg-12 col-sm-12">
                                            <ul>
                                                <li>
                                                    <a href="<?= $modules->getModuleUrlById(1) ?>">resumo</a>
                                                </li>
                                                <li>
                                                    <a href="#">meus produtos</a>
                                                    <div class="submenu">
                                                        <div class="container">
                                                            <div class="row">
                                                                <div class="col-xl-3 col-lg-3 col-sm-12">
                                                                    <ul>
                                                                        <li><h4>DÓMINIOS</h4></li>
                                                                        <li><a href="#">meus domínios</a></li>
                                                                        <li><a href="#">solicitações de registro</a>
                                                                        </li>
                                                                        <li><a href="#">pesquisar</a></li>
                                                                    </ul>
                                                                </div>
                                                                <div class="col-xl-3 col-lg-3 col-sm-12">
                                                                    <ul>
                                                                        <li><h4>HOSPEDAGEM</h4></li>
                                                                        <li><a href="#">meus contratos</a></li>
                                                                        <li><a href="#">planos de hospedagem</a>
                                                                        </li>
                                                                        <li><a href="#">status do serviço</a></li>
                                                                    </ul>
                                                                </div>
                                                                <div class="col-xl-3 col-lg-3 col-sm-12">
                                                                    <ul>
                                                                        <li><h4>DESENVOLVIMENTO</h4></li>
                                                                        <li><a href="#">meus contratos</a></li>
                                                                        <li><a href="#">solicitar orçamento</a></li>
                                                                        <li><a href="#">suporte especializado</a>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                                <div class="col-xl-3 col-lg-3 col-sm-12">
                                                                    <ul>
                                                                        <li><h4>CONCIERGE</h4></li>
                                                                        <li><a href="#">entrar em contato</a></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <a href="#">atendimento</a>
                                                    <div class="submenu">
                                                        <div class="container">
                                                            <div class="row">
                                                                <div class="col-xl-3 col-lg-3 col-sm-12">
                                                                    <ul>
                                                                        <li><h4>SUPORTE TÉCNICO</h4></li>
                                                                        <li><a href="#">registrar chamado</a></li>
                                                                        <li><a href="#">meus chamados</a></li>
                                                                        <li><a href="#">atendimento por chat</a>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                                <div class="col-xl-3 col-lg-3 col-sm-12">
                                                                    <ul>
                                                                        <li><h4>FINANCEIRO</h4></li>
                                                                        <li><a href="#">faturas</a></li>
                                                                        <li><a href="#">dados bancários</a></li>
                                                                        <li><a href="#">registrar chamado</a></li>
                                                                        <li><a href="#">meus chamados</a></li>
                                                                    </ul>
                                                                </div>
                                                                <div class="col-xl-6 col-lg-6 col-sm-12">
                                                                    <img src="<?= $properties->getSiteURL() ?>public/images/marketing-concierge-banner.png"
                                                                         style="border-radius:5px;">
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <a href="<?= $modules->getModuleUrlById(4) ?>">minha conta</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </nav>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-sm-3 right">
                        Olá, <?= $account->getFullName() ?>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <section>
        <div class="heading">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-sm-12">
                        <h3><?= ($modules->getModuleName() !== "" ? $modules->getModuleName() : "Página não encontrada") ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div id="content" class="content">


            <?php
            $content = $modules->load();
            if ($content) {
                require_once($content);
            } else {
                ?>

                <p>A página que você esta tentando acessar não foi encontrada.</p>

            <?php } ?>


        </div>
    </section>

</div>

<link href="https://fonts.googleapis.com/css?family=Gudea:400,400i,700&display=swap" rel="stylesheet">
<script type="text/javascript" src="<?= $properties->getSiteURL() ?>public/javascript/jquery-2.1.0.js"></script>
<script type="text/javascript" src="<?= $properties->getSiteURL() ?>public/javascript/card.js"></script>
<script type="text/javascript" src="<?= $properties->getSiteURL() ?>public/javascript/jquery.mask.js"></script>
<script type="text/javascript" src="<?= $properties->getSiteURL() ?>public/javascript/flexwei.script.js"></script>
</body>
</html>
<?php $database->close(); ?>