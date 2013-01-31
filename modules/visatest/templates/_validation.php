<?php require_once dirname(__FILE__) . '/../../../lib/Visanet/vpos_plugin_abierto.php'; ?>
<?php VPOSSend($array_send, $array_get, $llavePublicaCifrado, $llavePrivadaFirma, sfConfig::get('app_visanet_VECTOR')); ?>

<h5>Seleccionar plan de pagos:</h5>

<div class="login-carrito">
  <div class="giro-brou">
    <span class="span-pagos">
      El total a pagar con su tarjeta de crédito VISA es de: <?php echo $md_order->getDisplayTotal(); ?>, 
      usted debe hacer click seleccionado la forma de pago que desea y se le redirigirá a la pagina de Visanet donde usted 
      colocara su tarjeta de crédito en un sitio 100% autorizado por Visa
    </span>
    <div class="clear"></div>
    <div class="pagos-visa">
      <form name="frmSolicitudPago" method="post" action="<?php echo sfConfig::get('app_visanet_URL'); ?>">
        <input type="hidden" name="IDACQUIRER" value="<?php echo sfConfig::get('app_visanet_IDACQUIRER'); ?>">
        <input type="hidden" name="IDCOMMERCE" value="<?php echo sfConfig::get('app_visanet_IDCOMMERCE'); ?>">
        <input type="hidden" name="XMLREQ" value="<?php echo $array_get['XMLREQ']; ?>">
        <input type="hidden" name="DIGITALSIGN" value="<?php echo $array_get['DIGITALSIGN']; ?>">
        <input type="hidden" name="SESSIONKEY" value="<?php echo $array_get['SESSIONKEY']; ?>">
        <div style="float: left; text-align: center;">
          <img src="/images/site/visa-1.gif" /><br />
          <input type="radio" value="1" name="cuotas" checked="" />
        </div>
        <div style="float: left; text-align: center;">
          <img src="/images/site/visa-2.gif" /><br />
          <input type="radio" value="2" name="cuotas" />          
        </div>
        <div style="float: left; text-align: center;">
          <img src="/images/site/visa-3.gif" /><br />          
          <input type="radio" value="3" name="cuotas" />
        </div>
        <div style="float: left; text-align: center;">
          <img src="/images/site/visa-4.gif" /><br />    
          <input type="radio" value="4" name="cuotas" />
        </div>
        <div style="float: left; text-align: center;">
          <img src="/images/site/visa-5.gif" /><br />    
          <input type="radio" value="5" name="cuotas" />
        </div>
        <div style="float: left; text-align: center;">
          <img src="/images/site/visa-6.gif" /><br />    
          <input type="radio" value="6" name="cuotas" />
        </div>
        <div style="clear: both"></div>
        <input class="float_right" type="submit" id="boton-finalizar-green" value="PAGAR" />
      </form>
    </div>
  </div>

  <div class="pagos float_left">
    <div class="float_left"></div>
  </div>
</div>
