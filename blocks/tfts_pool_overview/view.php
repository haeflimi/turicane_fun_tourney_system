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


<h2>Pool Members
<?php if($in_pool):?>
    <button class="btn btn-danger pull-right" onClick="Tfts.leaveUserPool(<?=$me->getUserId();?>,<?=$tfts_game_id;?>,'<?=Core::make('token')->generate('leaveUserPool');?>');">Turnierpool verlassen</button>
<?php else:?>
    <button class="btn btn-success pull-right" onClick="Tfts.joinUserPool(<?=$me->getUserId();?>,<?=$tfts_game_id;?>,'<?=Core::make('token')->generate('joinUserPool');?>');">Turnierpool beitreten</button>
<?php endif; ?>
</h2>

<table class="table table-striped table-condensed">
    <tbody>
    <?php foreach($registrations as $r):?>
        <tr>
            <td><?=$r->getUser()->getUserName()?></td>
            <td>
                <?php if($r->getUser()->getUserId() != $me->getUserId()):?>
                <button class="btn btn-transparent btn-sm pull-right"  onClick="Tfts.challengeUser(<?=$me->getUserId();?>,<?=$r->getUser()->getUserId();?>,<?=$tfts_game_id;?>,'<?=Core::make('token')->generate('joinUserPool');?>', '<?=$r->getUser()->getUserName()?>');"><?=t('Challenge')?></button>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<hr/>
<h2>Open Matches</h2>
<table class="table table-striped table-condensed">
    <tbody>
    <?php foreach($openMatches as $om):?>
        <tr>
            <td><?=$om->getUser1()->getUserName()?> vs. <?=$om->getUser2()->getUserName()?></td>
            <td>
                <?php if($om->getUser1()->getUserID() == $me->getUserID() || $om->getUser2()->getUserID() == $me->getUserID()):?>
                <form id="resultForm" class="form-inline pull-right" method="POST">
                    <input type="hidden" name="ccm_token" value="<?=Core::make('token')->generate('reportResultUserMatch');?>"/>&nbsp;
                    <input class="form-control form-control-sm" type="number" placeholder="<?=$om->getUser1()->getUserName()?> Score" name="user1_score"/>&nbsp;
                    <input class="form-control form-control-sm" type="number" placeholder="<?=$om->getUser2()->getUserName()?> Score" name="user2_score"/>&nbsp;
                    <button type="button" class="btn btn-transparent btn-sm pull-right" onclick="Tfts.reportResultUserMatch(<?=$om->getId()?>,<?=$me->getUserID()?>);"><?=t('Report Result')?></button>
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
    <?php foreach($closedMatches as $cm):?>
        <tr>
            <td><?=$cm->getUser1()->getUserName()?> vs. <?=$cm->getUser2()->getUserName()?></td>
            <td>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>