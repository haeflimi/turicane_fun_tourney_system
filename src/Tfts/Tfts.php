<?php

namespace Tfts;

use Concrete\Core\User\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Common\Collections\Criteria;
use Concrete\Core\Support\Facade\Database;
use Tfts\Entity\Lan;

/**
 * This Class wraps all the important Functionality of the Turicane Fun Tourney System
 *
 * Class Tfts
 * @package TuricaneFunTourneySystem
 */
class Tfts {

    protected $db; // Database Connection we want to use in this Class
    protected $qb; // Doctrine Query Builder
    protected $em; // Doctrine Entity Manager

    public function __construct($obj = null) {
        // Get the Connection to the right MySQL Database. configured in /application/config/database.php
        $this->db = Database::connection('turicane_fun_tourney_system');
        // Create a Query Builder Instance using our new Connection
        $this->qb = $this->db->createQueryBuilder();

        // The EntityManager is used to work with Doctrine Entities, but only works when you let the ORM take
        // care of DB tables - in the default concrete5 database
        $this->em = $this->db->getEntityManager();
    }

    public function getData() {
        return 'Hello World!';
    }

    /**
     * The cleanest way to use the Database is utilizing Doctrine Entities
     * Basic Entities and Relations for the TFTS Tables are alreday in place here:
     * src/Entity
     */
    public function getUserRanking(User $user, Lan $lan) {
        $repository = $this->em->getRepository('Tfts\Entity\Ranking');
        $ranking = $repository->findOneBy(['lan' => $lan->getId(), 'user' => $user->getUserID()]);
        return $ranking;
    }

    /**
     * Example Method that uses a common SQL Query
     *
     * @param string $user
     * @param string $event
     * @return mixed
     */
    public function getUserRankingSQL(User $user, $event = 'Turicane 17') {

        // Get all recorded TFTS Rankings for the User Freezer
        $query = $this->db->executeQuery('SELECT t_user.user_name, t_lan_tfts_ranking.points, t_lan.lan_name
            FROM t_lan_tfts_ranking
            INNER JOIN t_lan ON t_lan_tfts_ranking.lan_id = t_lan.lan_id
            INNER JOIN t_user ON t_lan_tfts_ranking.user_id = t_user.user_id
            WHERE t_user.user_name = ? AND t_lan.lan_name = ?', [$user->getUserName(), $event]);
        $result = $query->fetchAll();
        return $result;
    }

    /**
     * Example Method that uses the Doctrine Query Builder
     *
     * @param string $user
     * @param string $event
     * @return mixed
     */
    public function getUserRankingQueryBuilder(User $user, $event = 'Turicane 17') {
        // Doing the Same thing using Doctrines Query builder takes care of things like SQL injection is one plus of
        // using that method
        // https://www.doctrine-project.org/projects/doctrine-dbal/en/2.9/reference/query-builder.html
        $query = $this->qb
                ->select('u.user_name', 'l.lan_name', 'ltsr.points')
                ->from('t_lan_tfts_ranking', 'ltsr')
                ->join('ltsr', 't_user', 'u', 'u.user_id = ltsr.user_id')
                ->join('ltsr', 't_lan', 'l', 'l.lan_id = ltsr.lan_id')
                ->where('u.user_name = ?')
                ->andWhere('l.lan_name = ?')
                ->setParameter(0, $user->getUserName())
                ->setParameter(1, $event);

        $result = $query->execute()->FetchAll();
        return $result;
    }

    /**
     * The given user wants to join the pool of the given game.
     * 
     * @param \Tfts\Game $game
     * @param User $user
     */
    public function joinUserPool(Game $game, User $user) {
        
    }

    /**
     * The given user wants to leave the pool of the given game.
     * 
     * @param \Tfts\Game $game
     * @param User $user
     */
    public function leaveUserPool(Game $game, User $user) {
        
    }

    /**
     * A user challenges another user for a duel of the given game.
     * 
     * @param \Tfts\Game $game
     * @param User $challenger
     * @param User $challenged
     */
    public function challengeUser(Game $game, User $challenger, User $challenged) {
        
    }

    /**
     * The challenger withdraws the challenge.
     * 
     * @param \Tfts\Match $match
     * @param User $challenger
     */
    public function withdrawUserChallenge(Match $match, User $challenger) {
        
    }

    /**
     * The challenged accepts the challenge.
     * 
     * @param \Tfts\Match $match
     * @param User $challenged
     */
    public function acceptUserChallenge(Match $match, User $challenged) {
        
    }

    /**
     * The challenged declines the challenge.
     * 
     * @param \Tfts\Match $match
     * @param User $challenged
     */
    public function declineUserChallenge(Match $match, User $challenged) {
        
    }

    /**
     * The given players reports the result for the given match.
     * 
     * @param \Tfts\Match $match
     * @param User $player
     * @param type $score1 Score of the challenger.
     * @param type $score2 Score of the challenged.
     */
    public function reportResultUserMatch(Match $match, User $player, $score1, $score2) {
        
    }

    /**
     * The open match is cancelled by the given user (can be done by both players).
     * 
     * @param \Tfts\Match $match
     * @param User $player
     */
    public function cancelUserMatch(Match $match, User $player) {
        
    }

    /**
     * Processes the given match. If both parties have reported the result, the 
     * match is closed and points will be awarded, otherwise this method does 
     * nothing.
     * 
     * @param \Tfts\Match $match
     */
    private function processMatch(Match $match) {
        
    }

    public function processTrackmaniaData() {
        $data = [$_REQUEST, 'Data processed!'];
        return new JsonResponse($data);
    }

    public function addPoints(User $user, $lan_id, $points) {
        $ranking = $this->getUserRanking($user, $this->em->find('Tfts\Entity\Lan', 1));
        $ranking->addPoints($points);
    }

    public function addPointsQB(User $user, $lan_id, $points) {
        // For most older user Accounts that where migrated from the old system, we still have the user
        // id saved as a Attribute
        $ui = $user->getUserInfoObject();
        $oldUserId = $ui->getAttribute('old_user_id');
        if (!empty($oldUserId) && is_numeric($oldUserId)) {

            $qb = $this->db->createQueryBuilder();
            $query = $qb
                    ->update('t_lan_tfts_ranking')
                    ->set('points', 'points + ?')
                    ->where('user_id = ?')
                    ->setParameter(0, (integer) $points)
                    ->setParameter(1, (integer) $oldUserId);
            $query->execute();

            $query = $this->qb
                    ->select('ltsr.points')
                    ->from('t_lan_tfts_ranking', 'ltsr')
                    ->where('ltsr.user_id = ?')
                    ->andWhere('ltsr.lan_id = ?')
                    ->setParameter(0, $user->getUserName())
                    ->setParameter(1, $lan_id);

            $result = $query->execute()->FetchAll();

            return $result;
        }
        return false;
    }
}
