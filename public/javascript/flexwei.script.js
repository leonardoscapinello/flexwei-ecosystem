let module_redir = "./redirect2module/";
let tab_param = "v:";

function gotoPage(module_url, parameter) {
    window.location.href = atob(module_url) + "/" + parameter;
}


function openTab(tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    tabName = tabName.replace(tab_param, "");
    var absoluteUrl = window.location.href.split("#")[0] + "#" + tab_param + tabName;
    history.pushState({
        id: tabName
    }, 'Flexwei', absoluteUrl);
    console.log(window.location.href.split("#")[0]);
    console.log(tabName);
    document.getElementById(tabName).style.display = "block";
    var btn = document.querySelectorAll("[data-nav='" + tabName + "']");
    if (btn !== null && btn !== undefined) {
        for (let i = 0; i < btn.length; i++) {
            btn[i].className += " active";
        }
    }
}

function preloadTab() {
    let uri = window.location.href.split("#");
    if (uri.length > 1) {
        let param = uri[1];
        if (param.substring(0, tab_param.length) === tab_param) {
            openTab(param.substring(0, param.length));
        }
    }
}

window.setTimeout(function () {
    preloadTab();
}, 100);


function clearZipcodeForm() {
    document.getElementById('street').value = ("");
    document.getElementById('neighborhood').value = ("");
    document.getElementById('city').value = ("");
    document.getElementById('state').value = ("");
    document.getElementById('country').value = ("");

    document.getElementById('street').readOnly = false;
    document.getElementById('neighborhood').readOnly = false;
    document.getElementById('city').readOnly = false;
    document.getElementById('state').readOnly = false;
    document.getElementById('country').readOnly = false;
}

function zipcodeCallback(conteudo) {
    if (!("erro" in conteudo)) {
        document.getElementById('street').value = (conteudo.logradouro);
        document.getElementById('neighborhood').value = (conteudo.bairro);
        document.getElementById('city').value = (conteudo.localidade);
        document.getElementById('state').value = (conteudo.uf);
        document.getElementById('country').value = ("Brasil");

        document.getElementById("number").focus();
    } else {
        clearZipcodeForm();
        alert("CEP não encontrado.");
    }
}

function getZipcode(valor) {
    var cep = valor.replace(/\D/g, '');
    document.getElementById('street').readOnly = true;
    document.getElementById('neighborhood').readOnly = true;
    document.getElementById('city').readOnly = true;
    document.getElementById('state').readOnly = true;
    document.getElementById('country').readOnly = true;
    if (cep !== "") {
        var validacep = /^[0-9]{8}$/;
        if (validacep.test(cep)) {
            document.getElementById('street').value = "...";
            document.getElementById('neighborhood').value = "...";
            document.getElementById('city').value = "...";
            document.getElementById('state').value = "...";
            document.getElementById('country').value = "...";
            var script = document.createElement('script');
            script.src = 'https://viacep.com.br/ws/' + cep + '/json/?callback=zipcodeCallback';
            document.body.appendChild(script);
        } else {
            clearZipcodeForm();
            alert("Formato de CEP inválido.");
        }
    } else {
        clearZipcodeForm();
    }
}

$(document).ready(function () {
    $('.date').mask('00/00/0000');
    $('.time').mask('00:00:00');
    $('.date_time').mask('00/00/0000 00:00:00');
    $('.zipcode').mask('00000-000');
    $('.phone').mask('0000-0000');
    $('.phone_with_ddd').mask('(00) 0000-0000');
    $('.phone_us').mask('(000) 000-0000');
    $('.mixed').mask('AAA 000-S0S');
    $('.cpf').mask('000.000.000-00', {reverse: true});
    $('.cnpj').mask('00.000.000/0000-00', {reverse: true});
    $('.money').mask('000.000.000.000.000,00', {reverse: true});
    $('.money2').mask("#.##0,00", {reverse: true});
});

function conf(msg) {
    if (confirm(msg) === false) {
        return false;
    }
    return true;
}


