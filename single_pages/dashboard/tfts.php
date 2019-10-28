<?php defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="ccm-ui"><h1>TFTS Settings</h1>
    <form id="tftsSettingsForm" method="POST" action="<?= $this->action('save') ?>">
        <table class="table table-striped">
            <tbody>
            <tr>
                <th>Current LAN:</th>
                <td>
                    <select name="currentLanId" class="form-control">
                        <option></option>
                        <?php foreach ($lans as $lan): ?>
                            <option value="<?= $lan->getId() ?>"<?= $lan->getId() == $currentLanId ? ' selected' : '' ?>><?= $lan->getLanPage()->getCollectionName() ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>System active:</th>
                <td><input type="checkbox" name="systemActive" class="form-control"
                           value="1"<?= $systemActive == 1 ? ' checked="checked"' : '' ?>/></td>
            </tr>
            <tr>
                <th>Ranking snapshots:</th>
                <td><input type="checkbox" name="rankingSnapshots" class="form-control"
                           value="1"<?= $rankingSnapshots == 1 ? ' checked="checked"' : '' ?>/></td>
            </tr>
            <tr>
                <th>Max user vs. user:</th>
                <td><input type="text" name="maxUserVsUser" class="form-control" value="<?= $maxUserVsUser ?>"/></td>
            </tr>
            <tr>
                <th>Max team vs. team:</th>
                <td><input type="text" name="maxTeamVsTeam" class="form-control" value="<?= $maxTeamVsTeam ?>"/></td>
            </tr>
            <tr>
                <th>Map API password:</th>
                <td><input type="text" name="mapApiPassword" class="form-control" value="<?= $mapApiPassword ?>"/></td>
            </tr>
            </tbody>
        </table>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <button class="pull-right btn btn-success" type="submit"><?= t('Save') ?></button>
            </div>
        </div>
    </form>
</div>