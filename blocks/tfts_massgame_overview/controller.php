<?php

namespace Concrete\Package\TuricaneFunTourneySystem\Block\TftsMassgameOverview;

use Concrete\Core\Block\BlockController;
use Concrete\Core\User\Group\GroupList;
use Concrete\Core\User\User;
use Tfts\Game;
use Page;
use Tfts\Tfts;

class Controller extends BlockController {

  public $collection;
  protected $btCacheBlockRecord = true;
  protected $btCacheBlockOutput = true;
  protected $btCacheBlockOutputOnPost = true;
  protected $btCacheBlockOutputForRegisteredUsers = false;
  protected $btCacheBlockOutputLifetime = 300;
  protected $btHandle = 'tfts_massgame_overview';

  public function __construct($obj = null) {
    parent::__construct($obj);
  }

  public function getBlockTypeDescription() {
    return t("");
  }

  public function getBlockTypeName() {
    return t("TFTS Pool Overview");
  }

  public function save($args) {
    parent::save($args);
  }

  public function view() {
    $app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
    $em = $app->make('Doctrine\ORM\EntityManager');
    $current_user = new User();
    $tfts = new Tfts();

    $g = new Game();
    $g->getOpenPools();

    $this->set('current_user', $current_user);
    $this->set('registrations', []);
    $this->set('openChallenges', []);
    $this->set('openMatches', []);
    $this->set('closedMatches', []);
  }

}
