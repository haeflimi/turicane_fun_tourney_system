<?php
defined('C5_EXECUTE') or die('Access Denied.');
$fh = Core::make('helper/form');
if (!$is_pool):
  ?>
  <div class="alert alert-danger">
    <?= t('Only TFTS Games configured as pool game can use the pool overview block') ?>
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
  <?php if ($is_team): ?>
    <div class="btn-group pull-right">
      <?php if (sizeof($registeredGroups) > 0) :
        ?>
        <div class="dropdown">
          <button class="btn btn-danger dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?= t('Leave pool with team') ?>
          </button>
          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <?php foreach ($registeredGroups as $group): ?>
              <a class="dropdown-item"
                 onClick="Tfts.leavePool(<?= $tfts_game_id; ?>, <?= $group->getGroupId(); ?>, <?= $is_team ?>, '<?= Core::make('token')->generate('leavePool'); ?>');"><?= $group->getGroupName() ?></a>
               <?php endforeach; ?>
          </div>
        </div>&nbsp;
        <?php
      endif;
      if (sizeof($unregisteredGroups) > 0) :
        ?>
        <div class="dropdown">
          <button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?= t('Join pool with team') ?>
          </button>
          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <?php foreach ($unregisteredGroups as $group): ?>
              <a class="dropdown-item"
                 onClick="Tfts.joinPool(<?= $tfts_game_id; ?>, <?= $group->getGroupId(); ?>, <?= $is_team ?>, '<?= Core::make('token')->generate('joinPool'); ?>');"><?= $group->getGroupName() ?></a>
               <?php endforeach; ?>
          </div>
        </div>
        <?php
      endif;
      if (sizeof($registeredGroups) == 0 && sizeof($unregisteredGroups) == 0):
        ?>
        <div class="alert alert-danger">
          <?= t('No teams available') ?>
        </div>
      <?php endif;
      ?>
    </div>
    <?php
  else:
    if ($in_pool):
      ?>
      <button class="btn btn-danger pull-right"
              onClick="Tfts.leavePool(<?= $tfts_game_id; ?>, <?= $current_user->getUserId(); ?>, <?= $is_team ?>, '<?= Core::make('token')->generate('leavePool'); ?>');">
                <?= t('Leave pool') ?>
      </button>
    <?php else: ?>
      <button class="btn btn-success pull-right"
              onClick="Tfts.joinPool(<?= $tfts_game_id; ?>, <?= $current_user->getUserId(); ?>, <?= $is_team ?>, '<?= Core::make('token')->generate('joinPool'); ?>');">
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
      $my_ids = array();
      if ($is_team) {
        foreach ($registeredGroups as $group) {
          $my_ids[] = $group->getGroupId();
        }
        $challenged_id = $registration->getGroupId();
      } else {
        $active_id = $current_user->getUserId();
        $my_ids[] = $active_id;
        $challenged_id = $registration->getUser()->getUserId();
      }

      $my_registration = $registration->belongsTo($current_user);

      // @todo: This code from here...
      $open_challenge = false;
      $open_match = false;
      $is_challenger = false;
      $is_challenged = false;
      $match_id = null;
      foreach ($my_ids as $my_id) {
        foreach ($openChallenges as $match) {
          if (($match->getChallengerId() == $my_id && $match->getChallengedId() == $challenged_id) || ($match->getChallengedId() == $my_id && $match->getChallengerId() == $challenged_id)) {
            $open_challenge = true;
            $match_id = $match->getId();
            $active_id = $my_id;
            $is_challenger = $match->getChallengerId() == $my_id;
            $is_challenged = $match->getChallengedId() == $my_id;
            break;
          }
        }
        foreach ($openMatches as $match) {
          if (($match->getChallengerId() == $my_id && $match->getChallengedId() == $challenged_id) || ($match->getChallengedId() == $my_id && $match->getChallengerId() == $challenged_id)) {
            $open_match = true;
            break;
          }
        }
      }
      $can_challenge = $in_pool && !$my_registration && !$open_challenge && !$open_match;
      // @todo: .. to here should go in a separate place: $can_challenge = $tfts->canChallenge($active_id, $this->registration->getGame(), $challenged_id);
      ?>
      <tr>
        <td>
          <?php if (!$is_team): ?>
            <a data-toggle="modal" data-target="#modal" data-source="/members/profile/<?= $registration->getUser()->getUserID() ?>" class="btn btn-outline-primary btn-round btn-sm mb-1"><i class="fa fa-user"></i> <?= $registration->getUser()->getUserName() ?></a>
          <?php else: ?>
            <?= $name ?>
          <?php endif; ?>
        </td>
        <td>
          <?php if ($is_team): ?>
            <?php foreach ($registration->getGroup()->getGroupMembers() as $teamMember): ?>
              <a data-toggle="modal" data-target="#modal" data-source="/members/profile/<?= $teamMember->getUserID() ?>" class="btn btn-outline-primary btn-round btn-sm mb-1"><i class="fa fa-user"></i> <?= $teamMember->getUserName() ?></a>
              <?php
            endforeach;
            ?>
          <?php endif; ?>
        </td>
        <td>
          <?php if (!$is_team && $can_challenge): ?>
            <button class="btn btn-transparent btn-sm pull-right"
                    onClick="Tfts.createChallenge(<?= $tfts_game_id; ?>, <?= $active_id; ?>, <?= $challenged_id; ?>, '<?= $name ?>', <?= $is_team ?>, '<?= Core::make('token')->generate('createChallenge'); ?>');"><?= t('Challenge') ?></button>
                  <?php elseif ($is_team && $can_challenge): ?>
            <div class="dropdown">
              <button class="btn btn-transparent btn-sm dropdown-toggle pull-right" type="button"
                      id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                      aria-expanded="false">
                        <?= t('Select team to challenge with') ?>
              </button>
              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <?php foreach ($registeredGroups as $group): ?>
                  <a class="dropdown-item"
                     onClick="Tfts.createChallenge(<?= $tfts_game_id; ?>, <?= $group->getGroupId(); ?>, <?= $challenged_id; ?>, '<?= $name ?>', <?= $is_team ?>, '<?= Core::make('token')->generate('createChallenge'); ?>');"><?= $group->getGroupName() ?></a>
                   <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>
          <?php if ($open_challenge && $is_challenger): ?>
            <button class="btn btn-transparent btn-sm pull-right"
                    onClick="Tfts.withdrawChallenge(<?= $match_id; ?>, <?= $active_id; ?>, <?= $is_team ?>, '<?= Core::make('token')->generate('withdrawChallenge'); ?>');"><?= t('Cancel challenge') ?></button>
                  <?php endif; ?>
                  <?php if ($open_challenge && $is_challenged): ?>
            <div class="btn-group pull-right">
              <button class="btn btn-transparent btn-sm pull-right"
                      onClick="Tfts.acceptChallenge(<?= $match_id; ?>, <?= $active_id; ?>, <?= $is_team ?>, '<?= Core::make('token')->generate('acceptChallenge'); ?>');"><?= t('Accept challenge') ?></button>&nbsp;
              <button class="btn btn-transparent btn-sm pull-right"
                      onClick="Tfts.declineChallenge(<?= $match_id; ?>, <?= $active_id; ?>, <?= $is_team ?>, '<?= Core::make('token')->generate('declineChallenge'); ?>');"><?= t('Decline challenge') ?></button>
            </div>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<hr/>
<?php if (count($openMatches) > 0): ?>
  <h2>Open Matches</h2>
  <table class="table table-striped table-condensed">
    <tbody>
      <?php foreach ($openMatches as $match): ?>

        <tr>
          <td><?= $match->getChallengerName() ?> vs. <?= $match->getChallengedName() ?></td>
          <td>
            <form id="resultForm<?= $match->getId(); ?>" class="" method="POST">
              <?php if ($tfts->canEnterResult($current_user, $match)): ?>
                <?php if ($is_team && $match->getGame()->getGroupSize() != $match->getMyTeam()->getGroupMembersNum())://only display this when your current team has too many players   ?>
                  <div class="row">
                    <div class="col">
                      <div class="form-group pull-right">
                        <p class="muted"><?= t('Choose players:') ?></p>
                        <?php foreach ($match->getMyTeam()->getGroupMembers() as $user): ?>
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="user_ids" id="user_ids"
                                   value="<?= $user->getUserID() ?>">
                            <label class="form-check-label" for="inlineCheckbox1"><?= $user->getUserName() ?></label>
                          </div>
                        <?php endforeach; ?>
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
                <div class="row">
                  <div class="col">
                    <div class="form-group form-inline pull-right">
                      <?php if ($tfts->canReportResult($current_user, $match)): ?>

                        <input type="hidden" name="ccm_token"
                               value="<?= Core::make('token')->generate('reportResultMatch'); ?>"/>&nbsp;
                        <input class="form-control form-control-sm" type="number"
                               placeholder="<?= $match->getChallengerName() ?>" name="score1"
                               value="<?= $match->getScore1() ?>"/>&nbsp;
                        <input class="form-control form-control-sm" type="number"
                               placeholder="<?= $match->getChallengedName() ?>" name="score2"
                               value="<?= $match->getScore2() ?>"/>&nbsp;
                        <button type="button" class="btn btn-transparent btn-sm pull-right"
                                onclick="Tfts.reportResultMatch(<?= $match->getId() ?>, <?= $tfts->getActiveId($current_user, $match) ?>, <?= $is_team ?>);"><?= t('Report result') ?></button>&nbsp;

                      <?php else: ?>
                        <?= t('Waiting for confirmation ...') ?>
                      <?php endif; ?>
                      <?php if ($tfts->canCancelMatch($match)): ?>
                        <button type="button" class="btn btn-transparent btn-sm pull-right"
                                onclick="Tfts.cancelMatch(<?= $match->getId() ?>, <?= $tfts->getActiveId($current_user, $match) ?>, <?= $is_team ?>);"><?= t('Cancel match') ?></button>
                              <?php endif; ?>

                    </div>
                  </div>
                </div>
              <?php endif; ?>
            </form>
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
          <td <?= $class1; ?>><?= $match->getChallengerName() ?> <strong>(+<?= $match->getCompute1() ?>)</strong>
          </td>
          <td align="center"><strong><?= $match->getScore1() ?></strong> :
            <strong><?= $match->getScore2() ?></strong></td>
          <td <?= $class2; ?>><?= $match->getChallengedName() ?> <strong>(+<?= $match->getCompute2() ?>)</strong>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php
endif;
?>