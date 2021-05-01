<?php

$mysqli = new mysqli("localhost", "root", "", "itramit_pagos");
$idFactura = $_POST['idFactura'];

if ($mysqli->connect_errno) {
    printf("Falló la conexión: %s\n", $mysqli->connect_error);
    exit();
}

$resultado = $mysqli->query("SELECT * FROM facturas WHERE PK_FACTURA='$idFactura'");
$factura = $resultado->fetch_assoc();

$amount = $factura['I_TOTAL'];
$apiVersion = "2.0";
$country = "ES";
$currency = "EUR";
$description = $factura['T_OBSERVACIONES'];
$merchantId = "10473";
$merchantKey = "295cb1a6133fec798db7d323e54a4c10";
$merchantTransactionId = $factura['S_NUMEROEMITIDO'];
$operationType = "DEBIT";
$paymentSolution = "creditcards";
$transactionId = $factura['S_NUMEROEMITIDO'];
$addressLine1 = $factura['V_TIPVIA'].$factura['D_NOMVIA'];
$addressLine2 = $factura['S_PISO'].$factura['S_ESCALERA'].$factura['S_LETRA'];

$idMunicipio = $factura['FK_MUNICIPIO'];
$resultado = $mysqli->query("SELECT * FROM municipios WHERE PK_MUNICIPIO='$idMunicipio'");
$municipio = $resultado->fetch_assoc();
$city = $municipio['S_MUNICIPIO'];

$firstName = $factura['D_NOMBRE'];
$customerId = $factura['S_NIF'];
$postCode = $factura['S_CODPOST'];
$customerCountry = "ES";

$errorURL = "http://localhost/TPVVirtual/errorURL.php";
$successURL = "http://localhost/TPVVirtual/successURL.php";
$statusURL = "http://localhost/TPVVirtual/statusURL.php";

$data = "amount=" . $amount . "&apiVersion=" . $apiVersion . "&country=" . $country . "&currency=" . $currency .
    "&description=" . $description . "&merchantId=" . $merchantId . "&merchantTransactionId=" . $merchantTransactionId
    ."&operationType=" . $operationType . "&paymentSolution=" . $paymentSolution .
    "&addressLine1=" . $addressLine1 . "&addressLine2=" . $addressLine2 . "&city=" . $city . "&customerId=" . $customerId .
    "&firstname=" . $firstName . "&postCode=" . $postCode . "&customerCountry=" . $customerCountry .
    "&errorURL=" . $errorURL . "&successURL=" . $successURL . "&statusURL=" . $statusURL;

$method = 'aes-256-ecb';
$encrypted = openssl_encrypt($data, $method, $merchantKey);
$integrityCheck = hash("sha256", $data);
$URI = "https://checkout-stg.easypaymentgateway.com/EPGCheckout/rest/online/tokenize";

$myParameters = "encrypted=" . urlencode(utf8_encode($encrypted)) . "&integrityCheck=" . $integrityCheck . "&merchantId=" . $merchantId;
$HtmlResult = "response";

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL,$URI);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,$myParameters);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$HtmlResult = curl_exec ($ch);

/* cerrar la conexión */
$mysqli->close();

//Redireccion al formulario de pago, aquí se debería implementar el envio por SMS/Correo electronico
header("Location: $HtmlResult");


