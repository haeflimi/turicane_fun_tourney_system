<?php
defined('C5_EXECUTE') or die('Access Denied.');
$fh = Core::make('helper/form');
if (!$is_pool):
  ?>
  <div class="alert alert-danger">
    <?= t('Only TFTS Games configured as pool game can use the pool overview block') ?>
  </div>
<?php elseif (!$current_user->isLoggedIn()): ?>
  <div class="alert alert-danger">
    <?= t('You need to be logged in to see tournament details.') ?>
  </div>
  <?php
  return;
endif;
?>
<h2>Participants
  <?php
  if ($is_team):
    if (sizeof($registeredGroups) > 0) :
      ?>
      <div class="dropdown">
        <button class="btn btn-danger dropdown-toggle pull-right" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <?= t('Leave pool with team') ?>
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
          <?php foreach ($registeredGroups as $group): ?>
            <a class="dropdown-item" onClick="Tfts.leaveGroupPool(<?= $group->getGroupId(); ?>, <?= $tfts_game_id; ?>, '<?= Core::make('token')->generate('leaveGroupPool'); ?>');"><?= $group->getGroupName() ?></a>
          <?php endforeach; ?>
        </div>
      </div>
      <?php
    endif;
    if (sizeof($unregisteredGroups) > 0) :
      ?>
      <div class="dropdown">
        <button class="btn btn-success dropdown-toggle pull-right" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <?= t('Join pool with team') ?>
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
          <?php foreach ($unregisteredGroups as $group): ?>
            <a class="dropdown-item" onClick="Tfts.joinGroupPool(<?= $group->getGroupId(); ?>, <?= $tfts_game_id; ?>, '<?= Core::make('token')->generate('joinGroupPool'); ?>');"><?= $group->getGroupName() ?></a>
          <?php endforeach; ?>
        </div>
      </div>
      <?php
    else:
      ?>
      <div class="alert alert-danger">
        <?= t('No teams available') ?>
      </div>
    <?php
    endif;
  else:
    if ($user_in_pool):
      ?>
      <button class="btn btn-danger pull-right"
              onClick="Tfts.leaveUserPool(<?= $current_user->getUserId(); ?>,<?= $tfts_game_id; ?>, '<?= Core::make('token')->generate('leaveUserPool'); ?>');">
                <?= t('Leave pool') ?>
      </button>
    <?php else: ?>
      <button class="btn btn-success pull-right"
              onClick="Tfts.joinUserPool(<?= $current_user->getUserId(); ?>,<?= $tfts_game_id; ?>, '<?= Core::make('token')->generate('joinUserPool'); ?>');">
                <?= t('Join pool') ?>
      </button>
    <?php
    endif;
  endif;
  ?>
</h2>
<table class="table table-striped table-condensed">
  <tbody>
    <?php
    foreach ($registrations as $registration):
      $name = $registration->getName();
      if ($is_team) {
        
      } else {
        $user_id = $current_user->getUserId();
        $challenged_id = $registration->getUser()->getUserId();
        $is_user = $challenged_id == $user_id;

        $open_challenge = false;
        $open_match = false;
        $is_challenger = false;
        $is_challenged = false;
        $match_id = null;
        foreach ($openChallenges as $match) {
          if (($match->getUser1()->getUserId() == $user_id && $match->getUser2()->getUserId() == $challenged_id) || ($match->getUser2()->getUserId() == $user_id && $match->getUser1()->getUserId() == $challenged_id)) {
            $open_challenge = true;
            $match_id = $match->getId();
            $is_challenger = $match->getUser1()->getUserId() == $user_id;
            $is_challenged = $match->getUser2()->getUserId() == $user_id;
            break;
          }
        }
        foreach ($openMatches as $match) {
          if (($match->getUser1()->getUserId() == $user_id && $match->getUser2()->getUserId() == $challenged_id) || ($match->getUser2()->getUserId() == $user_id && $match->getUser1()->getUserId() == $challenged_id)) {
            $open_match = true;
            break;
          }
        }
      }
      ?>
      <tr>
        <td><?= $name ?></td>
        <td>
          <?php if (!$is_user && !$open_challenge && !$open_match): ?>
            <button class="btn btn-transparent btn-sm pull-right" onClick="Tfts.challengeUser(<?= $user_id; ?>,<?= $challenged_id; ?>,<?= $tfts_game_id; ?>, '<?= Core::make('token')->generate('challengeUser'); ?>', '<?= $name ?>');"><?= t('Challenge') ?></button>
          <?php endif; ?>
          <?php if ($open_challenge && $is_challenger): ?>
            <button class="btn btn-transparent btn-sm pull-right" onClick="Tfts.withdrawUserChallenge(<?= $match_id; ?>,<?= $user_id; ?>, '<?= Core::make('token')->generate('withdrawUserChallenge'); ?>');"><?= t('Cancel challenge') ?></button>
          <?php endif; ?>
          <?php if ($open_challenge && $is_challenged): ?>
            <button class="btn btn-transparent btn-sm pull-right" onClick="Tfts.declineUserChallenge(<?= $match_id; ?>,<?= $user_id; ?>, '<?= Core::make('token')->generate('declineUserChallenge'); ?>');"><?= t('Decline challenge') ?></button>&nbsp;
            <button class="btn btn-transparent btn-sm pull-right" onClick="Tfts.acceptUserChallenge(<?= $match_id; ?>,<?= $user_id; ?>, '<?= Core::make('token')->generate('acceptUserChallenge'); ?>');"><?= t('Accept challenge') ?></button>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<hr/>
<?php
if (count($openMatches) > 0):
  ?>
  <h2>Open Matches</h2>
  <table class="table table-striped table-condensed">
    <tbody>
      <?php
      foreach ($openMatches as $match):
        ?>
        <tr>
          <td><?= $match->getChallengerName() ?> vs. <?= $match->getChallengedName() ?></td>
          <td>
            <?php
            if ($is_team):

            else:
              $user_id = $current_user->getUserID();
              $show_score_form = $match->getUser1()->getUserId() == $user_id || $match->getUser2()->getUserId() == $user_id;

              if ($show_score_form):
                ?>
                <form id="resultForm" class="form-inline pull-right" method="POST">
                  <input type="hidden" name="ccm_token" value="<?= Core::make('token')->generate('reportResultUserMatch'); ?>"/>&nbsp;
                  <input class="form-control form-control-sm" type="number" placeholder="<?= $match->getChallengerName() ?> Score" name="score1"/>&nbsp;
                  <input class="form-control form-control-sm" type="number" placeholder="<?= $match->getChallengedName() ?> Score" name="score2"/>&nbsp;
                  <button type="button" class="btn btn-transparent btn-sm pull-right" onclick="Tfts.reportResultUserMatch(<?= $match->getId() ?>,<?= $user_id ?>);"><?= t('Report result') ?></button>&nbsp;
                  <button type="button" class="btn btn-transparent btn-sm pull-right" onclick="Tfts.cancelUserMatch(<?= $match->getId() ?>,<?= $user_id ?>);"><?= t('Cancel match') ?></button>
                </form>
              <?php endif; ?>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <hr/>
  <?php
endif;
if (count($closedMatches) > 0):
  ?>
  <h2>Played matches</h2>
  <table class="table table-striped table-condensed">
    <tbody>
      <?php
      foreach ($closedMatches as $match):
        if ($match->getWinnerId() == 0) {
          $class1 = $class2 = 'class="bg-warning"';
        } else {
          $class1 = $match->getWinnerId() == $match->getChallengerId() ? 'class="bg-success"' : 'class="bg-danger"';
          $class2 = $match->getWinnerId() == $match->getChallengedId() ? 'class="bg-success"' : 'class="bg-danger"';
        }
        ?>
        <tr>
          <td <?= $class1; ?>><?= $match->getChallengerName() ?> <strong>(+<?= $match->getCompute1() ?>)</strong></td>
          <td align="center"><strong><?= $match->getScore1() ?></strong> : <strong><?= $match->getScore2() ?></strong></td>
          <td <?= $class2; ?>><?= $match->getChallengedName() ?> <strong>(+<?= $match->getCompute2() ?>)</strong></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php
endif;
?>