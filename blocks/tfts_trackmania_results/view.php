<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>
<section id="trackmaniaResults">
    <div class="widget-table-overflow">
        <div class="body slimScroll">

            <table class="table table-striped table-lg ranking-table">
                <?php foreach ($tmRankingLists as $name => $rl): ?>
                    <thead>
                    <tr>
                        <th colspan="3"><?= $name ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rl as $rang => $r): ?>
                        <tr>
                            <td><?= $rang ?></td>
                            <td><?= $r['lap_time'] ?></td>
                            <td><a data-toggle="modal" data-target="#modal"
                                   data-source="/members/profile/<?= $r['user']->getUserID() ?>"
                                   class="btn btn-round btn-outline-primary btn-sm"><i
                                            class="fa fa-user"></i> <?= $r['user']->getUserName() ?></a></td>
                        </tr>
                    <?php endforeach;; ?>
                    </tbody>
                <?php endforeach; ?>
            </table>

        </div>
    </div>
</section>