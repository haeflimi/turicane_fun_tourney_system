<?php

namespace Concrete\Package\TuricaneFunTourneySystem\Controller\SinglePage\Dashboard\Tfts;

use Concrete\Core\Page\Controller\DashboardPageController;
use Tfts\Game;

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
    foreach ($repository->findAll() as $game) {
      $page = $game->getGamePage();
      if ($page->getAttribute('tfts_game_is_mass')) {
        $games[] = $game;
      }
    }

    $this->set('games', $games);
  }

}
