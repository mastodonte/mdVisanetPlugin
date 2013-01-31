<?php if($error): ?>
<?php var_dump($response); ?>
<?php echo $error; ?>
<?php else: ?>

<h2>TRANSACCION COMPLETA!</h2>
<br />

<a href="<?php echo url_for('@homepage'); ?>">Continuar Comprando</a>
<br /><br />

<?php endif; ?>
