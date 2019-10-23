<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>
<section id="trackmaniaResults">
  <div class="widget-table-overflow">
    <div class="body slimScroll">
      <?php foreach ($tmRankingLists as $mapName => $rankingList): ?>
        <table class="table table-striped table-lg ranking-table">
          <thead>
            <tr>
              <th colspan="4"><?= $mapName ?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rankingList as $ranking): ?>
              <tr>
                <td><?= $ranking['rank'] ?></td>
                <td>
                  <?php
                  $centis = $ranking['record'] % 100;
                  if ($centis < 10) {
                    $centis = '0' . $centis;
                  }
                  echo date('i:s', $ranking['record'] / 100) . '.' . $centis;
                  ?>
                </td>
                <td><a data-toggle="modal" data-target="#modal"
                       data-source="<?= $ranking['user_profile'] ?>"
                       class="btn btn-round btn-outline-primary btn-sm"><i
                      class="fa fa-user"></i> <?= $ranking['user'] ?></a></td>
                <td><?= $ranking['when'] ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endforeach; ?>
    </div>
  </div>
</section>