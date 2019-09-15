<?php
defined('C5_EXECUTE') or die('Access Denied.');

$fh = Core::make('helper/form');

/*
 * This file is part of Block Boilerplate.
 *
 * (c) Oliver Green <oliver@c5labs.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

?>
<div class="block-boilerplate" style="">
    <h2>Result List Block</h2>

    <p>Here we can access the set Data from the controller and use it to build beautiful interfaces.:</p>
    <div class="row">

        <div class="col">
            <h3>Bsp.1 Freezer</h3>
            <?= var_dump($result) ?>
        </div>
        <div class="col">
            <h3>Bsp.2 Buddha</h3>
            <?= var_dump($result2) ?>
        </div>
        <div class="col">
            <h3>Bsp. 4 Add Points</h3>
            <form action="<?=$this->action('addPoints')?>" method="POST">
                <?=$fh->number('points')?>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <h3>User Object:</h3>
            </div>
        <div class="col">
            </div>
        <div class="col">
            </div>
    </div>
</div>