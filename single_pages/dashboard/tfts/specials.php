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
