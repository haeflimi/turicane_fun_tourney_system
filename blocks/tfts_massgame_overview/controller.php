<?php

namespace Concrete\Package\TuricaneFunTourneySystem\Block\TftsMassgameOverview;

use Concrete\Core\Block\BlockController;
use Concrete\Core\User\User;
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
    return t("TFTS Massgame Overview");
  }

  public function save($args) {
    parent::save($args);
  }

  public function view() {
    $app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
    $em = $app->make('Doctrine\ORM\EntityManager');
    $current_user = new User();
    $tfts = new Tfts();

    $page = Page::getCurrentPage();
    $this->set('is_mass', $page->getAttribute('tfts_game_is_mass'));
    $this->set('is_pool', $page->getAttribute('tfts_game_is_pool'));
    $this->set('is_team', $page->getAttribute('tfts_game_is_team'));

    //get the game from the page the block is inserted in
    $game = $em->getRepository('Tfts\Game')->findOneBy(['game_page_id' => $page->getCollectionId()]);
    $this->set('tfts_game_id', $game->getId());

    $this->set('in_pool', $current_user->isLoggedIn() ? is_object($tfts->findUserRegistration($game, $current_user)) : false);

    $this->set('tfts', $tfts);
    $this->set('current_user', $current_user);
    $this->set('registrations', $game->getRegistrations());
    $this->set('openPools', $game->getOpenPools());
    $this->set('finalPools', $game->getFinalPools());
  }

}
