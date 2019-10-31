<?php

namespace Concrete\Package\TuricaneFunTourneySystem\Block\TftsTeamManager;

use Concrete\Core\Block\BlockController;
use Concrete\Core\User\User;
use Concrete\Core\Support\Facade\Config;
use Core;
use Tfts\Tfts;

class Controller extends BlockController {

  public $collection;
  protected $btCacheBlockRecord = true;
  protected $btCacheBlockOutput = true;
  protected $btCacheBlockOutputOnPost = true;
  protected $btCacheBlockOutputForRegisteredUsers = false;
  protected $btCacheBlockOutputLifetime = 300;
  protected $btHandle = 'tfts_team_manager';

  public function __construct($obj = null) {
    parent::__construct($obj);
  }

  public function getBlockTypeDescription() {
    return t("");
  }

  public function getBlockTypeName() {
    return t("TFTS Team Manager");
  }

  public function save($args) {
    parent::save($args);
  }

  public function view() {
    $current_user = new User();
    $tfts = new Tfts();
    $this->set('current_user', $current_user);
    $this->set('groups', $tfts->getAllGroups());
    $this->set('userGroups', $tfts->getAllUserGroups($current_user));
    $this->set('allowGroupCreation', Config::get('tfts.systemActive'));
  }

}
