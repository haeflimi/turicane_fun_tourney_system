<?php defined('C5_EXECUTE') or die("Access Denied.");
?>

<header class="ccm-dashboard-page-header well" xmlns="http://www.w3.org/1999/html">

  <form method="post" name="addMapForm" action="<?php echo $this->action('addMap') ?>">
    <h2>Add Map:</h2>
    <div class="form-group form-inline">
      <label for="game_id" class="form-label-left">Game</label>
      <select name="game_id" class="form-control">
        <option value="0">Select game</option>
        <?php foreach ($games as $game): ?>
          <option value="<?= $game->getId() ?>"><?= $game->getName() ?></option>
        <?php endforeach; ?>
      </select>
      <input class="form-control" type="text" name="map_name" placeholder="Map Name"/>&nbsp;
      <label for="map_name" class="form-label-left">Resolution:&nbsp;</label>
      <select name="map_data_resolution" class="form-control">
        <option value="0">None</option>
        <option value="-2">Centi</option>
      </select>&nbsp;
      <label for="map_data_resolution" class="form-label-left">Unit:&nbsp;</label>
      <select name="map_data_unit" class="form-control">
        <option value="0">None</option>
        <option value="1">Seconds</option>
      </select>
      <button class="btn btn-primary pull-right" type="submit" value="Add" title="Add"><i class="fa fa-plus-circle"></i></button>
    </div>

  </form>
</header>

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

    <?php foreach ($mapGames as $mapGame): ?>
      <tr>
        <th colspan="4"><h2><?= $mapGame->getName() ?></h2></th>
      </tr>
      <?php foreach ($mapGame->getMaps() as $map): ?>
        <tr>
          <td>&nbsp;</td>
          <td><?= $map->getName() ?></td>
          <td>
            <ol>
              <?php foreach ($tfts->getMapRankingList($map->getId(), true) as $record): ?>
                <li>
                  <?= $record['user'] ?>
                  <form method="post" class="form-inline" name="recordForm<?= $map->getId() ?>"
                        action="<?php echo $this->action('modifyRecord') ?>">
                    <div class="form-group">
                      <input type="hidden" name="map_id" class="form-control" value="<?= $map->getId() ?>"/>
                      <input type="hidden" name="user_id" class="form-control" value="<?= $record['user_id'] ?>"/>
                      <input type="number" name="record" class="form-control" placeholder="Record" value="<?= $record['record'] ?>"/>
                      <?php if (!$map->isProcessed()): ?>
                        <button type="submit" name="update" value="Update" title="Update" class="btn btn-warning"><i class="fa fa-refresh"></i></button>
                        <button type="submit" name="delete" title="Delete" value="Delete" class="btn btn-danger"><i class="fa fa-remove"></i></button>
                      <?php endif; ?>
                    </div>
                  </form>
                </li>
              <?php endforeach; ?>
            </ol>
            <?php if (!$map->isProcessed()): ?>
              <form method="post" class="form-vertical" name="addRecordForm<?= $map->getId() ?>" action="<?php echo $this->action('addRecord') ?>">
                <div class="form-group form-inline">
                  <input class="form-control"  type="hidden" name="map_id" value="<?= $map->getId() ?>" />
                  <select class="form-control"  name="user_id">
                    <option value="0">Select user</option>
                    <?php foreach ($users as $user): ?>
                      <option value="<?= $user->getUserId() ?>"><?= $user->getUserName() ?></option>
                    <?php endforeach; ?>
                  </select>
                  <input class="form-control"  type="number" name="record" placeholder="Record"/>
                  <button class="btn btn-primary pull-right"  type="submit" value="Add" title="Add"><i class="fa fa-plus-circle"></i></button>
                </div>
              </form>
            <?php endif; ?>
          </td>
          <td>
            <?php if (sizeof($map->getRecords()) == 0): ?>
              <form method="post" name="mapForm<?= $map->getId() ?>" action="<?php echo $this->action('deleteMap') ?>">
                <input type="hidden" name="map_id" value="<?= $map->getId() ?>" />
                <button class="btn btn-danger" type="submit" value="Delete"><i class="fa fa-remove"></i></button>
              </form>
            <?php elseif (!$map->isProcessed()): ?>
              <form method="post" name="mapForm<?= $map->getId() ?>" action="<?php echo $this->action('processMap') ?>">
                <input type="hidden" name="map_id" value="<?= $map->getId() ?>" />
                <button class="btn btn-success" type="submit" value="Process">Process</button
              </form>
            <?php else: ?>
              <button class="btn btn-success btn-disabled" disabled>
                <i class="fa fa-check"></i> Processed
              </button>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endforeach; ?>
  </tbody>
</table>
