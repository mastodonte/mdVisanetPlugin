<div class="carito">
  <h5>Compra exitosa!</h5>
  <div class="pago-facturacion">

    <div class="clear"></div>

    <h5>Gracias por comprar en SuperVentas.com.uy</h5>

    <div class="login-carrito">
      <div class="giro-brou">
        <span class="span-exitosa">En este momento estamos procesando su pedido.</span><div class="clear"></div>
        <span class="negrita-e">Su número de orden es: </span><span class="verde-e"><?php echo $md_order->getId(); ?></span><div class="clear"></div>

        <span class="span-exitosa">Ténga en cuenta este numero cuando nos contacte para buscar su orden fácilmente.</br>

          Ante cualquier eventualidad un representante Super Ventas se pondrá en contacto telefónicamente o email.</br>
          Si pagó con tarjeta de crédito usted verá un cargo de en su resumen mensual de Nalfer S.A. o de Superventas.com.uy</span><div class="clear"></div>
        <span class="negrita-e">Ante cualquier consulta no dude en contactarnos en el</br>
          Servicio de atención al cliente </span><div class="clear"></div>
        <span class="span-exitosa">TEL: +598 2409 55 38 o info@superventas.com.uy</span>
      </div>

      <div class="pagos float_left">
        <div class="float_left">
        </div>
      </div>
    </div>
  </div>
</div>
<br />

<a href="<?php echo url_for('@homepage'); ?>">Continuar Comprando</a>
<br /><br />

<?php

foreach($data as $key => $value){
  echo "<br> $key => " . $value;
}

if($error){
  echo $error;
}
