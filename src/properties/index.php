<?php
if (!isset($_SESSION)) {
    session_start();
}

define("DIRNAME", dirname(__FILE__) . "/");

define("PAGARME_API_KEY", "ak_test_N2oaKtLC2OgCnmSvupDW4RhF5jxf7s");
define("PAGARME_CRYPT_KEY", "ek_test_6UVSxBYbuOgDCp1CTJxbP2eXVICWku");

require_once(DIRNAME . "../functions/http_response_code.php");
require_once(DIRNAME . "/../functions/user_agent.php");
require_once(DIRNAME . "../functions/notempty.php");
require_once(DIRNAME . "../functions/XML2Array.php");
require_once(DIRNAME . "../functions/sanitize_output.php");
require_once(DIRNAME . "../functions/translate.php");
require_once(DIRNAME . "../functions/get_request.php");
require_once(DIRNAME . "../functions/is_selected.php");


require_once(DIRNAME . "/../class/lessphp/lessc.inc.php");
require_once(DIRNAME . "/../class/Objects.php");
require_once(DIRNAME . "/../class/Properties.php");
require_once(DIRNAME . "/../class/StyleSheetCompiler.php");
require_once(DIRNAME . "/../class/Database.php");
require_once(DIRNAME . "/../class/Accounts.php");
require_once(DIRNAME . "/../class/AccountSession.php");
require_once(DIRNAME . "/../class/AccountsAddress.php");
require_once(DIRNAME . "/../class/Numeric.php");
require_once(DIRNAME . "/../class/Text.php");
require_once(DIRNAME . "/../class/Date.php");
require_once(DIRNAME . "/../class/Modules.php");
require_once(DIRNAME . "/../class/Token.php");
require_once(DIRNAME . "/../class/Security.php");
require_once(DIRNAME . "/../class/Contracts.php");
require_once(DIRNAME . "/../class/ContractsServices.php");
require_once(DIRNAME . "/../class/ContractsInvoices.php");
require_once(DIRNAME . "/../class/Transactions.php");
require_once(DIRNAME . "/../class/CreditCard.php");


require_once(DIRNAME . "/../vendor/autoload.php");

$objects = new Objects();
$database = new Database();
$numeric = new Numeric();
$text = new Text();
$modules = new Modules();
$session = new AccountSession();
$account = new Accounts();
$address = new AccountsAddress();
$token = new Token();
$security = new Security();
$contracts = new Contracts();
$contractsServices = new ContractsServices();
$contractsInvoices = new ContractsInvoices();
$date = new Date();
$transactions = new Transactions();
$pagarme = new PagarMe\Client(PAGARME_API_KEY);
$less = new lessc();
$properties = new Properties();
$stylesheet = new StyleSheetCompiler($properties->getDevelopment());
$creditCard = new CreditCard();

$less->compileFile(DIRNAME . "../../public/less/stylesheet.less", DIRNAME . "../../public/stylesheet/stylesheet.css");


ob_start("sanitize_output");
