<?php
defined('C5_EXECUTE') or die('Access Denied.');
$wh = Core::make('helper/form/user_selector');
if (!$current_user->isLoggedIn()):
  ?>
  <div class="alert alert-danger">
    <?= t('You need to be logged in to see tournament details.') ?>
  </div>
  <?php
  return;
endif;
?>

<div class="team-manager-block">
  <h3><?= t('Group manager - manage and create your groups!') ?></h3>
  <div class="team-manager-team-list">
    <?php foreach ($userGroups as $key => $group): ?>
      <div class="list-group team-list-item" data-gID="<?= $key ?>">
        <a class="list-group-heading d-flex justify-content-between align-items-center"  data-toggle="collapse" href="#team-collapse-<?= $group->getGroupID(); ?>">
          <span class="h4" title="<?= t('Show team members') ?>"><?= $group->getGroupName(); ?> </span>&nbsp;
        </a>
        <div class="list-group-item-collapse-wrapper">

          <div id="team-collapse-<?= $group->getGroupID() ?>" class="list-group-item-collapse collapse">
            <div class="list-group-item">
              <?php foreach ($group->getGroupMembers() as $user): ?>
                <a data-toggle="modal" data-target="#modal" data-source="/members/profile/<?= $user->getUserID() ?>" class="btn btn-outline-primary btn-round"><i class="fa fa-user"></i>  <?= $user->getUserName() ?></a>
              <?php endforeach; ?>
            </div>

            <div class="list-group-footer">
              <form id="inviteToGroup<?= $group->getGroupID() ?>" class="team-manager-invite-form form-inline" method="POST">
                <div class="input-group">
                  <?= $wh->quickSelect('user_id', false, ['placeholder' => t('Select User to Invite')]); ?>
                  <a class="btn btn-primary input-group-button input-group-append pull-right" title="<?= t('Invite Member') ?>"
                     onclick="Tfts.inviteToGroup(<?= $group->getGroupID() ?>, '<?= Core::make('token')->generate('inviteToGroup') ?>')">
                    <i class="fa fa-user-plus"></i>
                  </a>
                </div>
              </form>
              <a class="btn btn-danger btn-sm pull-right color-danger"
                 onclick="Tfts.leaveGroup(<?= $group->getGroupID() ?>, <?= $current_user->getUserID() ?>, '<?= Core::make('token')->generate('leaveUser') ?>');">
                <i class="fa fa-close"></i> Leave Team
              </a>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
    <?php if ($allowGroupCreation): ?>
      <div class="list-group team-list-item create-item">
        <a class="list-group-heading d-flex justify-content-between align-items-center"  data-toggle="collapse" href="#group-collapse-create">
          <span class="h5" title="<?= t('Create group') ?>"><?= t('Create group') ?> </span>
        </a>
        <div class="list-group-item-collapse-wrapper">
          <div id="group-collapse-create" class="list-group-item-collapse collapse">
            <div class="list-group-footer">
              <form id="createGroup" class="team-manager-team-form" method="POST">
                <div class="input-group">
                  <?= $form->text('group_name', ['class' => 'input-group-prepend', 'autocomplete' => 'off', 'placeholder' => 'Name']); ?>
                  <a class="btn btn-primary input-group-button input-group-append pull-right" title="<?= t('Create group') ?>"
                     onclick="Tfts.createGroup(<?= $current_user->getUserId() ?>, '<?= Core::make('token')->generate('createGroup'); ?>');">
                    <i class="fa fa-plus-circle"></i>
                  </a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
    <?php if ($errors):; ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error:</strong><br/>
        <?= $errors->output(); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <?php endif; ?>
  </div>
</div>
