<?php defined('C5_EXECUTE') or die('Access Denied.');
$fh = Core::make('helper/form')?>
<div class="block-boilerplate" style="">
    <h2>Result List Block</h2>



    <p>Here we can access the set Data from the controller and use it to build beautiful interfaces.:</p>
    <div class="">

        <div class="card">
            <div class="card-body">
                <h3>Bsp.1 Freezer</h3>
                <pre><code><?= var_dump($bsp1) ?></code></pre>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h3>Bsp.2 TuBorg</h3>
                <pre><?= var_dump($bsp2) ?></pre>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h3>Bsp.3</h3>
                <pre><?= var_dump($bsp3) ?></pre>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h3>Bsp. 4 Add Points</h3>
                <form class="form-inline add-point-form" action="<?=$this->action('addPoints')?>" method="POST">
                    <input type="hidden" name="ccm_token" value="<?=Core::make('token')->generate('addPoints');?>"/>
                    <?=$fh->number('pointsValue')?>&nbsp;&nbsp;
                    <?=$fh->submit('addPoints', 'Add! (normal Form Submit)')?>&nbsp;&nbsp;
                    <?=$fh->submit('addPointsAjax', 'Add! (ajax Request)')?>
                </form>
                <br/>
                <div id="add-point-alert" class="alert alert-warning alert-dismissible fade show" <?=($showMsg)?'':'style="display:none;"'?> role="alert">
                    <strong>Holy guacamole!</strong><br/>Those Points have been added and the User
                    has now <strong id="bsp4_points"><?=$bsp4_points?></strong> Points.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <?php if($errors):;?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error:</strong><br/>
                        <?=$errors->output();?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h3>Bsp. 5 User Data</h3>
                <pre><?= var_dump($bsp5) ?></pre>
                <p class="">More Data about the user (like c5 Attributes) can be fetched via the User Info Object:
                    <code>$user->getUserInfoObject()->getAttribute('old_user_id')</code></p>
            </div>
        </div>

    </div>
</div>