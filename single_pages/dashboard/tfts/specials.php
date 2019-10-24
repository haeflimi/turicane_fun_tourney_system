<?php defined('C5_EXECUTE') or die("Access Denied.");
?>

<table class="table table-striped table-condensed">
  <thead>
    <tr>
      <th>Player</th>
      <th>Description</th>
      <th>Points</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
  <form method="post" name="addSpecialForm" action="<?php echo $this->action('addSpecial') ?>">
    <tr>
      <td>
        <select name="user_id">
          <option value="0">Select user</option>
          <?php foreach ($users as $user): ?>
            <option value="<?= $user->getUserId() ?>"><?= $user->getUserName() ?></option>
          <?php endforeach; ?>
        </select>
      </td>
      <td><input type="text" name="description" /></td>
      <td><input type="text" name="points" value="0" /></td>
      <td><input type="submit" value="Add" /></td>
    </tr>
  </form>
  <?php foreach ($specials as $special): ?>
    <tr>
      <td><?= $special->getUser()->getUserName() ?></td>
      <td><?= $special->getDescription() ?></td>
      <td><?= $special->getPoints() ?></td>
      <td>
        <form method="post" name="specialForm<?= $special->getUser()->getUserId() ?>" action="<?php echo $this->action('deleteSpecial') ?>">
          <input type="hidden" name="special_id" value="<?= $special->getId() ?>" /> 
          <input type="submit" value="Delete" />
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
</tbody>
</table>
