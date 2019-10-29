<?php
defined('C5_EXECUTE') or die('Access Denied.');
if (!$current_user->isLoggedIn()):
  ?>
  <div class="alert alert-danger">
    <?= t('You need to be logged in to see tournament details.') ?>
  </div>
  <?php
  return;
endif;
?>