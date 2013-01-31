<?php

/**
 * visanet actions.
 *
 * @package    frontend
 * @subpackage mdEcommercePlugin
 * @author     Gaston Caldeiro
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class visanetActions extends sfActions {

  public function executeIndex(sfWebRequest $request) {
    try {
      sfContext::getInstance()->getConfiguration()->loadHelpers('I18N');
      
      // Procesar y crear la orden
      $this->md_order = Doctrine::getTable('mdOrder')->find($request->getParameter('id'));

      if ($this->md_order && $this->md_order->isOrderValid()) {
        $this->md_visanet = mdVisa::create($this->md_order->getId(), mdVisa::PENDING);
        $array_send = array();
        $mdShipping = $this->md_order->getShippingData();
        
        $Monto = (number_format($this->md_order->getTotal(), 2)*100);
        $CodigoMoneda = sfConfig::get('app_visanet_CURRENCY_CODE');
        $CodigoOperacion = $this->md_visanet->getId();
        $direccionCobranza= $mdShipping->getAddress();
        $ciudadCobranza = $mdShipping->getCity();
        $estadoCobranza = format_country($mdShipping->getCountryCode());
        $paisCobranza = $mdShipping->getCountryCode();
        $codigoPostalCobranza = $mdShipping->getPostcode();
        $telefonoCobranza = $mdShipping->getPhone();
        $correoElectronicoCobranza = $this->md_order->getMdUser()->getEmail();
        $primerNombreTarjetaH = $mdShipping->getFirstname();
        $segundoNombreTarjetaH = $mdShipping->getLastname();

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
        $this->llavePublicaCifrado = file_get_contents(sfConfig::get('sf_data_dir') . '/visa/ALIGNET.TESTING.PHP.CRYPTO.PUBLIC.txt');

        // Llave privada del comercio con la que se generará la firma digital del mensaje XMLREQ.
        // Esta llave privada es generada y almacenada por el comercio.
        $this->llavePrivadaFirma = file_get_contents(sfConfig::get('sf_data_dir') . '/superventas/SUPERVENTAS.FIRMA.PRIVADA.pem');

      } else {
        $this->getUser()->setFlash('error', 'Invalid Order');
        $this->redirect('@homepage');
      }
    } catch (Exception $e) {
      $this->getUser()->setFlash('error', $e->getMessage());
      $this->redirect('@homepage');
    }
  }

  /**
   * Procesa el formulario postPago en redpagos para ingresar el codigo
   * y finalizar el pedido.
   * 
   * @param sfWebRequest $request 
   */
  public function executeProcess(sfWebRequest $request) {
    if ($request->isMethod('POST')) {
      try {
        $this->error = false;
        
        $llavePrivadaCifrado = file_get_contents(sfConfig::get('sf_data_dir') . '/superventas/SUPERVENTAS.CIFRADO.PRIVADA.pem');    
        $llavePublicaFirma = file_get_contents(sfConfig::get('sf_data_dir') . '/visa/ALIGNET.TESTING.PHP.SIGNATURE.PUBLIC.txt');
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

        } else {
          //Puede haber un problema de mala configuración de las llaves, vector de inicializacion o el VPOS no ha enviado valores correctos
          $this->error = 'VISANET RESPONSE ERROR';
          $this->response = $arrayOut;
        }

      } catch (Exception $e) {
        $this->getUser()->setFlash('error', $e->getMessage());
        $this->redirect('@homepage');
      }
    } else {
      $this->getUser()->setFlash('error', 'Invalid Invocation Method');
      $this->redirect('@homepage');
    }
  }

}

