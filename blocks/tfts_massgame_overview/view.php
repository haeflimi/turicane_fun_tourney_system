<?php
defined('C5_EXECUTE') or die('Access Denied.');
$fh = Core::make('helper/form');
if (!$is_pool):
?>
  <div class="alert alert-danger">
    <?= t('Only TFTS Games configured as pool game can use the mass overview block') ?>
  </div>
<?php 
  return;
elseif ($is_team):
?>
  <div class="alert alert-danger">
    <?= t('Only TFTS Games configured as non team game can use the mass overview block') ?>
  </div>
<?php 
  return;
elseif (!$current_user->isLoggedIn()): ?>
  <div class="alert alert-danger">
    <?= t('You need to be logged in to see tournament details.') ?>
  </div>
  <?php
  return;
endif;
?>
<h2>Participants
  <?php if ($in_pool): ?>
    <button class="btn btn-danger pull-right" 
            onClick="Tfts.leavePool(<?= $tfts_game_id; ?>, <?= $current_user->getUserId(); ?>, 0, '<?= Core::make('token')->generate('leavePool'); ?>');">
              <?= t('Leave pool') ?>
    </button>
  <?php else: ?>
    <button class="btn btn-success pull-right" 
            onClick="Tfts.joinPool(<?= $tfts_game_id; ?>, <?= $current_user->getUserId(); ?>, 0, '<?= Core::make('token')->generate('joinPool'); ?>');">
              <?= t('Join pool') ?>
    </button>
  <?php endif; ?>
</h2>
<table class="table table-striped table-condensed">
  <tbody>
    <tr>
      <td>
      <?php foreach ($registrations as $registration): ?>
        <a data-toggle="modal" data-target="#modal" data-source="/members/profile/<?= $registration->getUser()->getUserID() ?>" class="btn btn-outline-primary btn-round btn-sm mb-1"><i class="fa fa-user"></i> <?= $registration->getUser()->getUserName() ?></a>&nbsp;
      <?php endforeach; ?>
      </td>
    </tr>
  </tbody>
</table>
<hr>
<h2>Active pools</h2>
<hr>
<h2>Final pools</h2>