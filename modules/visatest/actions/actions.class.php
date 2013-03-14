<?php
require_once dirname(__FILE__).'/../../../lib/Visanet/vpos_plugin_abierto.php';

/**
 * visatest actions.
 *
 * @package    frontend
 * @subpackage mdEcommercePlugin
 * @author     Gaston Caldeiro
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class visatestActions extends sfActions
{
  public function executeTest(sfWebRequest $request) {
    $this->cart = mdCartController::getInstance()->init();
    if(is_null($this->cart)){
      $this->redirect('@homepage');
    }
  }
  
  public function executeIndex(sfWebRequest $request) {
    $Monto = (number_format(10.23,2)*100);
    $CodigoMoneda = sfConfig::get('app_visanet_CURRENCY_CODE');
    $CodigoOperacion = rand(0, 1000);
    $direccionCobranza= 'Francisco Simon 1234';
    $ciudadCobranza = 'Montevideo';
    $estadoCobranza = 'Uruguay';
    $paisCobranza = 'UY';
    $codigoPostalCobranza = '11800';
    $telefonoCobranza = '24093422';
    $correoElectronicoCobranza = 'chugas488@gmail.com';
    $primerNombreTarjetaH = 'Gaston';
    $segundoNombreTarjetaH = 'Caldeiro';
    $array_send = array();
    
    $array_send['acquirerId'] = sfConfig::get('app_visanet_IDACQUIRER');
    $array_send['commerceId'] = sfConfig::get('app_visanet_IDCOMMERCE');
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
    $array_send['terminalCode'] = sfConfig::get('app_visanet_CODIGO_TERMINAL'); //(En caso de que Visanet no les haya asignado ya uno)
    $array_send['commerceMallId'] = '1';

    $this->array_send = $array_send;

    //Ejemplo de una llave harcodeada en el PHP
    //notese el \n son los agregados al final de cada linea, son saltos de linea necesarios para el formato PEM

    // Esta llave pública es generada por ALIGNET
    $this->llavePublicaCifrado = file_get_contents(sfConfig::get('sf_data_dir') . '/visa/' . sfConfig::get('app_visanet_ENVIRONMENT') . '/ALIGNET.TESTING.PHP.CRYPTO.PUBLIC.txt');

    // Llave privada del comercio con la que se generará la firma digital del mensaje XMLREQ.
    // Esta llave privada es generada y almacenada por el comercio.
    $this->llavePrivadaFirma = file_get_contents(sfConfig::get('sf_data_dir') . '/superventas/' . sfConfig::get('app_visanet_ENVIRONMENT') . '/SUPERVENTAS.FIRMA.PRIVADA.pem');
  }  
  
  public function executePhpinfo(sfWebRequest $request) 
  {

    $this->setLayout(false);
  } 
  
  public function executeProcess(sfWebRequest $request) 
  {
    /*$arrayOut = array(
      "acquirerId"=> "16",
      "commerceId"=>"5808",
      "purchaseAmount"=> "3150",
      "purchaseCurrencyCode"=> "840",
      "purchaseOperationNumber"=> "9",
      "purchaseIPAddress"=> "190.134.111.55",
      "products"=> "",
      "language"=> "SP",
      "billingFirstName"=> "Gaston",
      "billingLastName"=> "Caldeiro",
      "billingAddress"=> "Nueva Palmira 2016",
      "billingCity"=> "Montevideo",
      "billingState"=> "Uruguay",
      "billingCountry"=>"UY",
      "billingPhone"=> "24086211",
      "billingEMail"=> "chugas488@gmail.com",
      "terminalCode"=> "VBV00203",
      "errorCode"=> "2300",
      "errorMessage"=>"User Cancelled in PASS 1",
      "authorizationResult"=> "05"
    );*/

    if ($request->isMethod('POST')) {
      try {
        $this->error = false;
        
        $llavePrivadaCifrado = file_get_contents(sfConfig::get('sf_data_dir') . '/superventas/' . sfConfig::get('app_visanet_ENVIRONMENT') . '/SUPERVENTAS.CIFRADO.PRIVADA.pem');    
        $llavePublicaFirma = file_get_contents(sfConfig::get('sf_data_dir') . '/visa/' . sfConfig::get('app_visanet_ENVIRONMENT') . '/ALIGNET.TESTING.PHP.SIGNATURE.PUBLIC.txt');
        $VI = sfConfig::get('app_visanet_VECTOR');

        $arrayIn = array();
        $arrayIn['IDACQUIRER'] = $request->getParameter('IDACQUIRER');
        $arrayIn['IDCOMMERCE'] = $request->getParameter('IDCOMMERCE');
        $arrayIn['XMLRES'] = $request->getParameter('XMLRES');
        $arrayIn['DIGITALSIGN'] = $request->getParameter('DIGITALSIGN');
        $arrayIn['SESSIONKEY'] = $request->getParameter('SESSIONKEY');
        $arrayOut = '';

        if (VPOSResponse($arrayIn, $arrayOut, $llavePublicaFirma, $llavePrivadaCifrado, $VI)) {
          // Procesar respuesta
          $this->md_visa = visanetController::getInstance()->process($arrayOut);
          $this->md_order = $this->md_visa->getMdOrder();
          $this->data = $arrayOut;
          
          if( $arrayOut['authorizationResult'] != visanetController::AUTHORIZED_CODE){
            $this->getUser()->setFlash('error', 'Transaccion Cancelada!');
            $this->redirect('@homepage');
          }          

        } else {
          //Puede haber un problema de mala configuración de las llaves, vector de inicializacion o el VPOS no ha enviado valores correctos
          $this->error = 'VISANET RESPONSE ERROR';
          $this->data = $arrayOut;
          $this->getUser()->setFlash('error', 'Error: Ha habido un problema al procesar la respuesta de VISANET y la transaccion no se ha podido realizar. Intente nuevamente!');
          $this->redirect('@homepage');
        }

      } catch (Exception $e) {
        $this->getUser()->setFlash('error', $e->getMessage());
        $this->redirect('@homepage');
      }
    } else {
      $this->getUser()->setFlash('error', 'Invalid Invocation Method');
      $this->redirect('@homepage');
    }
    
    
    /*$llavePrivadaCifrado = "-----BEGIN RSA PRIVATE KEY-----\n".
    "MIICXgIBAAKBgQDNZe51wdh9QWZU2/cxxaTCN/3cNWunbOSFz2JMPhbzjrtekopZ\n".
    "YorZ0Z/PviJ7RDOfo8J5tSwIpoacRNjlhMsDu31cNwlCWbj8VRJUhPQlr+TT0Hdq\n".
    "bb84ZmW+DGD7uScJ1HO+n4nbkJenzAEsqK6x5F6Mqp/M62zBdV7azcvo6QIDAQAB\n".
    "AoGAYlm9nitM02U+b8HIEtAVvV38M7ZsrwWoQx5zbhiI+uTMeVNn5bcWz4fdoybi\n".
    "8e1NXtKWK5sB076RVEBvLy+v2WRawkeB+pa4a6IbmipkCUxycYvmo5h0uDE2247S\n".
    "FdYUo4J8MGUuMa9pDIBU9vJXw2FaGr0ajR9z49yfgjftlJECQQD4nMxn4PWnw+TK\n".
    "aJ9Aydf7BOo1S3FTxogZVUwb2lbfcwCqh10AqB6TKOdMhff2AZltmZpIispZpPdt\n".
    "amThjMdVAkEA04Bmi3yA7pghg6t1s2MXsJX0NV55VUrbX/DkfnmBKJ4zYR8bhplv\n".
    "zdeYxDDN7n3U3hFPrqYaVUYeTAddEhNzRQJBAMcAY9IGzTxj3sBybH9k0gB5V6wf\n".
    "XnBrq0dz8n8dD4q/OFpIDhbXe9nZ1QN4/RmrABAt8sR8bCrDlNa9YlD06h0CQQC7\n".
    "6fUyMWiMlHYSeqCUxZIivti/IjVDZsMKtwkMpf/vir+zpuPZ7zG6/bcpeQM+xmX+\n".
    "9/qH/eSfpzD78/7pNIbRAkEAonzlXQW6OjFURZIkjo5xdBsSUGTtamaGnZrfXl2h\n".
    "LkZHs83ABBCxL6qoWkK1/OOyl413vnnj1g0WOMKDhshbxA==\n".
    "-----END RSA PRIVATE KEY-----";*/

    /*$llavePublicaFirma = "-----BEGIN PUBLIC KEY-----\n".
    "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCvJS8zLPeePN+fbJeIvp/jjvLW\n".
    "Aedyx8UcfS1eM/a+Vv2yHTxCLy79dEIygDVE6CTKbP1eqwsxRg2Z/dI+/e14WDRs\n".
    "g0QzDdjVFIuXLKJ0zIgDw6kQd1ovbqpdTn4wnnvwUCNpBASitdjpTcNTKONfXMtH\n".
    "pIs4aIDXarTYJGWlyQIDAQAB\n".
    "-----END PUBLIC KEY-----";*/

    /*$llavePrivadaCifrado = file_get_contents(sfConfig::get('sf_data_dir') . '/superventas/' . sfConfig::get('app_visanet_ENVIRONMENT') . '/SUPERVENTAS.CIFRADO.PRIVADA.pem');    
    $llavePublicaFirma = file_get_contents(sfConfig::get('sf_data_dir') . '/visa/' . sfConfig::get('app_visanet_ENVIRONMENT') . '/ALIGNET.TESTING.PHP.SIGNATURE.PUBLIC.txt');

    $arrayIn = array();
    $arrayIn['IDACQUIRER'] = $request->getParameter('IDACQUIRER');
    $arrayIn['IDCOMMERCE'] = $request->getParameter('IDCOMMERCE');
    $arrayIn['XMLRES'] = $request->getParameter('XMLRES');
    $arrayIn['DIGITALSIGN'] = $request->getParameter('DIGITALSIGN');
    $arrayIn['SESSIONKEY'] = $request->getParameter('SESSIONKEY');
    
    echo 'IDACQUIRER ' . $arrayIn['IDACQUIRER'] . '<br />';
    echo 'IDCOMMERCE ' . $arrayIn['IDCOMMERCE'] . '<br />';
    echo 'XMLRES ' . $arrayIn['XMLRES'] . '<br />';
    echo 'DIGITALSIGN ' . $arrayIn['DIGITALSIGN'] . '<br />';
    echo 'SESSIONKEY ' . $arrayIn['SESSIONKEY'] . '<br />';    

    $arrayOut = '';

    $VI = sfConfig::get('app_visanet_VECTOR');

    if (VPOSResponse($arrayIn, $arrayOut, $llavePublicaFirma, $llavePrivadaCifrado, $VI)) {
      var_dump($arrayOut);
      while (list($key, $val) = each($arrayOut)) {
        echo "<br> $key => " . $val;
      }
    } else {
      echo "<br> Respuesta Inv&acute;lida";
    }    
    
    die();   */    
  }
}

