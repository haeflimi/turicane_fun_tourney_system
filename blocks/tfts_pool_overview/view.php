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
        if ($is_team):?>
            <div class="btn-group pull-right">
            <?php if (sizeof($registeredGroups) > 0) :
                ?>
                <div class="dropdown">
                    <button class="btn btn-danger dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?= t('Leave pool with team') ?>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <?php foreach ($registeredGroups as $group): ?>
                            <a class="dropdown-item"
                               onClick="Tfts.leaveGroupPool(<?= $group->getGroupId(); ?>, <?= $tfts_game_id; ?>, '<?= Core::make('token')->generate('leaveGroupPool'); ?>');"><?= $group->getGroupName() ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>&nbsp;
            <?php
            endif;
            if (sizeof($unregisteredGroups) > 0) :
                ?>
                <div class="dropdown">
                    <button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?= t('Join pool with team') ?>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <?php foreach ($unregisteredGroups as $group): ?>
                            <a class="dropdown-item"
                               onClick="Tfts.joinGroupPool(<?= $group->getGroupId(); ?>, <?= $tfts_game_id; ?>, '<?= Core::make('token')->generate('joinGroupPool'); ?>');"><?= $group->getGroupName() ?></a>
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
            <?php
            endif;?>
            </div>
        <?php else:
            if ($in_pool):
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
            $my_ids = array();
            if ($is_team) {
                $type = 'Group';
                foreach ($registeredGroups as $group) {
                    $my_ids[] = $group->getGroupId();
                }
                $challenged_id = $registration->getGroupId();
            } else {
                $type = 'User';
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
                <td><?= $name ?></td>
                <td>
                    <?php if (!$is_team && $can_challenge): ?>
                        <button class="btn btn-transparent btn-sm pull-right"
                                onClick="Tfts.challengeUser(<?= $active_id; ?>,<?= $challenged_id; ?>,<?= $tfts_game_id; ?>, '<?= Core::make('token')->generate('challengeUser'); ?>', '<?= $name ?>');"><?= t('Challenge') ?></button>
                    <?php elseif ($is_team && $can_challenge): ?>
                        <form id="resultForm" class="form-inline pull-right" method="POST">
                            <div class="dropdown">
                                <button class="btn btn-transparent btn-sm dropdown-toggle pull-right" type="button"
                                        id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                    <?= t('Select team to challenge with') ?>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <?php foreach ($registeredGroups as $group): ?>
                                        <a class="dropdown-item"
                                           onClick="Tfts.challengeGroup(<?= $group->getGroupId(); ?>,<?= $challenged_id; ?>,<?= $tfts_game_id; ?>, '<?= Core::make('token')->generate('challengeGroup'); ?>', '<?= $name ?>');"><?= $group->getGroupName() ?></a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>
                    <?php if ($open_challenge && $is_challenger): ?>
                        <button class="btn btn-transparent btn-sm pull-right"
                                onClick="Tfts.withdraw<?= $type; ?>Challenge(<?= $match_id; ?>,<?= $active_id; ?>, '<?= Core::make('token')->generate('withdraw' . $type . 'Challenge'); ?>');"><?= t('Cancel challenge') ?></button>
                    <?php endif; ?>
                    <?php if ($open_challenge && $is_challenged): ?>
                        <button class="btn btn-transparent btn-sm pull-right"
                                onClick="Tfts.decline<?= $type; ?>Challenge(<?= $match_id; ?>,<?= $active_id; ?>, '<?= Core::make('token')->generate('decline' . $type . 'Challenge'); ?>');"><?= t('Decline challenge') ?></button>&nbsp;
                        <button class="btn btn-transparent btn-sm pull-right"
                                onClick="Tfts.accept<?= $type; ?>Challenge(<?= $match_id; ?>,<?= $active_id; ?>, '<?= Core::make('token')->generate('accept' . $type . 'Challenge'); ?>');"><?= t('Accept challenge') ?></button>
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
            <form id="resultForm" class="" method="POST">
                <tr>
                    <td><?= $match->getChallengerName() ?> vs. <?= $match->getChallengedName() ?></td>
                    <td>
                        <?php if ($tfts->canEnterResult($current_user, $match)):?>
                            <?php if ($type == 'Group' && $match->getGame()->getGroupSize() < $match->getMyTeam()->getGroupMembersNum())://only display this when your current team has too many players?>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group pull-right">
                                        <p class="muted"><?= t('Choose who was playing:') ?></p>
                                        <?php foreach($match->getMyTeam()->getGroupMembers() as $u):?>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="user_ids" id="user_ids"
                                                   value="<?=$u->getUserID()?>">
                                            <label class="form-check-label" for="inlineCheckbox1"><?=$u->getUserName()?></label>
                                        </div>
                                        <?php endforeach;?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group form-inline pull-right">
                                        <input type="hidden" name="ccm_token"
                                               value="<?= Core::make('token')->generate('reportResult' . $type . 'Match'); ?>"/>&nbsp;
                                        <input class="form-control form-control-sm" type="number"
                                               placeholder="<?= $match->getChallengerName() ?>" name="score1"/>&nbsp;
                                        <input class="form-control form-control-sm" type="number"
                                               placeholder="<?= $match->getChallengedName() ?>" name="score2"/>&nbsp;
                                        <button type="button" class="btn btn-transparent btn-sm pull-right"
                                                onclick="Tfts.reportResult<?= $type ?>Match(<?= $match->getId() ?>,<?= $active_id ?>);"><?= t('Report result') ?></button>&nbsp;
                                        <button type="button" class="btn btn-transparent btn-sm pull-right"
                                                onclick="Tfts.cancel<?= $type ?>Match(<?= $match->getId() ?>,<?= $active_id ?>);"><?= t('Cancel match') ?></button>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
            </form>
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