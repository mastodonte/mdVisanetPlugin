<form action="<?php echo url_for('@mdCart-payment'); ?>" method="GET">

  <input class="checkbox" type="radio" name="payment" value="visanet" checked="checked" /> VISA (pruebas)

  <input id="md_resumen_pagar" class="float_right boton-pagar-green" type="submit" value="PAGAR" <?php echo (is_null($cart->getAddressDeliveryId()) ? 'disabled="disabled"' : ''); ?>>
  
</form>
