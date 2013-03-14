<?php $mdPayment = Doctrine::getTable('mdPaymentModule')->findOneByLabel($md_order->getModulePayment()); ?>
<?php $registers = $md_order->getMdVisa(); ?>

<div style="margin: 2px 0 1em 50px;">
  <table>
    <tbody>
      <tr>
        <td><label>Modo de pago: </label></td>
        <td><?php echo $mdPayment->getName(); ?></td>      
      </tr>
      <?php foreach($registers as $register): ?>

        <tr>
          <td><label>Status:</label></td>
          <td><?php echo $register->getStatus(); ?></td>
        </tr>
 
       <?php if($register->getStatus() == mdVisa::PAYED): ?>
          <tr>
            <td><label>Plan:</label></td>
            <td><?php echo $register->getPlanName(); ?></td>
          </tr>        
          <tr>
            <td><label># Cuotas:</label></td>
            <td><?php echo $register->getQuotaCode(); ?></td>
          </tr>
        <?php endif; ?>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
