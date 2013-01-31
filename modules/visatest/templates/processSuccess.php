<h2>TRANSACCION COMPLETA!</h2>
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
