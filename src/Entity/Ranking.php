<?php

namespace Tfts\Entity;

use Concrete\Core\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="tftsRankings",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="user_id", columns={"user_id","lan_id"})}
 * )
 */
class Ranking {

  /**
   * @ORM\Column(type="integer", length=10, nullable=false, options={"default":0})
   */
  private $points = 0;

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\User\User", inversedBy="rankings")
   * @ORM\JoinColumn(name="user_id", referencedColumnName="uID")
   */
  private $user;

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Tfts\Entity\Lan", inversedBy="rankings")
   * @ORM\JoinColumn(name="lan_id", referencedColumnName="lan_id")
   */
  private $lan;

  public function __construct(Lan $lan, User $user) {
    $this->lan = $lan;
    $this->user = $user;
  }

  public function getLan() {
    return $this->lan;
  }

  public function getUser() {
    return $this->user;
  }

  public function getPoints() {
    return $this->points;
  }

  public function setPoints(int $points) {
    $this->points = $points;
  }

  public static function compare(Ranking $ranking1, Ranking $ranking2) {
    return $ranking1->getPoints() <=> $ranking2->getPoints();
  }

}
