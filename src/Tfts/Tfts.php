<?php

namespace Tfts;

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Config;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\User;
use Concrete\Core\User\UserList;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Exception;

/**
 * This Class wraps all the important Functionality of the Turicane Fun Tourney System
 *
 * Class Tfts
 */
class Tfts {

  protected $em; // Doctrine Entity Manager

  public function __construct($obj = null) {
    $app = Application::getFacadeApplication();
    $this->em = $app->make('Doctrine\ORM\EntityManager');
  }

  /**
   * Returns all registrations for the given game.
   *
   * @param \Tfts\Game $game
   * @return Registration a list of registrations.
   */
  public function getRegistrations(Game $game): Collection {
    return $game->getRegistrations();
  }

  /**
   * @param \Tfts\Game $game
   * @return Match a list of open matches for the given game.
   */
  public function getOpenGameChallenges(Game $game): Collection {
    return $game->getOpenChallenges();
  }


  /**
   * @param \Tfts\Game $game
   * @return Match a list of open matches for the given game.
   */
  public function getOpenMatches(Game $game): Collection {
    return $game->getOpenMatches();
  }

  /**
   * @param \Tfts\Game $game
   * @return Match a list of closed matches for the given game.
   */
  public function getClosedMatches(Game $game): Collection {
    return $game->getClosedMatches();
  }

  /**
   * @param User $user
   * @return Match a list of open challenges for the given user.
   */
    public function getOpenUserChallenges(User $user): Array
    {
        //@todo this also needs to include the challanges to the teams the user is a member of
        $repo = $this->em->getRepository('Tfts\Match');
        return $repo->findBy(['user2'=>$user->getUserID(),'match_accepted'=>0]);
    }

    /**
     * @param User $user
     * @return Match a list of open challenges for the given user.
     */
    public function getOpenUserConfirmations(User $user): Array
    {
        //@todo this also needs to include the confirmation request to the teams the user is a member of
        $repo = $this->em->getRepository('Tfts\Match');
        $challenger = $repo->findBy(['user1'=>$user->getUserID(),'match_accepted'=>1,'match_confirmed1'=>0,'match_confirmed2'=>1]);
        $challenged = $repo->findBy(['user2'=>$user->getUserID(),'match_accepted'=>1,'match_confirmed1'=>1,'match_confirmed2'=>0]);
        return array_merge($challenger, $challenged);
    }

  /**
   * @param User $user
   * @return Match a list of open matches for the given user.
   */
  public function getOpenUserMatches(User $user): Collection {
    // @TODO: get open matches for user
    return null;
  }

  /**
   * @param User $user
   * @return Match a list of finished matches for the given user.
   */
  public function getFinishedUserMatches(User $user): Collection {
    // @TODO: get finished matches for user
    return null;
  }

  /**
   * @param Group $group
   * @return Match a list of open challenges for the given group.
   */
  public function getOpenGroupChallenges(Group $group): Collection {
    // @TODO: get open challenges for user
    return null;
  }

  /**
   * @param Group $group
   * @return Match a list of open matches for the given group.
   */
  public function getOpenGroupMatches(Group $group): Collection {
    // @TODO: get open matches for user
    return null;
  }

  /**
   * @param Group $group
   * @return Match a list of finished matches for the given group.
   */
  public function getFinishedGroupMatches(Group $group): Collection {
    // @TODO: get finished matches for user
    return null;
  }

  /**
   * The given user wants to join the pool of the given game.
   *
   * @param \Tfts\Game $game
   * @param User $user
   * @return bool true if the join was successful, false otherwise.
   */
  public function joinUserPool($game_id, $user_id): bool {

      $user = User::getByUserID($user_id);
      $game = $this->em->find(Game::class, $game_id);

    // verify system is active
    if (!$this->isSystemActive()) {
        throw new Exception("Something bad happened"); //@TODO: let me know hat happened
      return false;
    }

    // verify game
    if (!$game->isPool() || $game->isGroup()) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
      return false;
    }

    // verify user is not already registered
    if (!is_null($this->findRegistration($game, $user))) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
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
   * @return bool true if the leave was successful, false otherwise.
   */
  public function leaveUserPool($game_id, $user_id): bool {

      $user = User::getByUserID($user_id);
      $game = $this->em->find(Game::class, $game_id);

    // verify system is active
    if (!$this->isSystemActive()) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
      return false;
    }

    $registration = $this->findRegistration($game, $user);
    // verify user is registered
    if (is_null($registration)) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
      return false;
    }

    $this->em->remove($registration);
    $this->em->flush();
    return true;
  }

    /**
     * A user challenges another user for a match.
     *
     * @param \Tfts\Game $game
     * @param User $challenger
     * @param User $challenged
     * @return Match null if an exception occured, the created match otherwise.
     */
    public function challengeUser($game_id, $challenger_id, $challenged_id): Match
    {
        $game = $this->em->find(Game::class, $game_id);
        $challenger = User::getByUserID($challenger_id);
        $challenged = User::getByUserID($challenged_id);

        // verify system is active
        if (!$this->isSystemActive()) {
            throw new Exception("Something bad happened"); //@TODO: let me know hat happened
            return null;
        }

        // @TODO: check max games against another player
        // verify both users are registered for that game
        if (is_null($this->findRegistration($game, $challenger)) || is_null($this->findRegistration($game, $challenged))) {
            throw new Exception("Something bad happened"); //@TODO: let me know hat happened
            return null;
        }

        $repository = $this->em->getRepository(Match::class);
        // verify challenge cannot be triggered twice
        if (!is_null($repository->findOneBy(['user1' => $challenger->getUserId(), 'user2' => $challenged->getUserId(), 'match_finish_date' => null]))) {
            throw new Exception("You already have a open Match with this Player"); //@TODO: let me know hat happened
            return null;
        }

        // verify challenged hasn't already challenged the challenger
        if (!is_null($repository->findOneBy(['user1' => $challenged->getUserId(), 'user2' => $challenger->getUserId(), 'match_finish_date' => null]))) {
            throw new Exception("You have already challenged that Player"); //@TODO: let me know hat happened
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
   * @return bool true if the withdraw was successful, false otherwise.
   */
  public function withdrawUserChallenge(Match $match, User $challenger): bool {
    // verify system is active
    if (!$this->isSystemActive()) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
      return false;
    }

    $repository = $this->em->getRepository(Match::class);
    // verify match exists and user is challenger
    if (is_null($repository->findOneBy(['match_id' => $match, 'user1' => $challenger->getUserId()]))) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
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
     * @return bool true if the accept was successful, false otherwise.
     */
    public function acceptUserChallenge($match_id, $user_id): bool
    {
        // verify system is active
        if (!$this->isSystemActive()) {
            throw new Exception("Something bad happened"); //@TODO: let me know hat happened
            return false;
        }

        $match = $this->em->find('Tfts\Match', $match_id);

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
   * @return bool true if the decline was successful, false otherwise.
   */
  public function declineUserChallenge($match_id, $user_id): bool {
    // verify system is active
    if (!$this->isSystemActive()) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
      return false;
    }

    $match = $this->em->find(Match::class, $match_id);
    // verify match exists and user is challenged
    if ($match->getUser2()->getUserID() != $user_id) {
      throw new Exception($match->getUser2()->getUserID()." - Something bad happened ".$user_id); //@TODO: let me know hat happened
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
   * @return bool true if the report was successful, false otherwise.
   */
  public function reportResultUserMatch($match_id, $user_id, $user1_score, $user2_score): bool {
      $user = User::getByUserID($user_id);;
      $match = $this->em->find('Tfts\Match', $match_id);

    // verify system is active
    if (!$this->isSystemActive()) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
      return false;
    }

    $db_match = $this->getUserMatch($match, $user);
    if ($db_match == null) {
      return false;
    }

    $is_challenger = $match->getUser1()->getUserId() == $user->getUserId();
    $is_challenged = $match->getUser2()->getUserId() == $user->getUserId();

    $this->updateMatch($match, $is_challenger, $is_challenged, $user1_score, $user2_score);
    $this->processMatch($db_match);
    $this->em->persist($db_match);
    $this->em->flush();
    return true;
  }

  public function confirmResultUserMatch($match_id, $user_id): bool {
      $match = $this->em->find('Tfts\Match', $match_id);

      $is_challenger = $match->getUser1()->getUserId() == $user_id;
      $is_challenged = $match->getUser2()->getUserId() == $user_id;

      $this->updateMatch($match, $is_challenger, $is_challenged, $match->getScore1(), $match->getScore2());
      $this->processMatch($match);
      $this->em->persist($match);
      $this->em->flush();
      return truen;
  }

    /**
     * What to do if a user does not confirm a match result
     *
     * @param $match_id
     * @param $user_id
     * @return bool
     */
    public function declineResultUserMatch($match_id, $user_id): bool {
        //@todo What to do here?
        throw new Exception("This is not implemented yet. What should happen here?");
        return false;
    }

  /**
   * The open match is cancelled by the given user (can be done by both players).
   *
   * @param \Tfts\Match $match
   * @param User $user
   * @return bool true if the cancel was successful, false otherwise.
   */
  public function cancelUserMatch(Match $match, User $user): bool {
    // verify system is active
    if (!$this->isSystemActive()) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
      return false;
    }

    $db_match = $this->getUserMatch($match, $user);
    if ($db_match == null) {
      return false;
    }

    $this->em->remove($db_match);
    $this->em->flush();
    return true;
  }

  /**
   * The given group wants to join the pool of the given game.
   *
   * @param \Tfts\Game $game
   * @param Group $group
   * @return bool true if the join was successful, false otherwise.
   */
  public function joinGroupPool($game_id, $group_id): bool {
      $game = $this->em->find('Tfts\Game', $game_id);
      $group = Group::getByID($group_id);

    // verify system is active
    if (!$this->isSystemActive()) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
      return false;
    }

    // verify game
    if (!$game->isPool() || !$game->isGroup()) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
      return false;
    }

    // verify group has enough members
    if ($group->getGroupMembersNum() < $game->getGroupSize()) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
      return false;
    }

    // verify group is not already registered
    if (!is_null($this->findRegistration($game, null, $group))) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
      return false;
    }

    $registration = new Registration($game, null, $group->getGroupId());
    $this->em->persist($registration);
    $this->em->flush();
    return true;
  }

  /**
   * The given group wants to leave the pool of the given game.
   *
   * @param Game $game
   * @param Group $group
   * @return bool true if the leave was successful, false otherwise.
   */
  public function leaveGroupPool($game_id, $group_id): bool {
      $game = $this->em->find('Tfts\Game', $game_id);
      $group = Group::getByID($group_id);

    // verify system is active
    if (!$this->isSystemActive()) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
      return false;
    }

    $registration = $this->findRegistration($game, null, $group);
    // verify group is registered
    if (is_null($registration)) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
      return false;
    }

    $this->em->remove($registration);
    $this->em->flush();
    return true;
  }

  /**
   * A group challenges another group for a match.
   *
   * @param \Tfts\Game $game
   * @param User $challenger
   * @param User $challenged
   * @return Match null if an exception occured, the created match otherwise.
   */
  public function challengeGroup($game_id, $challenger_id, $challenged_id): ?Match {
      $game = $this->em->find('Tfts\Game', $game_id);
      $challenger = Group::getByID($challenger_id);
      $challenged = Group::getByID($challenged_id);

    // verify system is active
    if (!$this->isSystemActive()) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
      return null;
    }

    // @TODO: check max games against another group
    // verify both users are registered for that game
    if (is_null($this->findRegistration($game, null, $challenger)) || is_null($this->findRegistration($game, null, $challenged))) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
      return null;
    }

    $repository = $this->em->getRepository(Match::class);
    // verify challenge cannot be triggered twice
    if (!is_null($repository->findOneBy(['group1_id' => $challenger->getGroupId(), 'group2_id' => $challenged->getGroupId(), 'match_finish_date' => null]))) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
      return null;
    }

    // verify challenged hasn't already challenged the challenger
    if (!is_null($repository->findOneBy(['group1_id' => $challenged->getGroupId(), 'group2_id' => $challenger->getGroupId(), 'match_finish_date' => null]))) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
      return null;
    }

    $match = new Match($game);
    $match->setGroups($challenger->getGroupId(), $challenged->getGroupId());
    $this->em->persist($match);
    $this->em->flush();
    return $match;
  }

  /**
   * The challenger withdraws the challenge.
   *
   * @param \Tfts\Match $match
   * @param Group $challenger
   * @return bool true if the withdraw was successful, false otherwise.
   */
  public function withdrawGroupChallenge(Match $match, Group $challenger): bool {
    // verify system is active
    if (!$this->isSystemActive()) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
      return false;
    }

    $repository = $this->em->getRepository(Match::class);
    // verify match exists and user is challenger
    if (is_null($repository->findOneBy(['match_id' => $match, 'group1_id' => $challenger->getGroupId()]))) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
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
   * @param Group $challenged
   * @return bool true if the accept was successful, false otherwise.
   */
  public function acceptGroupChallenge($match_id, $challenged_id): bool {
    // verify system is active
    if (!$this->isSystemActive()) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
      return false;
    }
    $match = $this->em->find('Match', $match_id);
    $repository = $this->em->getRepository(Match::class);

    // verify match exists and group is challenged
    if (is_null($repository->findOneBy(['match_id' => $match_id, 'group2_id' => $challenged_id]))) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
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
   * @param Group $challenged
   * @return bool true if the decline was successful, false otherwise.
   */
  public function declineGroupChallenge($match_id, $challenged_id): bool {
    // verify system is active
    if (!$this->isSystemActive()) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
      return false;
    }
      $match = $this->em->find('Match', $match_id);
    $repository = $this->em->getRepository(Match::class);
    // verify match exists and group is challenged
    if (is_null($repository->findOneBy(['match_id' => $match_id, 'group2_id' => $challenged_id]))) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
      return false;
    }

    $this->em->remove($match);
    $this->em->flush();
    return true;
  }

  /**
   * The given group reports the result for the given match. It also has to
   * provide a list of users that actually participated in the match.
   *
   * @param \Tfts\Match $match
   * @param Group $group
   * @param Collection $users
   * @param type $score1 Score of the challenger.
   * @param type $score2 Score of the challenged.
   * @return bool true if the report was successful, false otherwise.
   */
  public function reportResultGroupMatch($match_id, $group_id, $score1, $score2): bool {
      $match = $this->em->find('Tfts\Match',$match_id);
      $group = Group::getByID($group_id);
      $users = $group->getGroupMembers();

    // verify system is active
    if (!$this->isSystemActive()) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
      return false;
    }

    $db_match = $this->getGroupMatch($match, $group);
    if ($db_match == null) {
      return false;
    }

    // verify the correct amount of users is provided
    if ($db_match->getGame()->getGroupSize() != sizeof($users)) {
      return false;
    }

    // verify users are members of the group
    foreach ($users as $user) {
      if (!$user->inGroup($group)) {
        throw new Exception("Something bad happened"); //@TODO: let me know hat happened
        return false;
      }
    }

    // remove existing group match users
    foreach ($this->getMatchGroupUsers($match, $group->getGroupId()) as $matchGroupUser) {
      if ($matchGroupUser->getGroupId() == $group->getGroupId()) {
        $this->em->remove($matchGroupUser);
      }
    }
    $this->em->flush();

    // create new group match users
    foreach ($users as $user) {
      $this->em->persist(new MatchGroupUser($match, $this->userToEntity($user), $group->getGroupId()));
    }
    $this->em->flush();

    $is_challenger = $match->getGroup1Id() == $group->getGroupId();
    $is_challenged = $match->getGroup2Id() == $group->getGroupId();

    $this->updateMatch($db_match, $is_challenger, $is_challenged, $score1, $score2);
    $this->processMatch($db_match);
    $this->em->persist($db_match);
    $this->em->flush();
    return true;
  }

  /**
   * The open match is cancelled by the given group (can be done by both groups).
   *
   * @param \Tfts\Match $match
   * @param Group $group
   * @return bool true if the cancel was successful, false otherwise.
   */
  public function cancelGroupMatch(Match $match, Group $group): bool {
    // verify system is active
    if (!$this->isSystemActive()) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
      return false;
    }

    $db_match = $this->getGroupMatch($match, $group);
    if ($db_match == null) {
      return false;
    }

    $this->em->remove($db_match);
    $this->em->flush();
    return true;
  }

  /**
   * @return Snapshot|null the latest snapshot or null.
   */
  public function getLatestSnapshot(): ?Snapshot {
    $repository = $this->em->getRepository(Snapshot::class);
    $snapshots = $repository->findBy(['lan' => $this->getLan()], ['snapshot_datetime' => 'DESC']);
    return sizeof($snapshots) == 0 ? null : $snapshots[0];
  }

  /**
   * Creates a snapshot of the current ranking. Only one snapshot per hour is allowed.
   *
   * @return bool true if the snapshot was created, false otherwise.
   */
  public function createRankingSnapshot(): bool {
    $rankings = $this->getLan()->getRankings();
    // check we have a ranking first
    if (sizeof($rankings) == 0) {
      return false;
    }

    // verify last snapshot happened at least over an hour ago
    $dateTime = new \DateTime("now");
    $latestSnapshot = $this->getLatestSnapshot();
    if (!is_null($latestSnapshot) && $dateTime->diff($latestSnapshot->getDateTime(), true)->h < 1) {
      return false;
    }

    $snapshot = new Snapshot($this->getLan(), $dateTime);
    $this->em->persist($snapshot);
    foreach ($rankings as $ranking) {
      $this->em->persist(new RankingSnapshot($ranking, $snapshot, $this->getUserRank($this->entityToUser($ranking->getUser()))));
    }
    $this->em->flush();
    return true;
  }

  /**
   * @param User $user
   * @return int the rank movement since the last ranking snapshot.
   */
  public function getRankMovement(User $user): int {
    $currentRank = $this->getUserRank($user);
    $previousRank = $this->getUserRank($user, $this->getLatestSnapshot());
    return $previousRank - $currentRank;
  }

  /**
   *
   * @param User $user
   * @param Snapshot|null $snapshot
   * @return int the user rank for the current lan.
   */
  public function getUserRank(User $user, Snapshot $snapshot = null): int {
    if (is_null($snapshot)) {
      $rankings = $this->getLan()->getRankings()->toArray();
      usort($rankings, 'Tfts\Ranking::compare');
    } else {
      $snapshot = $this->getLatestSnapshot();
      if (is_null($snapshot)) {
        return $this->getUserRank($user);
      }

      foreach ($snapshot->getRankingSnapshots() as $rankingSnapshot) {
        if ($rankingSnapshot->getRanking()->getUser()->getUserId() == $user->getUserId()) {
          return $rankingSnapshot->getRank();
        }
      }
      return 0;
    }

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

  /**
   * @param Map $map
   * @param User $user
   * @return int the user rank for the given map.
   */
  public function getTrackmaniaRank(Map $map, User $user): int {
    $trackmanias = $map->getTrackmanias()->toArray();
    usort($trackmanias, 'Tfts\Trackmania::compare');

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
   * @return bool true if the points have been added, false otherwise.
   */
  public function addPoints(User $user, int $points): bool {
    $repository = $this->em->getRepository(Ranking::class);
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
  public function processTrackmaniaData(): JsonResponse {
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
    $map_repository = $this->em->getRepository(Map::class);
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

    $trackmania_repository = $this->em->getRepository(Trackmania::class);
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
  public function processMap(Map $map): bool {
    $db_map = $this->em->find(Map::class, $map->getId());

    // verify map exists
    if (is_null($db_map)) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
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
   * Creates the given amount of pools for the given game and fills them with
   * registered users.
   *
   * @param Game $game
   * @param int $count
   * @return bool true if the pools were created, false otherwise.
   */
  public function createPools(Game $game, int $count): bool {
    if (!$game->isMass()) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
      return false;
    }

    // verify there are no open pools
    if (sizeof($game->getOpenPools()) > 0) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
      return false;
    }

    // sort registrations for shuffeling reasons
    $registrations = $game->getRegistrations()->toArray();
    usort($registrations, 'Tfts\Registration::compare');

    // read users from registrations
    $users = new ArrayCollection();
    foreach ($registrations as $registration) {
      $users->add($registration->getUser());
    }

    $this->createAndFillPools($game, $count, $users);

    // delete registrations
    foreach ($registrations as $registration) {
      $this->em->remove($registration);
    }

    $this->em->flush();
    return true;
  }

  /**
   * The open pools of the given game will be processed and the requested amount
   * of new pools will be created. The rank is the requirement for a user to
   * proceed. If a user drops out, 3 points will be awarded.
   *
   * @param Game $game
   * @param int $count
   * @param int $rank
   * @return bool true if the pools were processed, false otherwise.
   */
  public function processPools(Game $game, int $count, int $rank): bool {
    if (!$game->isMass()) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
      return false;
    }

    // verify there are at least 2 open pools
    $oldPools = $game->getOpenPools();
    if (sizeof($oldPools) < 2) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
      return false;
    }

    $usersToAdvance = new ArrayCollection();

    // filter users for next round & award points to dropped out users
    foreach ($oldPools as $oldPool) {
      foreach ($oldPool->getUsers() as $poolUser) {
        if ($poolUser->getRank() != 0 && $poolUser->getRank() <= $rank) {
          $usersToAdvance->add($poolUser->getUser());
        } else {
          $this->em->persist(new Special($this->getLan(), $poolUser->getUser(), 'Teilnahme ' . $game->getName(), 3));
          $this->addPoints($this->entityToUser($poolUser->getUser()), 3);
        }
      }
    }

    $pools = $this->createAndFillPools($game, $count, $usersToAdvance);
    // ensure hirarchy and mark old pools as played
    foreach ($oldPools as $oldPool) {
      foreach ($pools as $pool) {
        $oldPool->setPlayed(true);
        $oldPool->addChild($pool);
        $pool->addParent($oldPool);

        $this->em->persist($oldPool);
        $this->em->persist($pool);
      }
    }
    $this->em->flush();
    return true;
  }

  /**
   * If the game has only one pool left, finish it.
   *
   * @param Game $game
   * @return bool true if the final pool was processed, false otherwise.
   */
  public function processFinalPool(Game $game): bool {
    if (!$game->isMass()) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
      return false;
    }

    // verify there's only one pool left
    $pools = $game->getOpenPools();
    if (sizeof($pools) != 1) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
      return false;
    }

    $awardedPoints = array('default' => 3, 1 => 15, 2 => 12, 3 => 10, 4 => 8, 5 => 7, 6 => 6, 7 => 5, 8 => 4);
    $finalPool = $pools->first();
    foreach ($finalPool->getUsers() as $poolUser) {
      $points = array_key_exists($poolUser->getRank(), $awardedPoints) ? $awardedPoints[$poolUser->getRank()] : $awardedPoints['default'];
      $this->em->persist(new Special($this->getLan(), $poolUser->getUser(), 'Finale ' . $game->getName() . ' (#' . $poolUser->getRank() . ')', $points));
      $this->addPoints($this->entityToUser($poolUser->getUser()), $points);
    }
    $finalPool->setPlayed(true);
    $this->em->persist($finalPool);
    $this->em->flush();
    return true;
  }

  /**
   * Sets the rank for the given user if and only if they belong to the pool.
   *
   * @param Pool $pool
   * @param User $user
   * @param int $rank
   * @return bool true if the rank has been set, false otherwise.
   */
  public function setPoolRank(Pool $pool, User $user, int $rank): bool {
    // verify that user belongs to pool
    $poolUser = $pool->getPoolUser($this->userToEntity($user));
    if (is_null($poolUser)) {
      return false;
    }

    $poolUser->setRank($rank);
    $this->em->persist($poolUser);
    $this->em->flush();
    return true;
  }

  /**
   * @return bool true if the TFTS system is active, false otherwise.
   */
  private function isSystemActive(): bool {
    return true;
//    return filter_var(Config::get('tfts.systemActive'), FILTER_VALIDATE_BOOLEAN);
  }

  /**
   * @return Lan the current lan object.
   */
  private function getLan(): Lan {
    return $this->em->find(Lan::class, Config::get('tfts.currentLanId'));
  }

  /**
   * Finds a registration for given game, user and group composition.
   *
   * @param Game $game
   * @param User $user
   * @param Group $group
   * @return Registration
   */
  public function findRegistration(Game $game, User $user = null, Group $group = null): ?Registration {
      //@Todo This should work without supplying a Group Parameter to do the find prefix justice and is not very useful like this
    $repository = $this->em->getRepository(Registration::class);

    // verify that only one and one only of user and group is set
    if (($user == null && $group == null) || ($user != null && $group != null)) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
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
  private function entityToUser(\Concrete\Core\Entity\User\User $entity): User {
    $user_list = new UserList();
    $user_list->filterByUserName($entity->getUserName());
    return $user_list->getResults()[0]->getUserObject();
  }

  /**
   * @param User $user
   * @return Concrete\Core\Entity\User\User the entity matching the given user.
   */
  private function userToEntity(User $user): \Concrete\Core\Entity\User\User {
    $repository = $this->em->getRepository(\Concrete\Core\Entity\User\User::class);
    return $repository->findOneBy(['uID' => $user->getUserId()]);
  }

  /**
   * Loads the match from the database and verifies that it exists and the user is a part of it.
   *
   * @param Match $match
   * @param User $user
   * @return Match the updated match entity from the database.
   */
  private function getUserMatch(Match $match, User $user): ?Match {
    return $this->getMatch($match, $match->getUser1()->getUserId(), $match->getUser2()->getUserId(), $user->getUserId(), 'user1', 'user2');
  }

  /**
   * Loads the match from the database and verifies that it exists and the group is a part of it.
   *
   * @param Match $match
   * @param Group $group
   * @return Match the updated match entity from the database.
   */
  private function getGroupMatch(Match $match, Group $group): ?Match {
    return $this->getMatch($match, $match->getGroup1Id(), $match->getGroup2Id(), $group->getGroupId(), 'group1_id', 'group2_id');
  }

  /**
   * Loads the match from the database and verifies that it exists and the identifier is a part of it.
   *
   * @param Match $match
   * @param int $challengerId
   * @param int $challengedId
   * @param int $id
   * @param \Tfts\String $challenger_field
   * @param \Tfts\String $challenged_field
   * @return Match|null
   */
  private function getMatch(Match $match, int $challengerId, int $challengedId, int $id, String $challenger_field, String $challenged_field): ?Match {
    $is_challenger = $challengerId == $id;
    $is_challenged = $challengedId == $id;

    $repository = $this->em->getRepository(Match::class);
    $db_match = $repository->findOneBy(['match_id' => $match, ($is_challenger ? $challenger_field : $challenged_field) => $id]);
    // verify match exists
    if (is_null($db_match)) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
      return null;
    }

    // verify user is challenger or challenged
    if (!$is_challenger && !$is_challenged) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
      return null;
    }

    // verify match is not closed
    if ($db_match->getFinishDate() != null) {
      throw new Exception("Something bad happened"); //@TODO: let me know hat happened
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

    if ($match->getGame()->isGroup()) {
      $rank1 = $this->getGroupRank($match, $match->getGroup1Id());
      $rank2 = $this->getGroupRank($match, $match->getGroup1Id());
    } else {
      $rank1 = $this->getUserRank($this->entityToUser($match->getUser1()));
      $rank2 = $this->getUserRank($this->entityToUser($match->getUser2()));
    }

    $rank_diff = ($rank1 - $rank2) / 100.0;
    if ($rank_diff > 0.7) {
      $rank_diff = 0.7;
    }
    if ($rank_diff < -0.7) {
      $rank_diff = -0.7;
    }

    $points_win = $match->getGame()->getPointsWin();
    $points_loss = $match->getGame()->getPointsLoss();

    // user 1 has won
    if ($match->getScore1() > $match->getScore2()) {
      $compute1 = $points_win + round($points_win * $rank_diff, 0);
      $compute2 = $points_loss - round($points_loss * $rank_diff, 0);
    }
    // draw
    else if ($match->getScore1() == $match->getScore2()) {
      $compute1 = ($points_win / 2) + round(($points_win / 2) * $rank_diff, 0);
      $compute2 = ($points_win / 2) - round(($points_win / 2) * $rank_diff, 0);
    }
    // user 2 has won
    else if ($match->getScore1() < $match->getScore2()) {
      $compute1 = $points_loss + round($points_loss * $rank_diff, 0);
      $compute2 = $points_win - round($points_win * $rank_diff, 0);
    }

    $match->setFinished(true);
    $match->setCompute1($compute1);
    $match->setCompute2($compute2);

    if ($match->getGame()->isGroup()) {
      foreach ($this->getMatchGroupUsers($match, $match->getGroup1Id()) as $matchGroupUser) {
        $this->addPoints($this->entityToUser($matchGroupUser->getUser()), $compute1);
      }
      foreach ($this->getMatchGroupUsers($match, $match->getGroup2Id()) as $matchGroupUser) {
        $this->addPoints($this->entityToUser($matchGroupUser->getUser()), $compute2);
      }
    } else {
      $this->addPoints($this->entityToUser($match->getUser1()), $compute1);
      $this->addPoints($this->entityToUser($match->getUser2()), $compute2);
    }
  }

  /**
   * @param type $match
   * @param int $group_id
   * @return int the group rank for the current lan.
   */
  private function getGroupRank($match, int $group_id): int {
    $matchGroupUsers = $this->getMatchGroupUsers($match, $group_id);
    $rank = 0;
    foreach ($matchGroupUsers as $matchGroupUser) {
      $rank += $this->getUserRank($this->entityToUser($matchGroupUser->getUser()));
    }
    return $rank / sizeof($matchGroupUsers);
  }

  /**
   * @param type $match
   * @param int $group_id
   * @return Collection a list of match group users.
   */
  private function getMatchGroupUsers($match, int $group_id): Collection {
    $repository = $this->em->getRepository(MatchGroupUser::class);
    return new ArrayCollection($repository->findBy(['match' => $match, 'group_id' => $group_id]));
  }

  /**
   * Creates the given amount of pools and fills the users in it.
   *
   * @param Game $game
   * @param int $count
   * @param Collection $users
   * @return Collection the created pools
   */
  private function createAndFillPools(Game $game, int $count, Collection $users): Collection {
    $pools = new ArrayCollection();

    // create
    for ($idx = 1; $idx <= $count; $idx++) {
      $pool = new Pool($game, 'Pool ' . $idx);
      $this->em->persist($pool);
      $pools->add($pool);
    }

    // fill
    for ($idx = 0; $idx < sizeof($users); $idx++) {
      $user = $users->get($idx);
      $pool = $pools->get($idx % sizeof($pools));

      $this->em->persist(new PoolUser($pool, $user));

      // first user in pool is always host
      if (is_null($pool->getHost())) {
        $pool->setHost($user);
        $this->em->persist($pool);
      }
    }
    return $pools;
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
