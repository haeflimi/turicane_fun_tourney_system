<?php

namespace Tfts;

use Concrete\Core\Support\Facade\Config;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\User;
use Concrete\Core\User\UserList;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tfts\Entity\Lan;
use Tfts\Entity\Game;
use Tfts\Entity\Registration;
use Tfts\Entity\Match;
use Tfts\Entity\Ranking;
use Tfts\Entity\Map;
use Tfts\Entity\Trackmania;
use Tfts\Entity\Special;

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
   * The given user wants to join the pool of the given game.
   *
   * @param \Tfts\Game $game
   * @param User $user
   * @return boolean true if the join was successful, false otherwise.
   */
  public function joinUserPool($game = false, $user = false) {
      if(!$game && !$user && $this->validateRequest($_POST, $_POST['action'])){
          $user = User::getByUserID($_POST['user_id']);
          $game = $this->em->find('Tfts\Entity\Game',$_POST['game_id']);
      }

    // verify system is active
    if (!$this->isSystemActive()) {
      // @TODO: throw exception?
      return false;
    }

    // verify user is not already registered
    if (!is_null($this->findRegistration($game, $user))) {
      // @TODO: throw exception?
      return false;
    }

    $registration = new Registration($game, $this->userToEntity($user));
    $this->em->persist($registration);
    $this->em->flush();
    return true;
  }

  /**
   * The given user wants to leave the pool of the given game.
   * 
   * @param \Tfts\Game $game
   * @param User $user
   * @return boolean true if the leave was successful, false otherwise.
   */
  public function leaveUserPool(Game $game, User $user) {
    // verify system is active
    if (!$this->isSystemActive()) {
      // @TODO: throw exception?
      return false;
    }

    $registration = $this->findRegistration($game, $user);
    // verify user is registered
    if (is_null($registration)) {
      // @TODO: throw exception?
      return false;
    }

    $this->em->remove($registration);
    $this->em->flush();
    return true;
  }

  /**
   * A user challenges another user for a duel of the given game.
   * 
   * @param \Tfts\Game $game
   * @param User $challenger
   * @param User $challenged
   * @return Match null if an exception occured, the created match otherwise.
   */
  public function challengeUser(Game $game, User $challenger, User $challenged) {
    // verify system is active
    if (!$this->isSystemActive()) {
      // @TODO: throw exception?
      return null;
    }

    // @TODO: check max games against another player
    // verify both users are registered for that game
    if (is_null($this->findRegistration($game, $challenger)) || is_null($this->findRegistration($game, $challenged))) {
      // @TODO: throw exception?
      return null;
    }

    $repository = $this->em->getRepository('Tfts\Entity\Match');
    // verify challenge cannot be triggered twice
    if (!is_null($repository->findOneBy(['user1' => $challenger->getUserId(), 'user2' => $challenged->getUserId(), 'match_finish_date' => null]))) {
      // @TODO: throw exception?
      return null;
    }

    // verify challenged hasn't already challenged the challenger
    if (!is_null($repository->findOneBy(['user1' => $challenged->getUserId(), 'user2' => $challenger->getUserId(), 'match_finish_date' => null]))) {
      // @TODO: throw exception?
      return null;
    }

    $match = new Match($game);
    $match->setUsers($this->userToEntity($challenger), $this->userToEntity($challenged));
    $this->em->persist($match);
    $this->em->flush();
    return $match;
  }

  /**
   * The challenger withdraws the challenge.
   * 
   * @param \Tfts\Match $match
   * @param User $challenger
   * @return boolean true if the withdraw was successful, false otherwise.
   */
  public function withdrawUserChallenge(Match $match, User $challenger) {
    // verify system is active
    if (!$this->isSystemActive()) {
      // @TODO: throw exception?
      return false;
    }

    $repository = $this->em->getRepository('Tfts\Entity\Match');
    // verify match exists and user is challenger
    if (is_null($repository->findOneBy(['match_id' => $match, 'user1' => $challenger->getUserId()]))) {
      // @TODO: throw exception?
      return false;
    }

    $this->em->remove($match);
    $this->em->flush();
    return true;
  }

  /**
   * The challenged accepts the challenge.
   * 
   * @param \Tfts\Match $match
   * @param User $challenged
   * @return boolean true if the accept was successful, false otherwise.
   */
  public function acceptUserChallenge(Match $match, User $challenged) {
    // verify system is active
    if (!$this->isSystemActive()) {
      // @TODO: throw exception?
      return false;
    }

    $repository = $this->em->getRepository('Tfts\Entity\Match');
    // verify match exists and user is challenged
    if (is_null($repository->findOneBy(['match_id' => $match, 'user2' => $challenged->getUserId()]))) {
      // @TODO: throw exception?
      return false;
    }

    $match->setAccepted(true);
    $this->em->persist($match);
    $this->em->flush();
    return true;
  }

  /**
   * The challenged declines the challenge.
   * 
   * @param \Tfts\Match $match
   * @param User $challenged
   * @return boolean true if the decline was successful, false otherwise.
   */
  public function declineUserChallenge(Match $match, User $challenged) {
    // verify system is active
    if (!$this->isSystemActive()) {
      // @TODO: throw exception?
      return false;
    }

    $repository = $this->em->getRepository('Tfts\Entity\Match');
    // verify match exists and user is challenged
    if (is_null($repository->findOneBy(['match_id' => $match, 'user2' => $challenged->getUserId()]))) {
      // @TODO: throw exception?
      return false;
    }

    $this->em->remove($match);
    $this->em->flush();
    return true;
  }

  /**
   * The given user reports the result for the given match.
   * 
   * @param \Tfts\Match $match
   * @param User $user
   * @param type $score1 Score of the challenger.
   * @param type $score2 Score of the challenged.
   */
  public function reportResultUserMatch(Match $match, User $user, $score1, $score2) {
    // verify system is active
    if (!$this->isSystemActive()) {
      // @TODO: throw exception?
      return false;
    }

    $db_match = $this->getUserMatch($match, $user);
    if ($db_match == null) {
      return;
    }

    $is_challenger = $match->getUser1()->getUserId() == $user->getUserId();
    $is_challenged = $match->getUser2()->getUserId() == $user->getUserId();

    $this->updateMatch($db_match, $is_challenger, $is_challenged, $score1, $score2);
    $this->processMatch($db_match);
    $this->em->persist($db_match);
    $this->em->flush();
    return true;
  }

  /**
   * The open match is cancelled by the given user (can be done by both players).
   * 
   * @param \Tfts\Match $match
   * @param User $user
   */
  public function cancelUserMatch(Match $match, User $user) {
    // verify system is active
    if (!$this->isSystemActive()) {
      // @TODO: throw exception?
      return false;
    }

    $db_match = $this->getUserMatch($match, $user);
    if ($db_match == null) {
      return;
    }

    $this->em->remove($db_match);
    $this->em->flush();
    return true;
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
   * @param User $user
   * @return int the user rank for the current lan.
   */
  public function getUserRank(User $user) {
    $rankings = $this->getLan()->getRankings()->toArray();
    usort($rankings, 'Tfts\Entity\Ranking::compare');

    $rank = 0;
    $last_points = 0;
    $skipped = 0;

    foreach ($rankings as $ranking) {
      if ($last_points == $ranking->getPoints()) {
        $skipped++;
      } else {
        $rank = $rank + 1 + $skipped;
        $skipped = 0;
      }
      if ($ranking->getUser()->getUserId() == $user->getUserId()) {
        return $rank;
      }
      $last_points = $ranking->getPoints();
    }
    return 0;
  }

  public function getTrackmaniaRank(Map $map, User $user) {
    $trackmanias = $map->getTrackmanias()->toArray();
    usort($trackmanias, 'Tfts\Entity\Trackmania::compare');

    $rank = 0;
    $last_record = 0;
    $skipped = 0;

    foreach ($trackmanias as $trackmania) {
      if ($last_record == $trackmania->getRecord()) {
        $skipped++;
      } else {
        $rank = $rank + 1 + $skipped;
        $skipped = 0;
      }
      if ($trackmania->getUser()->getUserId() == $user->getUserId()) {
        return $rank;
      }
      $last_record = $trackmania->getRecord();
    }
    return 0;
  }

  /**
   * Adds the given points to the given user.
   *
   * @param User $user
   * @param int $points
   */
  public function addPoints(User $user, int $points) {
    $repository = $this->em->getRepository('Tfts\Entity\Ranking');
    $ranking = $repository->findOneBy(['lan' => $this->getLan(), 'user' => $user->getUserId()]);
    if (is_null($ranking)) {
      $ranking = new Ranking($this->getLan(), $this->userToEntity($user));
    }

    $ranking->setPoints($ranking->getPoints() + $points);
    $this->em->persist($ranking);
    $this->em->flush();
    return true;
  }

  /**
   * Processes the data provided which is considered to be a trackmania result.
   * In order to be processed, the password must match and the user must exist.
   *
   * @return JsonResponse result of the process.
   */
  public function processTrackmaniaData() {
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    if ($password != Config::get('tfts.trackmaniaApiPassword')) {
      // @TODO: exit here once the config values are updated
//      return new JsonResponse('Invalid password');
    }

    // verify that user exists
    $user_name = filter_input(INPUT_POST, 'user', FILTER_SANITIZE_STRING);
    $userList = new UserList();
    $userList->filterByUserName($user_name);
    if (sizeof($userList->getResults()) != 1) {
      return new JsonResponse('Invalid user: ' . $user_name);
    }
    $user = $userList->getResults()[0]->getUserObject();

    // create map if necessary
    $map_name = filter_input(INPUT_POST, 'map_name', FILTER_SANITIZE_STRING);
    $map_repository = $this->em->getRepository('Tfts\Entity\Map');
    $map = $map_repository->findOneBy(['lan' => $this->getLan(), 'map_name' => $map_name]);
    if (is_null($map)) {
      $map = new Map($this->getLan(), $map_name);
      $this->em->persist($map);
      $this->em->flush();
    }

    // prepare datetime object
    $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
    $time = filter_input(INPUT_POST, 'time', FILTER_SANITIZE_STRING);
    $datetime = new \DateTime($date . ' ' . $time);

    // split record to milliseconds
    $record = filter_input(INPUT_POST, 'record', FILTER_SANITIZE_STRING);
    $split = explode(".", $record);
    $milliseconds = intval($split[1]);
    $hms = explode(":", $split[0]);
    switch (sizeof($hms)) {
      case 1:
        $milliseconds += intval($hms[0]) * 1000;
        break;
      case 2:
        $milliseconds += (intval($hms[0]) * 60 + intval($hms[1])) * 1000;
        break;
      case 3:
        $milliseconds += (intval($hms[0]) * 3600 + intval($hms[1]) * 60 + intval($hms[3])) * 1000;
        break;
      default:
        return new JsonResponse('Invalid record: ' . $record);
    }

    $trackmania_repository = $this->em->getRepository('Tfts\Entity\Trackmania');
    $trackmania = $trackmania_repository->findOneBy(['map' => $map, 'user' => $user->getUserId()]);
    // new?
    if (is_null($trackmania)) {
      $this->em->persist(new Trackmania($this->userToEntity($user), $map, $datetime, $milliseconds));
      $this->em->flush();
      return new JsonResponse('Added new record');
    }

    // improvement?
    if ($milliseconds < $trackmania->getRecord()) {
      $trackmania->setDateTime($datetime);
      $trackmania->setRecord($milliseconds);
      $this->em->persist($trackmania);
      $this->em->flush();
      return new JsonResponse('Record improved');
    }

    return new JsonResponse('No improvement');
  }

  /**
   * Processes the given map. Every user that was able to set a record is
   * awarded with 1 point. The best users will be awarded with more points:
   *
   * 1st = 25
   * 2nd = 18
   * 3rd = 15
   * 4th = 12
   * 5th = 10
   * 6th = 8
   * 7th = 6
   * 8th = 4
   * 9th = 3
   * 10th= 2
   *
   * @param Map $map
   * @return bool true if the map was processed, false otherwise.
   */
  public function processMap(Map $map) {
    $db_map = $this->em->find('Tfts\Entity\Map', $map->getId());

    // verify map exists
    if (is_null($db_map)) {
      // @TODO: throw exception?
      return false;
    }

    // verify map hasn't been processed
    if ($db_map->isProcessed()) {
      return false;
    }

    $awardedPoints = array('default' => 1, 1 => 25, 2 => 18, 3 => 15, 4 => 12, 5 => 10, 6 => 8, 7 => 6, 8 => 4, 9 => 3, 10 => 2);
    foreach ($db_map->getTrackmanias() as $trackmania) {
      $rank = $this->getTrackmaniaRank($db_map, $this->entityToUser($trackmania->getUser()));
      if ($rank == 0) {
        continue;
      }

      $points = array_key_exists($rank, $awardedPoints) ? $awardedPoints[$rank] : $awardedPoints['default'];
      $description = 'Teilnahme Trackmania (#' . $rank . ', ' . $db_map->getName() . ')';

      $this->em->persist(new Special($db_map->getLan(), $trackmania->getUser(), $description, $points));
      $this->addPoints($this->entityToUser($trackmania->getUser()), $points);
    }

    $db_map->setProcessed(true);
    $this->em->persist($db_map);
    $this->em->flush();
    return true;
  }

  /**
   * @return bool true if the TFTS system is active, false otherwise.
   */
  private function isSystemActive() {
    return true;
//    return filter_var(Config::get('tfts.systemActive'), FILTER_VALIDATE_BOOLEAN);
  }

  /**
   * @return Lan the current lan object.
   */
  private function getLan() {
    return $this->em->find('Tfts\Entity\Lan', Config::get('tfts.currentLanId'));
  }

  /**
   * Finds a registration for given game, user and group composition.
   *
   * @param Game $game
   * @param User $user
   * @param Group $group
   * @return Registration
   */
  private function findRegistration(Game $game, User $user = null, Group $group = null) {
    $repository = $this->em->getRepository('Tfts\Entity\Registration');

    // verify that only one and one only of user and group is set
    if (($user == null && $group == null) || ($user != null && $group != null)) {
      // @TODO: throw exception?
      return null;
    }

    $e_user = $user == null ? null : $this->userToEntity($user);
    $group_id = $group == null ? null : $group->getPermissionObjectIdentifier();
    return $repository->findOneBy(['game' => $game, 'user' => $e_user, 'group_id' => $group_id]);
  }

  /**
   * @param \Concrete\Core\Entity\User\User $entity
   * @return User the user matching the given entity.
   */
  private function entityToUser(\Concrete\Core\Entity\User\User $entity) {
    $user_list = new UserList();
    $user_list->filterByUserName($entity->getUserName());
    return $user_list->getResults()[0]->getUserObject();
  }

  /**
   * @param User $user
   * @return Concrete\Core\Entity\User\User the entity matching the given user.
   */
  private function userToEntity(User $user) {
    $repository = $this->em->getRepository('Concrete\Core\Entity\User\User');
    return $repository->findOneBy(['uID' => $user->getUserId()]);
  }

  /**
   * Loads the match from the database and verifies that it exists and the user is a part of it.
   *
   * @param Match $match
   * @param User $user
   * @return Match the updated match entity from the database.
   */
  private function getUserMatch(Match $match, User $user) {
    $is_challenger = $match->getUser1()->getUserId() == $user->getUserId();
    $is_challenged = $match->getUser2()->getUserId() == $user->getUserId();

    $repository = $this->em->getRepository('Tfts\Entity\Match');
    $db_match = $repository->findOneBy(['match_id' => $match, ($is_challenger ? 'user1' : 'user2') => $user->getUserId()]);
    // verify match exists
    if (is_null($db_match)) {
      // @TODO: throw exception?
      return null;
    }

    // verify user is challenger or challenged
    if (!$is_challenger && !$is_challenged) {
      // @TODO: throw exception?
      return null;
    }

    // verify match is not closed
    if ($db_match->getFinishDate() != null) {
      // @TODO: throw exception?
      return null;
    }

    return $db_match;
  }

  /**
   * Updates the given match with the scores and sets correct confirmed flags.
   *
   * @param Match $match
   * @param bool $is_challenger
   * @param bool $is_challenged
   * @param int $score1
   * @param int $score2
   * @return boolean
   */
  private function updateMatch(Match $match, bool $is_challenger, bool $is_challenged, int $score1, int $score2) {
    $first = !$match->isConfirmed1() && !$match->isConfirmed2();
    $second = ($match->isConfirmed1() && $is_challenged) || ($match->isConfirmed2() && $is_challenger);
    $confirm = $match->getScore1() == $score1 && $match->getScore2() == $score2;

    // first one to report or other result than opponent
    if ($first || ($second && !$confirm)) {
      $match->setScore1($score1);
      $match->setScore2($score2);
      $match->setConfirmed1($is_challenger);
      $match->setConfirmed2($is_challenged);
    }
    // second one to report and confirm result
    else if ($second && $confirm) {
      $match->setConfirmed1(true);
      $match->setConfirmed2(true);
    }
    // update result
    else {
      $match->setScore1($score1);
      $match->setScore2($score2);
    }
  }

  /**
   * Processes the given match. If both parties have confirmed the scores, the
   * match is closed and points will be awarded, otherwise this method does
   * nothing.
   *
   * @param Match $match
   */
  private function processMatch(Match $match) {
    // match not finished - do nothing
    if (!$match->isConfirmed1() || !$match->isConfirmed2()) {
      return;
    }

    // @TODO: differ between solo and team game
    $rank1 = $this->getUserRank($this->entityToUser($match->getUser1()));
    $rank2 = $this->getUserRank($this->entityToUser($match->getUser2()));

    $rank_diff = ($rank1 - $rank2) / 100;
    if ($rank_diff > 0.7) {
      $rank_diff = 0.7;
    }
    if ($rank_diff < -0.7) {
      $rank_diff = -0.7;
    }

    $points_win = $match->getGame()->getPointsWin();
    $points_loss = $match->getGame()->getPointsLoss();

    // user 1 has won
    if ($match->getscore1() > $match->getscore2()) {
      $compute1 = $points_win + round($points_win * $rank_diff, 0);
      $compute2 = $points_loss - round($points_loss * $rank_diff, 0);
    }
    // draw
    else if ($match->getscore1() == $match->getscore2()) {
      $compute1 = ($points_win / 2) + round(($points_win / 2) * $rank_diff, 0);
      $compute2 = ($points_win / 2) - round(($points_win / 2) * $rank_diff, 0);
    }
    // user 2 has won
    else if ($match->getscore1() < $match->getscore2()) {
      $compute1 = $points_loss + round($points_loss * $rank_diff, 0);
      $compute2 = $points_win - round($points_win * $rank_diff, 0);
    }

    $match->setFinished(true);
    $match->setCompute1($compute1);
    $match->setCompute2($compute2);

    // @TODO: differ between solo and team game
    $this->addPoints($this->entityToUser($match->getUser1()), $compute1);
    $this->addPoints($this->entityToUser($match->getUser2()), $compute2);
  }

    /**
     * Validate a Post Request for a token
     *
     * @param $data
     * @param bool $action
     * @return bool|\Concrete\Core\Error\Error
     */
    public function validateRequest($data, $action = false) {
        $errors = new \Concrete\Core\Error\Error();

        // we want to use a token to validate each call in order to protect from xss and request forgery
        $token = \Core::make("token");
        if ($action && !$token->validate($action)) {
            $errors->add('Invalid Request, token must be valid.');
        }

        if ($errors->has()) {
            return $errors;
        }

        return true;
    }
}
