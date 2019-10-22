<?php

namespace Concrete\Package\TuricaneFunTourneySystem\Block\TftsLiveRanking;

use Concrete\Core\Block\BlockController;
use Tfts\Tfts;

class Controller extends BlockController {

  public $collection;
  protected $btCacheBlockRecord = true;
  protected $btCacheBlockOutput = true;
  protected $btCacheBlockOutputOnPost = true;
  protected $btCacheBlockOutputForRegisteredUsers = false;
  protected $btCacheBlockOutputLifetime = 300;
  protected $btHandle = 'tfts_live_ranking';

  public function __construct($obj = null) {
    parent::__construct($obj);
  }

  public function getBlockTypeDescription() {
    return t("");
  }

  public function getBlockTypeName() {
    return t("TFTS Live Ranking");
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

    $this->set('rankingList', $tfts->getRankingList());
  }

}
