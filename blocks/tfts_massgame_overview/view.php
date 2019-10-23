<?php
defined('C5_EXECUTE') or die('Access Denied.');
$fh = Core::make('helper/form');
if (!$is_mass):
  ?>
  <div class="alert alert-danger">
    <?= t('Only TFTS Games configured as mass game can use the mass overview block') ?>
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
elseif (!$current_user->isLoggedIn()):
  ?>
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
<?php if (sizeof($openPools) > 0): ?>
  <hr>
  <h2>Active pools</h2>
  <table class="table table-striped table-condensed">
    <tbody>
      <tr>
        <?php foreach ($openPools as $pool): ?>
          <td><h4><?= $pool->getName() ?></h4>
            <?php foreach ($pool->getSortedUsers() as $rank => $poolUser): ?>
              <div>
                <?= $poolUser->getRank() > 0 ? $poolUser->getRank() . '.' : '' ?> <a data-toggle="modal" data-target="#modal" data-source="/members/profile/<?= $poolUser->getUser()->getUserID() ?>" class="btn btn-outline-primary btn-round btn-sm mb-1"><i class="fa fa-user"></i> <?= $poolUser->getUser()->getUserName() ?></a>
                <?= $pool->getHost()->getUserId() == $poolUser->getUser()->getUserId() ? ' (Host)' : '' ?>
              </div>
            <?php endforeach; ?>
          </td>
        <?php endforeach; ?>
      </tr>
    </tbody>
  </table>
<?php endif; ?>
<?php if (sizeof($finalPools) > 0): ?>
  <hr>
  <h2>Final pools</h2>
  <table class="table table-striped table-condensed">
    <tbody>
      <tr>
        <th>Winner</th>
        <th>2nd</th>
        <th>3rd</th>
      </tr>
      <?php foreach ($finalPools as $pool): ?>
        <tr>
          <?php
          foreach ($pool->getSortedUsers() as $rank => $poolUser):
            if ($rank >= 3) {
              break;
            }
            switch ($rank) {
              case 0:
                $class = 'bg-success';
                break;
              case 1:
                $class = 'bg-warning';
                break;
              case 2:
                $class = 'bg-info';
                break;
              default:
                $class = '';
                break;
            }
            ?>
            <td class="<?= $class; ?>">
              <a data-toggle="modal" data-target="#modal" data-source="/members/profile/<?= $poolUser->getUser()->getUserID() ?>" class="btn btn-outline-primary btn-round btn-sm mb-1"><i class="fa fa-user"></i> <?= $poolUser->getUser()->getUserName() ?></a>
            </td>
          <?php endforeach; ?>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>