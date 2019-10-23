<?php defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="ccm-ui">
  <?php foreach ($games as $game): ?>
    <div class="well">
      <p><?= $game->getName() ?> (<?= sizeof($game->getRegistrations()) ?> registrations)</p>
      <?php if (sizeof($game->getOpenPools()) == 0): ?>
        <form id="massgameForm<?= $game->getId() ?>" method="POST" action="<?= $this->action('createPools') ?>">
          <input type="hidden" name="game_id" value="<?= $game->getId() ?>"/>
          <select name="count">
            <option>Pool count</option>
            <?php for ($idx = 1; $idx <= 10; $idx++): ?>
              <option><?= $idx ?></option>
            <?php endfor; ?>
          </select>
          <input type="submit" value="Create pools"/>
        </form>
      <?php else: ?>
        <table class="table table-striped table-condensed">
          <tbody>
          <form id="massgameForm<?= $game->getId() ?>" method="POST" action="<?= $this->action('updateRanks') ?>">
            <tr>
              <?php foreach ($game->getOpenPools() as $pool): ?>
                <td><strong><?= $pool->getName() ?></strong>
                  <?php foreach ($pool->getSortedUsers() as $user): ?>
                    <div>
                      <?= $user->getUser()->getUserName() ?><?= $user->getUser() == $pool->getHost() ? ' (Host)' : '' ?>
                      <select name="ranks[<?= $pool->getId() ?>][<?= $user->getUser()->getUserId() ?>]" class="pull-right">
                        <option value="0">Select rank</option>
                        <?php for ($idx = 1; $idx <= sizeof($pool->getUsers()); $idx++): ?>
                          <option<?= $idx == $user->getRank() ? ' selected' : '' ?>><?= $idx ?></option>
                        <?php endfor; ?>
                      </select>
                    </div>
                  <?php endforeach; ?>
                </td>
              <?php endforeach; ?>
              <td><input type="submit" value="Update" class="pull-right"/></td>
            </tr>
          </form>
          <tr>
            <td colspan="<?= sizeof($game->getOpenPools()) + 1 ?>">
              <?php if (sizeof($game->getOpenPools()) > 1): ?>
                <form id="massgameForm<?= $game->getId() ?>" method="POST" action="<?= $this->action('processPools') ?>">
                  <input type="hidden" name="game_id" value="<?= $game->getId() ?>"/>
                  <select name="count">
                    <option>Pool count</option>
                    <?php for ($idx = 1; $idx <= 10; $idx++): ?>
                      <option><?= $idx ?></option>
                    <?php endfor; ?>
                  </select>
                  <select name="rank">
                    <option>Required rank</option>
                    <?php for ($idx = 1; $idx <= 10; $idx++): ?>
                      <option><?= $idx ?></option>
                    <?php endfor; ?>
                  </select>
                  <input type="submit" value="Process pools"/>
                </form>
              <?php else: ?>
                <form id="massgameForm<?= $game->getId() ?>" method="POST" action="<?= $this->action('processFinalPool') ?>">
                  <input type="hidden" name="game_id" value="<?= $game->getId() ?>"/>
                  <input type="submit" value="Finish"/>
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