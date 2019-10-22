<?php defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="ccm-dashboard-header-buttons btn-group">
    <button class="btn btn-default" type="submit" name="action" value="add"><?=t('Add Game')?></button>
</div>

<div class="ccm-ui">
    <?php foreach($games as $game): ?>
        <div class="well"><?=$game->getName()?></div>
    <?php endforeach; ?>
</div>

<div class="ccm-dashboard-form-actions-wrapper">
    <div class="ccm-dashboard-form-actions">
        <button class="pull-right btn btn-success" type="submit" ><?=t('Save')?></button>
    </div>
</div>