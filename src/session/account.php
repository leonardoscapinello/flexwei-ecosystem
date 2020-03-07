<?php
require_once("../properties/index.php");
if ($session->isLogged()) {
    header("location: " . $modules->getHome());
    die;
}
$username = get_request("u");
if (not_empty($username)) $username = $text->base64_decode($username);
?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <?= $stylesheet->minifyAndPrintInlint(DIRNAME . "../../public/stylesheet/authenticate.css") ?>
</head>
<body>

<div id="authenticate">

    <form id="authenticateForm">
        <div class="login">
            <div class="form">
                <div class="login_title">
                    <img src="./public/images/flexwei-white.png" class="company">
                    <span>Faça Login em sua conta</span>
                </div>
                <div class="login_fields">
                    <div class="login_fields__user">
                        <div class="icon">
                            <img src="./public/images/user_icon_copy.png">
                        </div>
                        <input placeholder="Nome de usuário ou e-mail" type="text" name="username" id="username"
                               autocomplete="off" value="<?= $username ?>" required>
                        <div class="validation">
                            <img src="./public/images/tick.png">
                        </div>
                    </div>
                    <div class="login_fields__password">
                        <div class="icon">
                            <img src="./public/images/lock_icon_copy.png">
                        </div>
                        <input placeholder="Digite sua senha" type="password" name="password" id="password"
                               autocomplete="off" required>
                        <div class="validation">
                            <img src="./public/images/tick.png">
                        </div>
                    </div>
                    <div class="login_fields__submit">
                        <input type="submit" value="Log In">
                        <div class="forgot">
                            <a href="#" onClick="recoveryPassword();">Recuperar Senha</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="result_box success">
                <img src="./public/images/flexwei-white.png" class="company">
                <h2>Seja bem-vindo(a) à Flexwei.</h2>
                <p>Aguarde um instante enquanto preparamos tudo...</p>
                <p style="text-align: center">
                    <img src="./public/images/puff.svg">
                </p>
            </div>
            <div class="result_box failed">
                <img src="./public/images/flexwei-white.png" class="company">
                <h2>Usuário ou Senha incorreto.</h2>
                <p>A combinação de usuário e senha não conferem para que possamos autorizar seu acesso com esse usuário.
                    Tente novamente ou entre em contato com nosso suporte.</p>
                <p style="text-align:center">
                    <button onClick="reverseLogin();return false" style="margin-bottom:15px;" class="active">
                        Tentar
                        Novamente
                    </button>
                    <a href="#" onclick="recoveryPassword();">Recuperar Senha</a>
                </p>
            </div>
            <div class="disclaimer">
                <p>Caso precise de ajude entre em contato com nossa central de ajuda por e-mail
                    <b>suporte@flexwei.com</b>
                </p>
            </div>
        </div>
        <div class="authent">
            <img src="./public/images/puff.svg">
            <p>Autenticando...</p>
        </div>
    </form>


</div>


<script type="text/javascript" src="./public/javascript/jquery-2.1.0.js"></script>
<script src="./public/javascript/jquery-ui.min.js"></script>
<script type="text/javascript">
    $("#authenticateForm").submit(function (e) {
        e.preventDefault();
        $(".login").addClass("test");
        setTimeout(function () {
            $(".login").addClass("testtwo");
        }, 300);
        setTimeout(function () {
            $(".authent").show().animate({right: -320}, {easing: "easeOutQuint", duration: 600, queue: false});
            $(".authent").animate({opacity: 1}, {duration: 200, queue: false}).addClass("visible");
        }, 500);
        setTimeout(function () {
            $(".authent").show().animate({right: 90}, {easing: "easeOutQuint", duration: 600, queue: false});
            $(".authent").animate({opacity: 0}, {duration: 200, queue: false}).addClass("visible");
            $(".login").removeClass("testtwo");
        }, 2500);
        setTimeout(function () {
            $(".login").removeClass("test");
            $(".login div").fadeOut(123);
        }, 2800);


        $.ajax({
            type: "POST",
            url: "./login/createsession",
            data: $("#authenticateForm").serialize(),
            success: function () {
                setTimeout(function () {
                    $(".success").fadeIn(300, function () {
                        setTimeout(function () {
                            window.location.href = "./login/redirect"
                        }, 2000);
                    });
                }, 3200);
            },
            error: function () {
                setTimeout(function () {
                    $(".failed").fadeIn();
                    $(".authent").removeClass("visible");
                    $(".authent").hide();
                    setTimeout(function () {
                        reverseLogin();
                    }, 2000);
                }, 3200);
            }
        });


    });

    function reverseLogin() {
        $(".login").addClass("test");
        setTimeout(function () {
            $(".result_box").fadeOut(300, function () {
                $(".login").removeClass("test");
                $(".login .form,.login .form div").delay(400).fadeIn(300);
                $(".disclaimer").delay(400).fadeIn(300);
            });
        }, 1000);
        window.history.pushState('Login Flexwei', 'Login Flexwei', "./login?silent&u=" + btoa($("#username").val()));
    }

    $("input[type='text'],input[type='password']").focus(function () {
        $(this).prev().animate({"opacity": "1"}, 200)
    });
    $("input[type='text'],input[type='password']").blur(function () {
        $(this).prev().animate({"opacity": ".5"}, 200)
    });

    $("input[type='text'],input[type='password']").keyup(function () {
        if (!$(this).val() === "") {
            $(this).next().animate({"opacity": "1", "right": "30"}, 200)
        } else {
            $(this).next().animate({"opacity": "0", "right": "20"}, 200)
        }
    });

    var open = 0;
    $(".tab").click(function () {
        $(this).fadeOut(200, function () {
            $(this).parent().animate({"left": "0"})
        });
    });

    function redirectBackToLogin() {
        window.location.href = "./login?u=" + btoa($("#username").val());
    }

    function recoveryPassword() {
        window.location.href = "./recovery?u=" + btoa($("#username").val());
    }

</script>
</body>
</html>