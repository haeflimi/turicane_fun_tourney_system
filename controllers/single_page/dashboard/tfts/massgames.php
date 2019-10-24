<?php

namespace Concrete\Package\TuricaneFunTourneySystem\Controller\SinglePage\Dashboard\Tfts;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Support\Facade\Config;
use Tfts\Game;
use Tfts\Tfts;

class Massgames extends DashboardPageController {

  public function __construct($obj = null) {
    parent::__construct($obj);
    $this->db = \Database::connection();
    $this->qb = $this->db->createQueryBuilder();
    $this->em = $this->db->getEntityManager();
  }

  public function getCollectionDescription() {
    return t("Manage the massgames within the Turicane Fun Turney System");
  }

  public function view() {
    $repository = $this->em->getRepository(Game::class);
    $games = [];
    foreach ($repository->findBy(['lan' => Config::get('tfts.currentLanId')]) as $game) {
      $page = $game->getGamePage();
      if ($page->getAttribute('tfts_game_is_mass')) {
        $games[] = $game;
      }
    }
    $this->set('games', $games);
  }

  public function createPools() {
    $tfts = new Tfts();
    try {
      $tfts->createPools($this->post('game_id'), $this->post('count'));
    } catch (Exception $ex) {
      // @TODO: show exception in a user-readable form
      throw $ex;
    }
    $this->reloadPage();
  }

  public function updateRanks() {
    $tfts = new Tfts();
    try {
      foreach ($this->post('ranks') as $poolId => $userIds) {
        foreach ($userIds as $userId => $rank) {
          $tfts->setPoolRank($poolId, $userId, $rank);
        }
      }
    } catch (Exception $ex) {
      // @TODO: show exception in a user-readable form
      throw $ex;
    }
    $this->reloadPage();
  }

  public function processPools() {
    $tfts = new Tfts();
    try {
      $tfts->processPools($this->post('game_id'), $this->post('count'), $this->post('rank'));
    } catch (Exception $ex) {
      // @TODO: show exception in a user-readable form
      throw $ex;
    }
    $this->reloadPage();
  }

  public function processFinalPool() {
    $tfts = new Tfts();
    try {
      $tfts->processFinalPool($this->post('game_id'));
    } catch (Exception $ex) {
      // @TODO: show exception in a user-readable form
      throw $ex;
    }
    $this->reloadPage();
  }

  private function reloadPage() {
    $page = \Page::getCurrentPage();
    $this->redirect($page->getCollectionPath());
  }

}
