<?php

namespace Concrete\Package\TuricaneFunTourneySystem\Controller\SinglePage\Dashboard\Tfts;

use Concrete\Core\Page\Controller\DashboardPageController;
use Tfts\Game;

class Games extends DashboardPageController {

  public function __construct($obj = null) {
    parent::__construct($obj);
    $this->db = \Database::connection('turicane_fun_tourney_system');
    $this->qb = $this->db->createQueryBuilder();
    $this->em = $this->db->getEntityManager();
  }

  public function getCollectionDescription() {
    return t("Manage the games within the Turicane Fun Turney System");
  }

  public function view() {
    $repository = $this->em->getRepository(Game::class);

    $this->set('games', $repository->findAll());
  }

}
