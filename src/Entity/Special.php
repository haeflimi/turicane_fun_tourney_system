<?php

namespace Tfts\Entity;

use Concrete\Core\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;
use Tfts\Entity\Lan;

/**
 * @ORM\Entity
 * @ORM\Table(name="tftsSpecials")
 */
class Special {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer", length=10)
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $special_id;

  /**
   * @ORM\Column(type="integer", length=10, nullable=false)
   */
  private $special_points;

  /**
   * @ORM\Column(type="string", length=255, nullable=false)
   */
  private $special_description;

  /**
   * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\User\User")
   * @ORM\JoinColumn(name="user_id", referencedColumnName="uID")
   */
  private $user;

  /**
   * @ORM\ManyToOne(targetEntity="Tfts\Entity\Lan", inversedBy="specials")
   * @ORM\JoinColumn(name="lan_id", referencedColumnName="lan_id")
   */
  private $lan;

  public function __construct(Lan $lan, User $user, String $description, int $points) {
    $this->lan = $lan;
    $this->user = $user;
    $this->special_description = $description;
    $this->special_points = $points;
  }

  public function getId() {
    return $this->special_id;
  }

  public function getUser() {
    return $this->user;
  }

  public function getDescription() {
    return $this->special_description;
  }

  public function getPoints() {
    return $this->special_points;
  }

}
