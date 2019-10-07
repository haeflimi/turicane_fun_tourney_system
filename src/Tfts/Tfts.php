<?php

namespace Tfts;

use Concrete\Core\User\User;
use Concrete\Core\User\Group\Group;
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
        $registration = new Registration($game, $user);
        $this->em->persist($registration);
        return $registration;
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
     * Returns all registrations for the given game.
     * 
     * @param \Tfts\Game $game
     * @return Registration a list of registrations.
     */
    public function getRegistrations(Game $game) {
        return $game->getRegistrations();
    }

    /**
     * @param \Tfts\Game $game
     * @return Match a list of open matches for the given game.
     */
    public function getOpenChallenges(Game $game) {
        // @TODO: filter open challenges
        return $game->getMatches();
    }

    /**
     * @param \Tfts\Game $game
     * @return Match a list of open matches for the given game.
     */
    public function getOpenMatches(Game $game) {
        // @TODO: filter open matches
        return $game->getMatches();
    }

    /**
     * @param \Tfts\Game $game
     * @return Match a list of closed matches for the given game.
     */
    public function getClosedMatches(Game $game) {
        // @TODO: filter closed matches
        return $game->getMatches();
    }

    /**
     * @param User $user
     * @return Match a list of open challenges for the given user.
     */
    public function getOpenUserChallenges(User $user) {
        // @TODO: get open challenges for user
        return null;
    }

    /**
     * @param User $user
     * @return Match a list of open matches for the given user.
     */
    public function getOpenUserMatches(User $user) {
        // @TODO: get open matches for user
        return null;
    }

    /**
     * @param User $user
     * @return Match a list of finished matches for the given user.
     */
    public function getFinishedUserMatches(User $user) {
        // @TODO: get finished matches for user
        return null;
    }

    /**
     * 
     * @return JsonResponse
     */
    public function processTrackmaniaData() {
        // "password=" + PASSWORD + "&date=" + date + "&time=" + time + "&name=" + name + "&record=" + record + "&map=" + map;
        // @TODO: handle data
        $data = [$_REQUEST, 'Data processed!'];
        return new JsonResponse($data);
    }

}
