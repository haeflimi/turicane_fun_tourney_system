<?php

namespace Tfts;

use Concrete\Core\Entity\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Tfts\Game;

/**
 * @ORM\Entity
 * @ORM\Table(name="tftsMatches")
 */
class Match {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer", length=10)
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $match_id;

  /**
   * @ORM\Column(type="datetime", nullable=false, options={"default":"CURRENT_TIMESTAMP"})
   */
  private $match_challenge_date;

  /**
   * @ORM\Column(type="datetime", nullable=true)
   */
  private $match_accept_date;

  /**
   * @ORM\Column(type="datetime", nullable=true)
   */
  private $match_finish_date;

  /**
   * @ORM\Column(type="integer", length=1, nullable=false, options={"default":0})
   */
  private $match_accepted = 0;

  /**
   * @ORM\Column(type="integer", length=10, nullable=false, options={"default":0})
   */
  private $match_score1 = 0;

  /**
   * @ORM\Column(type="integer", length=10, nullable=false, options={"default":0})
   */
  private $match_score2 = 0;

  /**
   * @ORM\Column(type="integer", length=1, nullable=false, options={"default":0})
   */
  private $match_confirmed1 = 0;

  /**
   * @ORM\Column(type="integer", length=1, nullable=false, options={"default":0})
   */
  private $match_confirmed2 = 0;

  /**
   * @ORM\Column(type="integer", length=10, nullable=false, options={"default":0})
   */
  private $match_compute1 = 0;

  /**
   * @ORM\Column(type="integer", length=10, nullable=false, options={"default":0})
   */
  private $match_compute2 = 0;

  /**
   * @ORM\Column(type="integer", length=1, nullable=false, options={"default":0})
   */
  private $match_published = 0;

  /**
   * @ORM\ManyToOne(targetEntity="Tfts\Game", inversedBy="matches")
   * @ORM\JoinColumn(name="game_id", referencedColumnName="game_id")
   */
  private $game;

  /**
   * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\User\User", inversedBy="matches")
   * @ORM\JoinColumn(name="user1_id", referencedColumnName="uID", nullable=true)
   */
  private $user1;

  /**
   * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\User\User", inversedBy="matches")
   * @ORM\JoinColumn(name="user2_id", referencedColumnName="uID", nullable=true)
   */
  private $user2;

  /**
   * @ORM\Column(type="integer", length=10, nullable=true)
   */
  private $group1_id;

  /**
   * @ORM\Column(type="integer", length=10, nullable=true)
   */
  private $group2_id;

  /**
   * @ORM\OneToMany(targetEntity="Tfts\MatchGroupUser", mappedBy="match")
   */
  private $matchGroupUsers;

  public function __construct(Game $game) {
    $this->game = $game;
    $this->match_challenge_date = new \DateTime("now");
    $this->matchGroupUsers = new ArrayCollection();
  }

  public function setUsers(User $challenger, User $challenged) {
    $this->user1 = $challenger;
    $this->user2 = $challenged;
  }

  public function setGroups(int $challenger_id, int $challenged_id) {
    $this->group1_id = $challenger_id;
    $this->group2_id = $challenged_id;
  }

  public function setAccepted(bool $accepted) {
    if ($accepted) {
      $this->match_accepted = 1;
      $this->match_accept_date = new \DateTime("now");
    } else {
      $this->match_accepted = 0;
      $this->match_accept_date = null;
    }
  }

  public function setScore1(int $score) {
    $this->match_score1 = $score;
  }

  public function setScore2(int $score) {
    $this->match_score2 = $score;
  }

  public function setConfirmed1(bool $confirmed) {
    $this->match_confirmed1 = $confirmed ? 1 : 0;
  }

  public function setConfirmed2(bool $confirmed) {
    $this->match_confirmed2 = $confirmed ? 1 : 0;
  }

  public function setCompute1(int $compute) {
    $this->match_compute1 = $compute;
  }

  public function setCompute2(int $compute) {
    $this->match_compute2 = $compute;
  }

  public function setFinished(bool $finished) {
    if ($finished) {
      $this->match_finish_date = new \DateTime("now");
    } else {
      $this->match_finish_date = null;
    }
  }

  public function setPublished(bool $published) {
    if ($published) {
      $this->match_published = new \DateTime("now");
    } else {
      $this->match_published = null;
    }
  }

  public function getId(): int {
    return $this->match_id;
  }

  public function getGame(): Game {
    return $this->game;
  }

  public function getUser1(): User {
    return $this->user1;
  }

  public function getUser2(): User {
    return $this->user2;
  }

  public function getGroup1Id(): int {
    return $this->group1_id;
  }

  public function getGroup2Id(): int {
    return $this->group2_id;
  }

  public function getMatchGroupUsers(): Collection {
    return $this->matchGroupUsers;
  }

  public function getChallengeDate(): \DateTime {
    return $this->match_challenge_date;
  }

  public function isAccepted(): bool {
    return $this->match_accepted == 1;
  }

  public function getFinishDate(): ?\DateTime {
    return $this->match_finish_date;
  }

  public function getScore1(): int {
    return $this->match_score1;
  }

  public function getScore2(): int {
    return $this->match_score2;
  }

  public function isConfirmed1(): bool {
    return $this->match_confirmed1 == 1;
  }

  public function isConfirmed2(): bool {
    return $this->match_confirmed2 == 1;
  }

  public function getCompute1(): int {
    return $this->match_compute1;
  }

  public function getCompute2(): int {
    return $this->match_compute2;
  }

}
