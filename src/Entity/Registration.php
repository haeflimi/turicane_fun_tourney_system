<?php

namespace Tfts\Entity;

use Concrete\Core\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="tftsRegistrations",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="user", columns={"user_id","game_id"}), @ORM\UniqueConstraint(name="group", columns={"group_id","game_id"})}
 * )
 */
class Registration {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer", length=10)
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $registration_id;

  /**
   * @ORM\ManyToOne(targetEntity="Tfts\Entity\Game", inversedBy="registrations")
   * @ORM\JoinColumn(name="game_id", referencedColumnName="game_id", nullable=false)
   */
  private $game;

  /**
   * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\User\User")
   * @ORM\JoinColumn(name="user_id", referencedColumnName="uID", nullable=true)
   */
  private $user;

  /**
   * @ORM\Column(type="integer", length=10, nullable=true)
   */
  private $group_id;

  /**
   * @ORM\Column(type="integer", length=10, nullable=false)
   */
  private $rnd_number;

  public function __construct(Game $game, User $user = null, int $group_id = null) {
    $this->game = $game;
    $this->user = $user;
    $this->group_id = $group_id;
    $this->rnd_number = rand(1, 1000000);
  }

  public function getId() {
    return $this->registration_id;
  }

  public function getGame() {
    return $this->game;
  }

  public function getUser() {
    return $this->user;
  }

  public function getGroupId() {
    return $this->group_id;
  }

  public function getRandomNumber() {
    return $this->rnd_number;
  }

}
