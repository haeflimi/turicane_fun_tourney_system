<?php defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="ccm-ui">
  <?php foreach ($games as $game): ?>
    <div class="well">
        <h1><?= $game->getName() ?> <span class="text-muted small">(<?= sizeof($game->getRegistrations()) ?> registrations)</span></h1>
      <?php if (sizeof($game->getOpenPools()) == 0): ?>
        <form id="massgameForm<?= $game->getId() ?>" method="POST" class="form-inline" action="<?= $this->action('createPools') ?>">
          <input type="hidden" name="game_id" value="<?= $game->getId() ?>"/>
          <select name="count" class="form-control">
            <option>Pool count</option>
            <?php for ($idx = 1; $idx <= 10; $idx++): ?>
              <option><?= $idx ?></option>
            <?php endfor; ?>
          </select>
            <button class="btn btn-primary" type="submit" value="Create pools" title="Create pools"><i class="fa fa-plus-circle"></i></button>
        </form>
      <?php else: ?>
        <table class="table table-striped table-condensed">
          <tbody>
          <form id="massgameForm<?= $game->getId() ?>" class="form-inline" method="POST" action="<?= $this->action('updateRanks') ?>">
            <tr>
              <?php foreach ($game->getOpenPools() as $pool): ?>
                <td><h2><?= $pool->getName() ?></h2>
                  <?php foreach ($pool->getSortedUsers() as $user): ?>
                    <div class="form-group form-inline">
                        <label>
                            <?= $user->getUser()->getUserName() ?><?= $user->getUser() == $pool->getHost() ? ' (Host)' : '' ?>
                        </label>

                      <select name="ranks[<?= $pool->getId() ?>][<?= $user->getUser()->getUserId() ?>]" class="pull-right form-control">
                        <option value="0">Select rank</option>
                        <?php for ($idx = 1; $idx <= sizeof($pool->getUsers()); $idx++): ?>
                          <option<?= $idx == $user->getRank() ? ' selected' : '' ?>><?= $idx ?></option>
                        <?php endfor; ?>
                      </select>

                    </div>
                  <?php endforeach; ?>
                </td>
              <?php endforeach; ?>
                <td><button type="submit" value="Update" class="btn btn-primary" title="Update"><i class="fa fa-refresh"></i></button></td>
            </tr>
          </form>
          <tr>
            <td colspan="<?= sizeof($game->getOpenPools()) + 1 ?>">
              <?php if (sizeof($game->getOpenPools()) > 1): ?>
                <form id="massgameForm<?= $game->getId() ?>" class="form-inline" method="POST" action="<?= $this->action('processPools') ?>">
                  <input type="hidden" name="game_id" value="<?= $game->getId() ?>"/>
                  <select class="form-control" name="count">
                    <option>Pool count</option>
                    <?php for ($idx = 1; $idx <= 10; $idx++): ?>
                      <option><?= $idx ?></option>
                    <?php endfor; ?>
                  </select>
                  <select name="rank" class="form-control">
                    <option>Required rank</option>
                    <?php for ($idx = 1; $idx <= 10; $idx++): ?>
                      <option><?= $idx ?></option>
                    <?php endfor; ?>
                  </select>
                    <button type="submit" value="Process pools" class="btn btn-primary">Process pools</button>
                </form>
              <?php else: ?>
                <form id="massgameForm<?= $game->getId() ?>" method="POST" action="<?= $this->action('processFinalPool') ?>">
                  <input type="hidden" name="game_id" value="<?= $game->getId() ?>"/>
                    <button type="submit" class="btn btn-default" value="Finish">Finish</button>
                </form>
              <?php endif; ?>
            </td>
          </tr>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
</div>