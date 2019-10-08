<?php

namespace Tfts\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="tftsGames",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="game_id", columns={"game_id", "lan_id"})}
 * )
 */
class Game {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer", length=10)
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $game_id;

  /**
   * @ORM\Column(type="string", length=50, nullable=false)
   */
  private $game_name;

  /**
   * @ORM\Column(type="string", length=200, nullable=true)
   */
  private $game_icon;

  /**
   * @ORM\Column(type="string", length=200, nullable=true)
   */
  private $game_logo;

  /**
   * @ORM\Column(type="string", length=200, nullable=true)
   */
  private $game_banner;

  /**
   * @ORM\Column(type="integer", length=1, nullable=false, options={"default":0})
   */
  private $game_is_pool = 0;

  /**
   * @ORM\Column(type="integer", length=1, nullable=false, options={"default":0})
   */
  private $game_is_mass = 0;

  /**
   * @ORM\Column(type="integer", length=1, nullable=false, options={"default":0})
   */
  private $game_is_team = 0;

  /**
   * @ORM\Column(type="integer", length=10, nullable=false, options={"default":0})
   */
  private $game_player_count = 0;

  /**
   * @ORM\Column(type="string", length=20, nullable=true)
   */
  private $game_mode;

  /**
   * @ORM\Column(type="integer", length=10, nullable=false, options={"default":0})
   */
  private $game_points_win = 0;

  /**
   * @ORM\Column(type="integer", length=10, nullable=false, options={"default":0})
   */
  private $game_points_loss = 0;

  /**
   * @ORM\Column(type="string", nullable=true)
   */
  private $game_rules;

  /**
   * @ORM\Column(type="integer", length=1, nullable=false, options={"default":0})
   */
  private $game_is_deleted = 0;

  /**
   * @ORM\Column(type="integer", length=10, nullable=false, options={"default":0})
   */
  private $game_is_featured = 0;

  /**
   * @ORM\ManyToOne(targetEntity="Tfts\Entity\Lan", inversedBy="games")
   * @ORM\JoinColumn(name="lan_id", referencedColumnName="lan_id", nullable=false)
   */
  private $lan;

  /**
   * @ORM\OneToMany(targetEntity="Tfts\Entity\Registration", mappedBy="game")
   */
  private $registrations;

  /**
   * @ORM\OneToMany(targetEntity="Tfts\Entity\Match", mappedBy="game")
   */
  private $matches;

  /**
   * @ORM\OneToMany(targetEntity="Tfts\Entity\Pool", mappedBy="game")
   */
  private $pools;

  public function __construct(Lan $lan, $game_id, $game_name) {
    $this->lan = $lan;
    $this->game_id = $game_id;
    $this->game_name = $game_name;
  }

  public function getId() {
    return $this->game_id;
  }

  public function getName() {
    return $this->game_name;
  }

  public function getPointsWin() {
    return $this->game_points_win;
  }

  public function getPointsLoss() {
    return $this->game_points_loss;
  }

  public function getIsPool() {
    return $this->game_is_pool;
  }

  public function getIsTeam() {
    return $this->game_is_team;
  }

  public function getIsMass() {
    return $this->game_is_mass;
  }

  public function getLan() {
    return $this->lan;
  }

  public function getRegistrations() {
    return $this->registrations;
  }

  public function getMatches() {
    return $this->matches;
  }

  public function getPools() {
    return $this->pools;
  }

}
