<?php

$action = get_request("action");

if ($action === "address") {
    $address->setZipcode(get_request("zipcode"));
    $address->setStreet(get_request("street"));
    $address->setNumber(get_request("number"));
    $address->setNeighborhood(get_request("neighborhood"));
    $address->setCity(get_request("city"));
    $address->setState(get_request("state"));
    $address->setCountry(get_request("country"));
    $address->setComplement(get_request("complement"));
    if ($address->save()) {
        header("location: " . $modules->getModuleUrlById(4) . '?s=address#v:address');
        die;
    }
}

if ($action === "password") {
    $password = get_request("password");
    $confirm = get_request("confirm");
    if ($account->resetPassword($password, $confirm)) {
        header("location: " . $modules->getModuleUrlById(4) . '?s=pw#v:personal');
        die;
    } else {
        header("location: " . $modules->getModuleUrlById(4) . '?s=erpw#v:personal');
        die;
    }
}

if ($action === "cc") {
    $number = get_request("ccnumber");
    $holder = get_request("ccname");
    $expire_month = get_request("ccmonth");
    $expire_year = get_request("ccyear");
    $cvv = get_request("cccvv");
    $register = $accountsCards->register($number, $holder, $expire_month, $expire_year, $cvv);
    if ($register[0]) {
        header("location: " . $modules->getModuleUrlById(4) . '?s=cc#v:finance');
        die;
    } else {
        header("location: " . $modules->getModuleUrlById(4) . '?s=ercc&m=' . $text->base64_encode($register[1]) . '#v:finance');
        die;
    }
}


?>


<!-- TABS LIST -->
<div class="tab-header">
    <div class="container">
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-sm-12">
                <div class="tab">
                    <button data-nav="personal" class="tablinks active" onclick="openTab('personal')">Dados Pessoais
                    </button>
                    <button data-nav="address" class="tablinks" onclick="openTab('address')">Endereço</button>
                    <button data-nav="finance" class="tablinks" onclick="openTab('finance')">Financeiro</button>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="container">
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-sm-12">


            <div id="personal" class="tabcontent" style="display: block;">

                <?php if (get_request("s") === "erpw") { ?>
                    <div class="container">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-sm-12">
                                <div class="alert alert-danger fade show" role="alert">
                                    <div class="alert-icon"><i class="la la-lock"></i></div>
                                    <div class="alert-text">
                                        Não foi possível atualizar sua senha. Verifique se sua senha contém ao menos 5
                                        caracteres.
                                    </div>
                                    <div class="alert-close">
                                        <button type="button" class="alert-close close" data-dismiss="alert"
                                                aria-label="Close">
                                            <span aria-hidden="true"><i class="la la-close"></i></span>
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php } ?>

                <div class="content-block">
                    <h5>Informações Pessoais</h5>
                    <div class="container">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-sm-4">
                                <div class="input-group">
                                    <label for="">Nome</label>
                                    <input type="text"
                                           value="<?= $account->getFirstName() ?>" readonly/>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-sm-4">
                                <div class="input-group">
                                    <label for="">Sobrenome</label>
                                    <input type="text"
                                           value="<?= $account->getLastName() ?>" readonly/>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-sm-4">
                                <div class="input-group">
                                    <label for="">Documento Pessoal</label>
                                    <input type="text"
                                           value="<?= $account->getMaskedDocument() ?>" readonly/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-sm-4">
                                <div class="input-group">
                                    <label for="">Endereço de E-mail</label>
                                    <input type="text"
                                           value="<?= $account->getEmail() ?>" readonly/>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-sm-4">
                                <div class="input-group">
                                    <label for="">Telefone de Contato</label>
                                    <input type="text"
                                           value="<?= $text->mask($account->getPhoneNumber(), "+## (##) #.####-####") ?>"
                                           readonly/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h5>Alterar Senha</h5>

                    <form action="" method="POST" id="pw" name="pw"
                          onsubmit="return conf('Você tem certeza que deseja alterar sua senha? Ao alterar sua senha você será automaticamente desconectado de todos os dispositivos por razões de segurança (incluindo este).');">
                        <input type="hidden" value="password" name="action">
                        <div class="container">
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-sm-4">
                                    <div class="input-group">
                                        <label for="">Nova Senha</label>
                                        <input type="password" name="password" required>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-sm-4">
                                    <div class="input-group">
                                        <label for="">Confirmar nova senha</label>
                                        <input type="password" name="confirm" required/>
                                    </div>
                                </div>
                            </div>
                            <div class="container">
                                <div class="row">
                                    <div class="offset-4"></div>
                                    <div class="col-xl-4 col-lg-4 col-sm-4" align="center">
                                        <button class="btn" type="submit" form="pw">Alterar Senha</button>
                                    </div>
                                    <div class="offset-4"></div>
                                </div>
                            </div>
                        </div>
                    </form>


                </div>

            </div>
            <div id="address" class="tabcontent">
                <?php if (!$address->isAddressRegistered()) { ?>
                    <div class="container">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-sm-12">
                                <div class="alert alert-secondary fade show" role="alert">
                                    <div class="alert-icon"><i class="la la-home"></i></div>
                                    <div class="alert-text">
                                        Por favor, preencha os campos abaixo para atualizar seu endereço pessoal ou
                                        comercial. O preenchimento é obrigatório para que possamos seguir com nosso
                                        atendimento.
                                    </div>
                                    <div class="alert-close">
                                        <button type="button" class="alert-close close" data-dismiss="alert"
                                                aria-label="Close">
                                            <span aria-hidden="true"><i class="la la-close"></i></span>
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php } else if (get_request("s") === "address") { ?>
                    <div class="container">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-sm-12">
                                <div class="alert alert-success fade show" role="alert">
                                    <div class="alert-icon"><i class="la la-check-circle"></i></div>
                                    <div class="alert-text">
                                        As alterações em seu endereço foram salvas com sucesso. Obrigado por manter seu
                                        perfil atualizado.
                                    </div>
                                    <div class="alert-close">
                                        <button type="button" class="alert-close close" data-dismiss="alert"
                                                aria-label="Close">
                                            <span aria-hidden="true"><i class="la la-close"></i></span>
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php } ?>

                <div class="content-block">
                    <h5>Endereço Pessoal/Comercial</h5>
                    <form action="" method="POST">
                        <input type="hidden" value="address" name="action">
                        <div class="container">
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-sm-12">
                                    <div class="input-group">
                                        <label for="">Código Postal (CEP)</label>
                                        <input type="text" id="zipcode" name="zipcode" onblur="getZipcode(this.value);"
                                               class="zipcode"
                                               value="<?= $address->getZipcode() ?>" required/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-10 col-lg-10 col-sm-12">
                                    <div class="input-group">
                                        <label for="">Endereço</label>
                                        <input type="text" id="street" name="street"
                                               value="<?= $address->getStreet() ?>" required/>
                                    </div>
                                </div>
                                <div class="col-xl-2 col-lg-2 col-sm-12">
                                    <div class="input-group">
                                        <label for="">Número</label>
                                        <input type="text" id="number" name="number"
                                               value="<?= $address->getNumber() ?>" required/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-sm-4">
                                    <div class="input-group">
                                        <label for="">Bairro</label>
                                        <input type="text" id="neighborhood" name="neighborhood"
                                               value="<?= $address->getNeighborhood() ?>" required/>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-sm-4">
                                    <div class="input-group">
                                        <label for="">Cidade</label>
                                        <input type="text" id="city" name="city"
                                               value="<?= $address->getCity() ?>" required/>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-sm-4">
                                    <div class="input-group">
                                        <label for="">Estado</label>
                                        <input type="text" id="state" name="state"
                                               value="<?= $address->getCountry() ?>" required/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-xl-4 col-lg-4 col-sm-4">
                                    <div class="input-group">
                                        <label for="">País</label>
                                        <input type="text" id="country" name="country"
                                               value="<?= $address->getCountry() ?>" required/>
                                    </div>
                                </div>
                                <div class="col-xl-8 col-lg-8 col-sm-12">
                                    <div class="input-group">
                                        <label for="">Complemento</label>
                                        <input type="text" id="complement" name="complement"
                                               value="<?= $address->getComplement() ?>"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="offset-4"></div>
                                <div class="col-xl-4 col-lg-4 col-sm-4" align="center">
                                    <button class="btn">Atualizar Endereço</button>
                                </div>
                                <div class="offset-4"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


            <div id="finance" class="tabcontent">


                <?php if (!$address->isAddressRegistered()) { ?>
                    <div class="alert alert-danger fade show" role="alert">
                        <div class="alert-icon"><i class="la la-home"></i></div>
                        <div class="alert-text">
                            Precisamos que você atualize seu endereço para que possamos aprimorar
                            nosso atendimento. <a class="tablinks" onclick="openTab('address');">Atualizar agora</a>
                        </div>
                        <div class="alert-close">
                            <button type="button" class="alert-close close" data-dismiss="alert"
                                    aria-label="Close">
                                <span aria-hidden="true"><i class="la la-close"></i></span>
                            </button>
                        </div>
                    </div>
                <?php } ?>

                <div class="container">
                    <div class="row">
                        <div class="col-xl-8 col-lg-8 col-sm-12">


                            <?php if (get_request("s") === "cc") { ?>
                                <div class="alert alert-success fade show" role="alert">
                                    <div class="alert-icon"><i class="fal fa-credit-card-blank"></i></div>
                                    <div class="alert-text">
                                        Parabéns! Um novo cartão de crédito foi cadastrado com sucesso.
                                    </div>
                                    <div class="alert-close">
                                        <button type="button" class="alert-close close" data-dismiss="alert"
                                                aria-label="Close">
                                            <span aria-hidden="true"><i class="la la-close"></i></span>
                                        </button>
                                    </div>
                                </div>
                            <?php } else if (get_request("s") === "ercc") { ?>
                                <div class="alert alert-danger fade show" role="alert">
                                    <div class="alert-icon"><i class="fal fa-exclamation-triangle"></i></div>
                                    <div class="alert-text">
                                        <?php if (get_request("m") === null) { ?>
                                            Oops! Não foi possível cadastrar seu cartão de crédito, verifique as informações fornecidas e tente novamente.
                                        <?php } else { ?>
                                            Encontramos um problema: <br>
                                            <?= $text->bold($text->base64_decode(get_request("m"))) ?>
                                        <?php } ?>
                                    </div>
                                    <div class="alert-close">
                                        <button type="button" class="alert-close close" data-dismiss="alert"
                                                aria-label="Close">
                                            <span aria-hidden="true"><i class="la la-close"></i></span>
                                        </button>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="content-block">
                                <h5 style="padding: 10px 0;">Meus Cartões de Crédito</h5>


                                <?php

                                $cards = $accountsCards->list();
                                for ($i = 0; $i < count($cards); $i++) {

                                    $id_account_card = $cards[$i]['id_account_card'];
                                    $brand = $cards[$i]['brand'];
                                    $last_digits = $cards[$i]['last_digits'];
                                    $is_valid = $cards[$i]['is_valid'];
                                    $is_default = $cards[$i]['is_default'];

                                    ?>

                                    <div class="cards_inner__card <?= $is_valid === "Y" ? "" : "disabled" ?> <?= $security->decrypt($brand) ?>"
                                         onClick="gotoPage('<?= $modules->getEncodedModuleUrlById(5) ?>', '<?= md5($id_account_card) ?>');">
                                        <div class='logo'></div>
                                        <div class="card_digits">**** <?= $security->decrypt($last_digits) ?></div>
                                        <?= $is_default === "Y" ? "<div class=\"default_card\"><i class=\"fa fa-star\"></i></div>" : "" ?>
                                        <div class="remove_card"><i class="fa fa-eye"></i></div>
                                    </div>

                                <?php } ?>


                            </div>

                        </div>
                        <div class="col-xl-4 col-lg-4 col-sm-12">
                            <!-- 200x114-->
                            <form method="GET">
                                <input type="hidden" value="cc" name="action">
                                <div class="content-block">
                                    <h5 style="padding: 10px 15px;">Cadastrar Novo Cartão</h5>
                                    <div class="row">
                                        <div class="col-xl-12 col-lg-12 col-sm-12">
                                            <div class="input-group">

                                                <label class="todo">
                                                    <input class="todo__state" type="checkbox"
                                                           onchange="setUsername(this, 'ccname')"/>
                                                    <?= SVG_CHECKBOX ?>
                                                    <div class="todo__text">Esse cartão de crédito é meu.</div>
                                                </label>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="ccname__main">
                                        <div class="col-xl-12 col-lg-12 col-sm-12">
                                            <div class="input-group">
                                                <label for="ccname">Nome no Cartão</label>
                                                <input type="text" id="ccname" name="ccname"
                                                       data-fullname="<?= $text->base64_encode($account->getFullName()) ?>"
                                                       required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-12 col-lg-12 col-sm-12">
                                            <div class="input-group">
                                                <label for="ccnumber">Número do Cartão de Crédito</label>
                                                <input type="text" id="ccnumber" name="ccnumber"
                                                       onkeyup="onlyNumbers(this);return false"
                                                       onblur="onlyNumbers(this);return false"
                                                       required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-3 col-lg-3 col-sm-12">
                                            <div class="input-group">
                                                <label for="ccmonth">Mês</label>
                                                <select id="ccmonth" name="ccmonth">
                                                    <option value="01">01</option>
                                                    <option value="02">02</option>
                                                    <option value="03">03</option>
                                                    <option value="04">04</option>
                                                    <option value="05">05</option>
                                                    <option value="06">06</option>
                                                    <option value="07">07</option>
                                                    <option value="08">08</option>
                                                    <option value="09">09</option>
                                                    <option value="10">10</option>
                                                    <option value="11">11</option>
                                                    <option value="12">12</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-lg-3 col-sm-12">
                                            <div class="input-group">
                                                <label for="ccyear">Ano</label>
                                                <select id="ccyear" name="ccyear">
                                                    <?php for ($i = (intval(substr(date("Y"), 2, 2)));
                                                               $i < (intval(substr(date("Y"), 2, 2)) + 16);
                                                               $i++) { ?>
                                                        <option value="<?= $numeric->zeroFill($i, 2) ?>"><?= $numeric->zeroFill($i, 2) ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-sm-12">
                                            <div class="input-group">
                                                <label for="cccvv">CVV</label>
                                                <input type="text" id="cccvv" name="cccvv"
                                                       onkeyup="onlyNumbers(this);return false"
                                                       onblur="onlyNumbers(this);return false"
                                                       maxlength="5" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-12 col-lg-12 col-sm-4" align="center">
                                            <button class="btn">Cadastrar Cartão de Crédito</button>
                                            <div class="allowed_cards">
                                                <i class="fab fa-cc-visa"></i>
                                                <i class="fab fa-cc-mastercard"></i>
                                                <i class="fab fa-cc-amex"></i>
                                                <i class="fab fa-cc-diners-club"></i>
                                            </div>
                                            <p class="mute small-text"><i class="fa fa-lock"></i>&nbsp;Ambiente seguro.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>


        </div>
    </div>
</div>