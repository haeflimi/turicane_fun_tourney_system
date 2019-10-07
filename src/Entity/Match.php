<?php

namespace Tfts\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="tftsMatches",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="match_id", columns={"match_id","game_id"})}
 * )
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
  private $match_accepted;

  /**
   * @ORM\Column(type="integer", length=10, nullable=false, options={"default":0})
   */
  private $match_point1;

  /**
   * @ORM\Column(type="integer", length=10, nullable=false, options={"default":0})
   */
  private $match_point2;

  /**
   * @ORM\Column(type="integer", length=1, nullable=false, options={"default":0})
   */
  private $match_confirmed1;

  /**
   * @ORM\Column(type="integer", length=1, nullable=false, options={"default":0})
   */
  private $match_confirmed2;

  /**
   * @ORM\Column(type="integer", length=10, nullable=false, options={"default":0})
   */
  private $match_compute1;

  /**
   * @ORM\Column(type="integer", length=10, nullable=false, options={"default":0})
   */
  private $match_compute2;

  /**
   * @ORM\Column(type="integer", length=1, nullable=false, options={"default":0})
   */
  private $match_published;

  /**
   * @ORM\ManyToOne(targetEntity="Tfts\Entity\Game", inversedBy="matches")
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
   * @ORM\ManyToOne(targetEntity="Tfts\Entity\Team", inversedBy="matches")
   * @ORM\JoinColumn(name="team1_id", referencedColumnName="team_id", nullable=true)
   */
  private $team1;

  /**
   * @ORM\ManyToOne(targetEntity="Tfts\Entity\Team", inversedBy="matches")
   * @ORM\JoinColumn(name="team2_id", referencedColumnName="team_id", nullable=true)
   */
  private $team2;

  /**
   * @ORM\OneToMany(targetEntity="Tfts\Entity\MatchTeamUser", mappedBy="match")
   */
  private $matchTeamUsers;

}
