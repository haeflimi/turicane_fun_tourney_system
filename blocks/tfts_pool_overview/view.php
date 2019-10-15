<?php defined('C5_EXECUTE') or die('Access Denied.');
$fh = Core::make('helper/form');
if(!$is_pool):?>
    <div class="alert alert-danger">
        <?=t('TFTS Games configured as Poll Games can use the Pool Overview Block')?>
    </div>
<?php elseif(!$me->isLoggedIn()):?>
    <div class="alert alert-danger">
        <?=t('You need to be logged in to see Tournament details.')?>
    </div>
<?php return;
endif;?>


<h2>Participants
<?php if($in_pool):?>
    <button class="btn btn-danger pull-right" onClick="Tfts.leaveUserPool(<?=$me->getUserId();?>,<?=$tfts_game_id;?>,'<?=Core::make('token')->generate('leaveUserPool');?>');">Turnierpool verlassen</button>
<?php else:?>
    <button class="btn btn-success pull-right" onClick="Tfts.joinUserPool(<?=$me->getUserId();?>,<?=$tfts_game_id;?>,'<?=Core::make('token')->generate('joinUserPool');?>');">Turnierpool beitreten</button>
<?php endif; ?>
</h2>

<table class="table table-striped table-condensed">
    <tbody>
    <?php foreach($registrations as $r):
        $name = $r->getName();;
        $challenged_id = $r->getParticipantId();
        $challenger_id = ($is_team)?$id = $myTeam->getGroupID():$me->getUserID();
        $showChallenge = false;
        if(!$is_team && $r->getUser()->getUserId() != $me->getUserId())$showChallenge = true;
        if($is_team && !$me->inGroup($r->getGroup()))$showChallenge = true;
        ?>
        <tr>
            <td><?=$r->getName()?></td>
            <td>
                <?php if($showChallenge):?>
                <button class="btn btn-transparent btn-sm pull-right"  onClick="Tfts.challengeUser(<?=$challenger_id;?>,<?=$challenged_id;?>,<?=$tfts_game_id;?>,'<?=Core::make('token')->generate('joinUserPool');?>', '<?=$name?>', <?=$is_team?>);"><?=t('Challenge')?></button>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<hr/>
<h2><?=(count($openMatches) == 0)?'No ':'';?>Open Matches</h2>
<table class="table table-striped table-condensed">
    <tbody>
    <?php foreach($openMatches as $om):
        $meId = ($is_team)?$myTeam->getGroupID():$me->getUserID();
        $showForm = false;
        if(!$is_team && ($me->getUserID() == $om->getOpponent1Id() || $me->getUserID() == $om->getOpponent2Id())){
            $showForm = true;
        }elseif($is_team && ($myTeam->getGroupID() == $om->getOpponent1Id() || $myTeam->getGroupID() == $om->getOpponent2Id())){
            $showForm = true;
        }
    ?>
        <tr>
            <td><?=$om->getOpponent1Name()?> vs. <?=$om->getOpponent2Name()?></td>
            <td>
                <?php if($showForm):?>
                <form id="resultForm" class="form-inline pull-right" method="POST">
                    <input type="hidden" name="ccm_token" value="<?=Core::make('token')->generate('reportResultUserMatch');?>"/>&nbsp;
                    <input class="form-control form-control-sm" type="number" placeholder="<?=$om->getOpponent1Name()?> Score" name="user1_score"/>&nbsp;
                    <input class="form-control form-control-sm" type="number" placeholder="<?=$om->getOpponent2Name()?> Score" name="user2_score"/>&nbsp;
                    <button type="button" class="btn btn-transparent btn-sm pull-right" onclick="Tfts.reportResultUserMatch(<?=$om->getId()?>,<?=$meId?>,<?=$is_team?>);"><?=t('Report Result')?></button>
                </form>
                <?php endif;?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<hr/>
<h2>Match History</h2>
<table class="table table-striped table-condensed">
    <tbody>
    <?php foreach($closedMatches as $cm):
        $winnerId = ($is_team)?$cm->getWinner()->getGroupID():$cm->getWinner()->getUserID();
        $class1 = ($winnerId == $cm->getOpponent1Id())?'class="bg-success"':'class="bg-danger"';
        $class2 = ($winnerId == $cm->getOpponent2Id())?'class="bg-success"':'class="bg-danger"';?>
        <tr>
            <td <?=$class1;?>><?=$cm->getOpponent1Name()?></td>
            <td <?=$class1;?>><strong><?=$cm->getScore1()?></strong></td>
            <td <?=$class1;?>><strong>+<?=$cm->getCompute1()?> p.</strong></td>
            <td <?=$class2;?>><?=$cm->getOpponent2Name()?></td>
            <td <?=$class2;?>><strong><?=$cm->getScore2()?></strong></td>
            <td <?=$class2;?>><strong>+<?=$cm->getCompute2()?> p.</strong></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>