<?php // Solicitud de pago - Pag 14.  ?>

<?php require_once dirname(__FILE__).'/../../../lib/Visanet/vpos_plugin_abierto.php'; ?>

<?php
/* * ******************************************************************** */
/* * ***** Uso del Plug-in en PHP para enviar información al V-POS ****** */
/* * ******************************************************************** */

//Todos los parámetros del componente se colocan en un arreglo de cadenas, cuyo campo llave es el nombre del parámetro
$Monto = '1000';
$CodigoMoneda = '840';
$CodigoOperacion = '4';
$direccionCobranza= 'Francisco Simon 1234';
$ciudadCobranza = 'Montevideo';
$estadoCobranza = 'Uruguay';
$paisCobranza = 'UY';
$codigoPostalCobranza = '11800';
$telefonoCobranza = '24093422';
$correoElectronicoCobranza = 'chugas488@gmail.com';
$primerNombreTarjetaH = 'Gaston';
$segundoNombreTarjetaH = 'Caldeiro';

$array_send['acquirerId'] = '16';
$array_send['commerceId'] = '5808';
$array_send['purchaseAmount'] = $Monto;
$array_send['purchaseCurrencyCode'] = $CodigoMoneda;
$array_send['purchaseOperationNumber'] = $CodigoOperacion;
$array_send['billingAddress'] = $direccionCobranza;
$array_send['billingCity'] = $ciudadCobranza;
$array_send['billingState'] = $estadoCobranza;
$array_send['billingCountry'] = $paisCobranza;
$array_send['billingZIP'] = $codigoPostalCobranza;
$array_send['billingPhone'] = $telefonoCobranza;
$array_send['billingEMail'] = $correoElectronicoCobranza;
$array_send['billingFirstName'] = $primerNombreTarjetaH;
$array_send['billingLastName'] = $segundoNombreTarjetaH;
$array_send['language'] = 'SP'; //En español
$array_send['terminalCode'] = 'VBV00203'; //(En caso de que Visanet no les haya asignado ya uno)
$array_send['commerceMallId'] = '1';

//Setear un arreglo de cadenas con los parámetros que serán devueltos por el componente
$array_get['XMLREQ'] = "";
$array_get['DIGITALSIGN'] = "";
$array_get['SESSIONKEY'] = "";

//Vector de inicialización
$VI = '9E8B51CA41AA2FBD';

//Ejemplo de una llave harcodeada en el PHP
//notese el \n son los agregados al final de cada linea, son saltos de linea necesarios para el formato PEM

// Esta llave pública es generada por ALIGNET 
/*$llavePublicaCifrado =
        "-----BEGIN PUBLIC KEY-----\n".
        "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDTJt+hUZiShEKFfs7DShsXCkoq\n".
        "TEjv0SFkTM04qHyHFU90Da8Ep1F0gI2SFpCkLmQtsXKOrLrQTF0100dL/gDQlLt0\n".
        "Ut8kM/PRLEM5thMPqtPq6G1GTjqmcsPzUUL18+tYwN3xFi4XBog4Hdv0ml1SRkVO\n".
        "DRr1jPeilfsiFwiO8wIDAQAB\n".
        "-----END PUBLIC KEY-----";*/

$llavePublicaCifrado = file_get_contents(sfConfig::get('sf_data_dir') . '/visa/ALIGNET.TESTING.PHP.CRYPTO.PUBLIC.txt');

// Llave privada del comercio con la que se generará la firma digital del mensaje XMLREQ. 
// Esta llave privada es generada y almacenada por el comercio.
/*$llavePrivadaFirma =
"-----BEGIN RSA PRIVATE KEY-----\n".
"MIICXQIBAAKBgQCzojUSQ9FlfM3mRv759qMi8FeHa9K4i04ujtgJODgAuDHv3EtH\n".
"Y/wWuE7JDAp6FE198dPKIN7PWr13kplK6G/v0LnN98p3h3/f0tq3yIBDObTGFmJM\n".
"pJjCO435/U7SQ/fDFHNjLXFhEVAzVS1YASmWvXufipguZgN5WUeoSAdaIwIDAQAB\n".
"AoGAcXx9AvkNhYx/mIgp9km3bw9gfRHFowl/bzKXkduOpgW4ps0KUiP8023FeIa+\n".
"57mD65moL/7sRRwSr0RSzFxkupJMmHo2DhZQ2Qo/rqsWeAXzwQZbvUU743BCSYEI\n".
"HT7OfasVUG+mDPRLhUS3pjOvMxDB71UldQd2wvZZUL17LmECQQDiVfmxn1SVrefu\n".
"NP+FX+a/Snuui4q/UAHfLjV6s6oUp05Nd7XfryOJCHtcify/wEnDiIjE4ZDEcMxS\n".
"WeWa7mhPAkEAyy1HNe3AA8Hlhd7FR1+OiHCHh6OwNNCZgY9qRITcBGfy03qg+zaG\n".
"rMG8eJITVthhIhVRPoYaX4pAhXSavddn7QJAG/zVq4kwRHIExAf5sNxzBCSJtsO6\n".
"nH2gPaDRLCMbXQJzRFERRF+73S4XUxIdFvkIJg20G+RoqmHoYiaLpeTlMQJBAILb\n".
"EY8RJecFnW7f8E0spR8I4rEgYp9Rblx8YqPosc+Ap2s/AqlpD8n6KQm6gwwe5khO\n".
"VHohYqD/6NhLJlJ4hm0CQQC9U7ZE0qtkSwFQEDBEVM8QEv4z5zvypXSKG4lOzkID\n".
"siX9/4i4r1vQt9pcw8zRKLFtw7RwWNiosO9TfLe9j9FL\n".
"-----END RSA PRIVATE KEY-----";*/

$llavePrivadaFirma = file_get_contents(sfConfig::get('sf_data_dir') . '/superventas/SUPERVENTAS.FIRMA.PRIVADA.pem');
VPOSSend($array_send, $array_get, $llavePublicaCifrado, $llavePrivadaFirma, $VI);

?>

COMPRAR POR VISA

<br />
<form name="frmSolicitudPago" method="post" action="https://test2.alignetsac.com/VPOS/MM/transactionStart20.do">
  <input type="hidden" name="IDACQUIRER" value="<?php echo sfConfig::get('app_visanet_IDACQUIRER'); ?>">
  <input type="hidden" name="IDCOMMERCE" value="<?php echo sfConfig::get('app_visanet_IDCOMMERCE'); ?>">
  <input type="hidden" name="XMLREQ" value="<?php echo $array_get['XMLREQ']; ?>">
  <input type="hidden" name="DIGITALSIGN" value="<?php echo $array_get['DIGITALSIGN']; ?>">
  <input type="hidden" name="SESSIONKEY" value="<?php echo $array_get['SESSIONKEY']; ?>">
  <input type="submit" value="ENVIAR" />
</form>
