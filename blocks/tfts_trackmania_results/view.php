<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>
<section id="trackmaniaResults">
    <div class="widget-table-overflow">
        <div class="body slimScroll">

            <table class="table table-striped table-lg ranking-table">
                <?php foreach ($tmRankingLists as $mapName => $rankingList): ?>
                    <thead>
                    <tr>
                        <th colspan="3"><?= $mapName ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rankingList as $ranking): ?>
                        <tr>
                            <td><?= $ranking['rank'] ?></td>
                            <td><?= $ranking['record'] ?></td>
                            <td><a data-toggle="modal" data-target="#modal"
                                   data-source="<?= $ranking['user_profile'] ?>"
                                   class="btn btn-round btn-outline-primary btn-sm"><i
                                            class="fa fa-user"></i> <?= $ranking['user'] ?></a></td>
                            <td><?= $ranking['when'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                <?php endforeach; ?>
            </table>

        </div>
    </div>
</section>