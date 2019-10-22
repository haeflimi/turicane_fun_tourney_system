<?php
namespace Concrete\Package\TuricaneFunTourneySystem\Controller\SinglePage\Dashboard;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Support\Facade\Application;
use Tfts\Lan;

class Tfts extends DashboardPageController
{
    public function __construct($obj = null)
    {
        parent::__construct($obj);
        $this->db = \Database::connection('turicane_fun_tourney_system');
        $this->qb = $this->db->createQueryBuilder();
        $this->em = $this->db->getEntityManager();
    }

    public function getCollectionDescription()
    {
        return t("Manage the configuration of the Turicane Fun Turney System");
    }

    public function view()
    {
      $app = Application::getFacadeApplication();
      $this->set('config', $app->make('config'));
      
      $repository = $this->em->getRepository(Lan::class);
      $this->set('lans', $repository->findAll());
    }

}