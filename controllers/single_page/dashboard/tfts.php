<?php

namespace Concrete\Package\TuricaneFunTourneySystem\Controller\SinglePage\Dashboard;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Support\Facade\Application;
use Tfts\Lan;

class Tfts extends DashboardPageController {

  public function __construct($obj = null) {
    parent::__construct($obj);
    $this->db = \Database::connection();
    $this->qb = $this->db->createQueryBuilder();
    $this->em = $this->db->getEntityManager();
  }

  public function getCollectionDescription() {
    return t("Manage the configuration of the Turicane Fun Turney System");
  }

  public function view() {
    $app = Application::getFacadeApplication();
    $config = $app->make('config');
    $this->set('currentLanId', $config->get('tfts.currentLanId'));
    $this->set('systemActive', $config->get('tfts.systemActive'));
    $this->set('maxUserVsUser', $config->get('tfts.maxUserVsUser'));
    $this->set('maxTeamVsTeam', $config->get('tfts.maxTeamVsTeam'));
    $this->set('trackmaniaApiPassword', $config->get('tfts.trackmaniaApiPassword'));

    $repository = $this->em->getRepository(Lan::class);
    $this->set('lans', $repository->findAll());
  }

  public function save() {
    $app = Application::getFacadeApplication();
    $config = $app->make('config');
    $config->save('tfts.currentLanId', $this->post('currentLanId'));
    $config->save('tfts.systemActive', $this->post('systemActive') == 1 ? 1 : 0);
    $config->save('tfts.maxUserVsUser', $this->post('maxUserVsUser'));
    $config->save('tfts.maxTeamVsTeam', $this->post('maxTeamVsTeam'));
    $config->save('tfts.trackmaniaApiPassword', $this->post('trackmaniaApiPassword'));

    $page = \Page::getCurrentPage();
    $this->redirect($page->getCollectionPath());
  }

}
