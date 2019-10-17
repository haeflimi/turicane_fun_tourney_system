<?php

namespace Tfts;

use Concrete\Core\Entity\User\User;
use Concrete\Core\User\Group\Group;
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
   * @ORM\ManyToOne(targetEntity="Tfts\Game", inversedBy="registrations")
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

  public function getId(): int {
    return $this->registration_id;
  }

  public function getGame(): Game {
    return $this->game;
  }

  public function getUser(): User {
    return $this->user;
  }

  public function getGroupId(): int {
    return $this->group_id;
  }

  public function getGroup(): Group {
    return Group::getByID($this->group_id);
  }

  public function getRandomNumber(): int {
    return $this->rnd_number;
  }

  public static function compare(Registration $registration1, Registration $registration2): int {
    return $registration1->getRandomNumber() <=> $registration2->getRandomNumber();
  }

  public function getName(){
      if($this->group_id){
          return $this->getGroup()->getGroupName();
      }
      return $this->getUser()->getUserName();
  }
}
