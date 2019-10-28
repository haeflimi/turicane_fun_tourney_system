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
  <form method="post" name="addSpecialForm" class="form-inline" action="<?php echo $this->action('addSpecial') ?>">
    <tr>
      <td>
        <select name="user_id" class="form-control">
          <option value="0">Select user</option>
          <?php foreach ($users as $user): ?>
            <option value="<?= $user->getUserId() ?>"><?= $user->getUserName() ?></option>
          <?php endforeach; ?>
        </select>
      </td>
      <td><input type="text" name="description" placeholder="Description" class="form-control"/></td>
      <td><input type="text" name="points" placeholder="Points" class="form-control"/></td>
        <td><button class="btn btn-primary" type="submit" value="Add" title="Add"><i class="fa fa-plus-circle"></i></button></td>
    </tr>
  </form>
  <?php foreach ($specials as $special): ?>
    <tr>
      <td><?= $special->getUser()->getUserName() ?></td>
      <td><?= $special->getDescription() ?></td>
      <td><?= $special->getPoints() ?></td>
      <td>
        <form method="post" name="specialForm<?= $special->getUser()->getUserId() ?>" class="form-inline" action="<?php echo $this->action('deleteSpecial') ?>">
          <input type="hidden" name="special_id" value="<?= $special->getId() ?>" /> 
            <button class="btn btn-danger" type="submit" value="Delete" title="Delete"><i class="fa fa-remove"></i></button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
</tbody>
</table>
