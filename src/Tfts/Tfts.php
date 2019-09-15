<?php
namespace TuricaneTfts;

use Symfony\Component\HttpFoundation\JsonResponse;
use Concrete\Core\Package\Package;
use Concrete\Core\Support\Facade\Database;

/**
 * This Class wraps all the important Functionality of the Turicane Fun Tourney System
 *
 * Class Tfts
 * @package TuricaneTfts
 */
class Tfts
{
    protected $db; // Database Connection we want to use in this Class
    protected $qb; // Doctrine Query Builder
    protected $em; // Doctrine Entity Manager

    public function __construct($obj = null)
    {
        parent::__construct($obj);

        // Get the Connection to the right MySQL Database. configured in /application/config/database.php
        $this->db = Database::connection('turicane_tfts');
        // Create a Query Builder Instance using our new Connection
        $this->qb = $this->db->createQueryBuilder();
        // The EntityManager is used to work with Doctrine Entities
        $pkg = Package::getByHandle('turicane_tfts');
        //$orm = \Core::make(\Concrete\Core\Support\Facade\DatabaseORM::getFacadeAccessor());
        $this->em = $this->db->getEntityManager();
    }

    public function processRankingList($list){

        return $list;

    }

    public function getData() {
        return 'Hello World!';
    }

    /**
     * Example Method that uses a common SQL Query
     *
     * @param string $user
     * @param string $event
     * @return mixed
     */
    public function getUserRankingSQL($user = 'Freezer', $event = 'Turicane 17'){

        // Get all recorded TFTS Rankings for the User Freezer
        $query = $this->db->executeQuery('SELECT t_user.user_name, t_lan_tfts_ranking.points, t_lan.lan_name
            FROM t_lan_tfts_ranking
            INNER JOIN t_lan ON t_lan_tfts_ranking.lan_id = t_lan.lan_id
            INNER JOIN t_user ON t_lan_tfts_ranking.user_id = t_user.user_id
            WHERE t_user.user_name = "'.$user.'" AND t_lan.lan_name = "'.$event.'"');
        $result = $query->fetchAll();
        return $result;
    }

    public function getUserRanikingQueryBuilder($user = 'Freezer', $event = 'Turicane 17')
    {
        // Doing the Same thing using Doctrines Query builder takes care of things like SQL injection is one plus of
        // using that method
        $query = $this->qb
            ->select('u.user_name', 'l.lan_name', 'ltsr.points')
            ->from('t_lan_tfts_ranking', 'ltsr')
            ->join('ltsr','t_user', 'u', 'u.user_id = ltsr.user_id')
            ->join('ltsr', 't_lan', 'l', 'l.lan_id = ltsr.lan_id')
            ->where('u.user_name = ?')
            ->andWhere('l.lan_name = ?')
            ->setParameter(0, $user)
            ->setParameter(1, $event);
        ;
        $result = $query->execute()->FetchAll();
        return $result;
    }

    public function processTrackmaniaData()
    {
        $data = [$_REQUEST,'Data processed!'];
        return new JsonResponse($data);
    }
}