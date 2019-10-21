<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>
<section id="trackmaniaResults">
    <header>
        <h2>Trackmania Results</h2>
    </header>
    <div class="widget-table-overflow">
        <div class="body slimScroll">
            <?php foreach ($tmRankingLists as $name => $rl): ?>
                <h3><?= $name ?></h3>
                <table class="table table-striped table-lg ranking-table">
                    <thead>
                    <tr>
                        <th width="75">Rank</th>
                        <th width="50">Lap Time</th>
                        <th width="*">Nickname</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($rl as $rang => $r):?>
                    <tr>
                        <td><?= $rang ?></td>
                        <td><?=$r['lap_time']?></td>
                        <td><a data-toggle="modal" data-target="#modal" data-source="/members/profile/<?= $r['user']->getUserID() ?>"
                               class="btn btn-round btn-outline-primary btn-sm"><i
                                        class="fa fa-user"></i> <?= $r['user']->getUserName() ?></a></td>
                    </tr>
                    <?php endforeach;; ?>
                    </tbody>
                </table>
            <?php endforeach; ?>
            </template>
        </div>
    </div>
</section>