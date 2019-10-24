<?php

namespace Concrete\Package\TuricaneFunTourneySystem\Controller\SinglePage\Dashboard\Tfts;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Support\Facade\Config;
use Tfts\Special;
use Tfts\Tfts;

class Specials extends DashboardPageController {

  public function __construct($obj = null) {
    parent::__construct($obj);
    $this->db = \Database::connection();
    $this->qb = $this->db->createQueryBuilder();
    $this->em = $this->db->getEntityManager();
  }

  public function getCollectionDescription() {
    return t("Manage the specials within the Turicane Fun Turney System");
  }

  public function view() {
    $repository = $this->em->getRepository(Special::class);

    $this->set('specials', $repository->findBy(['lan' => Config::get('tfts.currentLanId')]));
  }

  public function deleteSpecial() {
    $tfts = new Tfts();
    try {
      $tfts->deleteSpecial($this->post('special_id'));
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
