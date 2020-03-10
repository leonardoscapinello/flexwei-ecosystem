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


function onlyNumbers(field) {
    field.value = field.value.replace(/[^0-9.]/g, '');
    field.value = field.value.replace(/(\..*)\./g, '$1');
}

function setUsername(checkbox, id_field) {
    let field = document.getElementById(id_field);
    let main = document.getElementById(id_field + "__main");
    if (checkbox !== null && checkbox !== undefined) {
        if (field !== null && field !== undefined) {
            if (checkbox.checked === true) {
                field.value = atob(field.dataset.fullname);
                field.readOnly = true;
                if (main !== undefined && main !== null) main.style.display = "none";
            } else {
                field.value = "";
                field.readOnly = false;
                if (main !== undefined && main !== null) main.style.display = "block";
            }
        }
    }
}

function viewCard(id) {

}


document.querySelectorAll('.download-btn').forEach(button => {

    let duration = 5000,
        svg = button.querySelector('svg'),
        svgPath = new Proxy({
            y: null,
            smoothing: null
        }, {
            set(target, key, value) {
                target[key] = value;
                if (target.y !== null && target.smoothing !== null) {
                    svg.innerHTML = getPath(target.y, target.smoothing, null);
                }
                return true;
            },
            get(target, key) {
                return target[key];
            }
        });

    button.style.setProperty('--duration', duration);

    svgPath.y = 20;
    svgPath.smoothing = 0;

    button.addEventListener('click', e => {

        e.preventDefault();

        if (!button.classList.contains('loading')) {

            button.classList.add('loading');

            gsap.to(svgPath, {
                smoothing: .3,
                duration: duration * .065 / 1000
            });

            gsap.to(svgPath, {
                y: 12,
                duration: duration * .265 / 1000,
                delay: duration * .065 / 1000,
                ease: Elastic.easeOut.config(1.12, .4)
            });

            setTimeout(() => {
                svg.innerHTML = getPath(0, 0, [
                    [3, 14],
                    [8, 19],
                    [21, 6]
                ]);
            }, duration / 2);

        }

    });

});

function getPoint(point, i, a, smoothing) {
    let cp = (current, previous, next, reverse) => {
            let p = previous || current,
                n = next || current,
                o = {
                    length: Math.sqrt(Math.pow(n[0] - p[0], 2) + Math.pow(n[1] - p[1], 2)),
                    angle: Math.atan2(n[1] - p[1], n[0] - p[0])
                },
                angle = o.angle + (reverse ? Math.PI : 0),
                length = o.length * smoothing;
            return [current[0] + Math.cos(angle) * length, current[1] + Math.sin(angle) * length];
        },
        cps = cp(a[i - 1], a[i - 2], point, false),
        cpe = cp(point, a[i - 1], a[i + 1], true);
    return `C ${cps[0]},${cps[1]} ${cpe[0]},${cpe[1]} ${point[0]},${point[1]}`;
}

function getPath(update, smoothing, pointsNew) {
    let points = pointsNew ? pointsNew : [
            [4, 12],
            [12, update],
            [20, 12]
        ],
        d = points.reduce((acc, point, i, a) => i === 0 ? `M ${point[0]},${point[1]}` : `${acc} ${getPoint(point, i, a, smoothing)}`, '');
    return `<path d="${d}" />`;
}
