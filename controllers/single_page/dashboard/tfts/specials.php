<?php
namespace Concrete\Package\TuricaneFunTourneySystem\Controller\SinglePage\Dashboard\Tfts;

use Concrete\Core\Page\Controller\DashboardPageController;
use Tfts\Game;

class Specials extends DashboardPageController
{
    public function __construct($obj = null)
    {
        parent::__construct($obj);

        // Get the Connection to the right MySQL Database. configured in /application/config/database.php
        $this->db = \Database::connection('turicane_fun_tourney_system');
        // Create a Query Builder Instance using our new Connection
        $this->qb = $this->db->createQueryBuilder();
        // The EntityManager is used to work with Doctrine Entities
        $this->em = $this->db->getEntityManager();
    }

    public function getCollectionDescription()
    {
        return t("Manage the Games that are Playable within the Turicane Fun Turney System");
    }

    public function view()
    {
        // https://documentation.concrete5.org/developers/working-with-pages/single-pages/dashboard-pages

        $gameList = $this->em->getRepository('Tfts\Game');

        $this->set('games', $gameList->findAll());
    }

}