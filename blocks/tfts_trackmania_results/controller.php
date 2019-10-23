<?php

namespace Concrete\Package\TuricaneFunTourneySystem\Block\TftsTrackmaniaResults;

use Concrete\Core\Block\BlockController;
use Page;
use Tfts\Tfts;
use Tfts\Map;

class Controller extends BlockController {

  public $collection;
  protected $btCacheBlockRecord = true;
  protected $btCacheBlockOutput = true;
  protected $btCacheBlockOutputOnPost = true;
  protected $btCacheBlockOutputForRegisteredUsers = false;
  protected $btCacheBlockOutputLifetime = 300;
  protected $btHandle = 'tfts_trackmania_results';

  public function __construct($obj = null) {
    parent::__construct($obj);
  }

  public function getBlockTypeDescription() {
    return t("");
  }

  public function getBlockTypeName() {
    return t("TFTS Trackmania Results");
  }

  public function save($args) {
    parent::save($args);
  }

  public function view() {
    $app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
    $em = $app->make('Doctrine\ORM\EntityManager');

    $this->requireAsset('javascript', 'vue');
    $this->requireAsset('javascript', 'pusher');
    $this->requireAsset('javascript', 'slimScroll');
    $this->requireAsset('javascript', 'timeago');

    $page = Page::getCurrentPage();
    $game = $em->getRepository('Tfts\Game')->findOneBy(['game_page_id' => $page->getCollectionId()]);

    $tfts = new Tfts();
    $rankingLists = [];
    $repository = $em->getRepository(Map::class);
    foreach ($repository->findBy(['game' => $game]) as $map) {
      $rankingLists[$map->getName()] = $tfts->getMapRankingList($map->getId());
    }
    $this->set('tmRankingLists', $rankingLists);
  }

}
