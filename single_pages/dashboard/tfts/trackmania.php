<?php defined('C5_EXECUTE') or die("Access Denied.");
?>

<table class="table table-striped table-condensed">
  <thead>
    <tr>
      <th>Game</th>
      <th>Map</th>
      <th>Records</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
  <form method="post" name="addMapForm" action="<?php echo $this->action('addMap') ?>">
    <tr>
      <td>
        <select name="game_id">
          <option value="0">Select game</option>
          <?php foreach ($games as $game): ?>
            <option value="<?= $game->getId() ?>"><?= $game->getName() ?></option>
          <?php endforeach; ?>
        </select>
      </td>
      <td><input type="text" name="map_name" placeholder="Name"/></td>
      <td>&nbsp;</td>
      <td><input type="submit" value="Add" /></td>
    </tr>
  </form>
  <?php foreach ($mapGames as $mapGame): ?>
    <tr>
      <th colspan="4"><?= $mapGame->getName() ?></th>
    </tr>
    <?php foreach ($mapGame->getMaps() as $map): ?>
      <tr>
        <td>&nbsp;</td>
        <td><?= $map->getName() ?></td>
        <td>
          <ol>
            <?php foreach ($tfts->getMapRankingList($map->getId()) as $record): ?>
              <form method="post" name="recordForm<?= $map->getId() ?>" action="<?php echo $this->action('modifyRecord') ?>">
                <input type="hidden" name="map_id" value="<?= $map->getId() ?>" /> 
                <input type="hidden" name="user_id" value="<?= $record['user_id'] ?>" />
                <li><?= $record['user'] ?> <input type="text" name="record" placeholder="Record" value="<?= $record['record'] ?>"/> <?php if (!$map->isProcessed()): ?><input type="submit" name="update" value="Update" /> <input type="submit" name="delete" value="Delete" /><?php endif; ?></li>
              </form>
            <?php endforeach; ?>
          </ol>
          <?php if (!$map->isProcessed()): ?>
          <form method="post" name="addRecordForm<?= $map->getId() ?>" action="<?php echo $this->action('addRecord') ?>">
            <input type="hidden" name="map_id" value="<?= $map->getId() ?>" /> 
            <select name="user_id">
              <option value="0">Select user</option>
              <?php foreach ($users as $user): ?>
                <option value="<?= $user->getUserId() ?>"><?= $user->getUserName() ?></option>
              <?php endforeach; ?>
            </select>
            <input type="text" name="record" placeholder="Record"/>
            <input type="submit" value="Add" />
          </form>
          <?php endif; ?>
        </td>
        <td>
          <?php if (sizeof($map->getRecords()) == 0): ?>
            <form method="post" name="mapForm<?= $map->getId() ?>" action="<?php echo $this->action('deleteMap') ?>">
              <input type="hidden" name="map_id" value="<?= $map->getId() ?>" /> 
              <input type="submit" value="Delete" />
            </form>
          <?php elseif (!$map->isProcessed()): ?>
            <form method="post" name="mapForm<?= $map->getId() ?>" action="<?php echo $this->action('processMap') ?>">
              <input type="hidden" name="map_id" value="<?= $map->getId() ?>" /> 
              <input type="submit" value="Process" />
            </form>
          <?php else: ?>
            Processed
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  <?php endforeach; ?>
</tbody>
</table>
