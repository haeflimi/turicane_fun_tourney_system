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

    protected $em; // Doctrine Entity Manager

    public function __construct($obj = null) {
        $app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
        $this->em = $app->make('Doctrine\ORM\EntityManager');
    }

    /**
     * Get a List of all participating Users with Points& a Rank
     *
     * src/Entity
     */
    public function getUserRanking(Lan $lan) {
        $repository = $this->em->getRepository('Tfts\Entity\Ranking');
        $ranking = $repository->findBy(['lan' => $lan->getId()]);
        return $ranking;
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

    /**
     * Return Teams/ Players that are visible/ selectable on the pool overview
     * of a certain game.
     *
     * @param Game $game
     */
    public function getPoolMembers(Game $game) {

    }

    /**
     * Get all pending Matches and open Challanges of a certain Game
     *
     * @param Team $team
     * @param Game $game
     */
    public function getPendingGameMatches(Game $game) {

    }

    /**
     * Get all finalised Matches of a certain game for display on game Page
     *
     * @param Game $game
     */
    public function getFinalGameMatches(Game $game) {

    }

    /**
     * Get all Pending Matches of a specific User and all the teams and massgames of this user for Display
     * on LAN Dashboard
     *
     * @param User $user
     */
    public function getPendingUserMatches(User $user) {

    }

    /**
     * Process the Trackmania Ranking data that that is arriving over the webhook and write it
     * to the database, updating the raningk
     *
     * @return JsonResponse
     */
    public function processTrackmaniaData() {
        $data = [$_REQUEST, 'Data processed!'];
        return new JsonResponse($data);
    }
}
