<?php

class visanetController {

  const AUTHORIZED_CODE = '00';
  
  const DENIED_CODE = '01';
  
  const REJECTED_CODE = '05';
  
  private static $instance = NULL;
  
  public function __construct() {

  }
  
  public static function getInstance() {
    if (is_null(self::$instance)) {
      self::$instance = new visanetController();
    }
    return self::$instance;
  }  
  
  public function process($response){
    //La salida esta  en $arrayOut con todos los parámetros decifrados devueltos por el VPOS
    $resultadoAutorizacion = $response['authorizationResult'];
    $errorMessage = $response['errorMessage'];
    $errorCode = $response['errorCode'];

    $md_visa = mdVisaTable::getInstance()->find($response['purchaseOperationNumber']);

    if(!$md_visa){
      throw new Exception('invalid transaction');
    }

    $md_order = $md_visa->getMdOrder();
    switch ($resultadoAutorizacion){
      case self::AUTHORIZED_CODE:

        // Obtenemos parametros extra
        $codigoAutorizacion = $response['authorizationCode'];
        $planCode = $response['planCode'];
        $planName = $response['planName'];
        $quotaCode = $response['quotaCode'];
        $quotaName = $response['quotaName'];
        $cardType = $response['cardType'];
        //$cardNumber = $response['cardNumber'];
        $ECI = $response['ECI'];
        $VCI = $response['VCI'];
        
        $md_user = $md_order->getMdUser();

        $md_visa->setStatus(mdVisa::PAYED);
        $md_visa->setAuthorizationResult($resultadoAutorizacion);
        $md_visa->setErrorCode($errorCode);
        $md_visa->setErrorMessage($errorMessage);
        $md_visa->setAuthorizationCod($codigoAutorizacion);
        $md_visa->setPlanCode($planCode);
        $md_visa->setPlanName($planName);
        $md_visa->setQuotaCode($quotaCode);
        $md_visa->setQuotaName($quotaName);
        $md_visa->setCardType($cardType);
        //$md_visa->setCardNumber($cardNumber);      
        $md_visa->setECI($ECI);
        $md_visa->setVCI($VCI);
        $md_visa->save();

        $md_order->callToReview(sfConfig::get('app_configuration_MD_PAYED'));

        // Enviamos Email al Cliente
        mdCartController::sendCustomerMail($md_user->getEmail(), $md_order);

        break;
      case self::DENIED_CODE:
          // Obtenemos parametros extra
          $codigoAutorizacion = $response['authorizationCode'];
          $planCode = $response['planCode'];
          $planName = $response['planName'];
          $quotaCode = $response['quotaCode'];
          $quotaName = $response['quotaName'];
          $cardType = $response['cardType'];
          //$cardNumber = $response['cardNumber'];
          $ECI = $response['ECI'];
          $VCI = $response['VCI'];

          $md_visa->setStatus(mdVisa::CANCELED);
          $md_visa->setAuthorizationResult($resultadoAutorizacion);
          $md_visa->setErrorCode($errorCode);
          $md_visa->setErrorMessage($errorMessage);
          
          $md_visa->setPlanCode($planCode);
          $md_visa->setPlanName($planName);
          $md_visa->setQuotaCode($quotaCode);
          $md_visa->setQuotaName($quotaName);
          $md_visa->setCardType($cardType);
          $md_visa->setECI($ECI);
          $md_visa->setVCI($VCI);
          
          $md_visa->save();
          $md_order->callCanceled();
        break;
      case self::REJECTED_CODE:
          $md_visa->setStatus(mdVisa::REJECTED);
          $md_visa->setAuthorizationResult($resultadoAutorizacion);
          $md_visa->setErrorCode($errorCode);
          $md_visa->setErrorMessage($errorMessage);
          $md_visa->save();
          $md_order->callCanceled();
        break;
    }
    return $md_visa;
  }

}
