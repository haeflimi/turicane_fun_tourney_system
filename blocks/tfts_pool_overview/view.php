<?php defined('C5_EXECUTE') or die('Access Denied.');
$fh = Core::make('helper/form');
if(!$is_pool):?>
    <div class="alert alert-danger">
        <?=t('TFTS Games configured as Poll Games can use the Pool Overview Block')?>
    </div>
<?php return;
endif;?>


<h2>Pool Members <?php if(1 || !$in_pool):?>
        <button class="btn btn-success pull-right" onClick="Tfts.joinUserPool(<?=$me->getUserId();?>,<?=$tfts_game_id;?>,'<?=Core::make('token')->generate('joinPool');?>');">Turnierpool beitreten</button>
    <?php endif; ?></h2>

<table class="table table-striped table-condensed">
    <tbody>
    <?php foreach($registrations as $r):?>
        <tr>
            <td><?=$r->getUser()->getUserName()?></td>
            <td>
                <button class="btn btn-transparent btn-sm" onclick="Tfts.challengeUser(<?=$r->getUser()->getUserId()?>);"><?=t('Challange')?></button>
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
                <button class="btn btn-transparent btn-sm" onclick="Tfts.reportMatchResults();"><?=t('Report Result')?></button>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<hr/>
<h2>Open Callanges</h2>
<table class="table table-striped table-condensed">
    <tbody>
    <?php foreach($openChallenges as $om):?>
        <tr>
            <td><?=$om->getUser1()->getUserName()?> vs. <?=$om->getUser2()->getUserName()?></td>
            <td>
                <button class="btn btn-transparent btn-sm" onclick="Tfts.acceptChallenge();"><?=t('Accept Challenge')?></button>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<hr/>
<h2>Closed Matches</h2>
<table class="table table-striped table-condensed">
    <tbody>
    <?php foreach($closedMatches as $om):?>
        <tr>
            <td><?=$om->getUser1()->getUserName()?> vs. <?=$om->getUser2()->getUserName()?></td>
            <td>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>