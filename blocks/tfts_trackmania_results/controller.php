<?php

namespace Concrete\Package\TuricaneFunTourneySystem\Block\TftsTrackmaniaResults;

use Concrete\Core\Block\BlockController;
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
    $this->requireAsset('javascript', 'vue');
    $this->requireAsset('javascript', 'pusher');
    $this->requireAsset('javascript', 'slimScroll');
    $this->requireAsset('javascript', 'timeago');
    $tfts = new Tfts();

    $rankingLists = [];
    $repository = $this->em->getRepository(Map::class);
    foreach ($repository->findAll() as $map) {
      $rankingLists[$map->getName()] = $tfts->getTrackmaniaRankingList($map->getId());
    }
    $this->set('tmRankingLists', $rankingLists);
  }

}
