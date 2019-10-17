<?php

namespace Concrete\Package\TuricaneFunTourneySystem\Block\TftsPoolOverview;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Page\PageList;
use Concrete\Core\User\User;
use Site;
use Group;
use Core;
use Database;
use Page;
use Permissions;
use \DateTime;
use Tfts\Tfts;
use Concrete\Core\User\Group\GroupList;

class Controller extends BlockController {

  public $collection;
  protected $btCacheBlockRecord = true;
  protected $btCacheBlockOutput = true;
  protected $btCacheBlockOutputOnPost = true;
  protected $btCacheBlockOutputForRegisteredUsers = false;
  protected $btCacheBlockOutputLifetime = 300;
  protected $btHandle = 'tfts_pool_overview';

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

    $page = Page::getCurrentPage();
    $this->set('is_pool', $page->getAttribute('tfts_game_is_pool'));
    $this->set('is_team', $is_team = $page->getAttribute('tfts_game_is_team'));

    //get the game from the page the block is inserted in
    $game = $em->getRepository('Tfts\Game')->findOneBy(['game_page_id' => $page->getCollectionId()]);
    $this->set('tfts_game_id', $game->getId());

    if ($is_team) {
      //get the current users groups to have them available for team signups
      $groupList = new GroupList();
      $groupList->filterByUserID($current_user->getUserID());
      $unregisteredGroups = [];
      $registeredGroups = [];
      //filter groups to have only those where user is team manager
      foreach ($groupList->getResults() as $group) {
        if (strpos($group->getGroupPath(), '/Team Manager/') !== false) {
          if (is_object($tfts->findGroupRegistration($game, $group))) {
            $registeredGroups[] = $group;
          } else {
            $unregisteredGroups[] = $group;
          }
        }
      }
      $this->set('unregisteredGroups', $unregisteredGroups);
      $this->set('registeredGroups', $registeredGroups);
    } else {
      //determine if user is am member of the pool
      $this->set('user_in_pool', is_object($tfts->findUserRegistration($game, $current_user)));
    }

    $this->set('current_user', $current_user);
    $this->set('registrations', $tfts->getRegistrations($game));
    $this->set('openChallenges', $tfts->getOpenGameChallenges($game));
    $this->set('openMatches', $tfts->getOpenMatches($game));
    $this->set('closedMatches', $tfts->getClosedMatches($game));
  }

}
