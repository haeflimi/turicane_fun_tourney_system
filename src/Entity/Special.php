<?php

namespace Tfts;

use Concrete\Core\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;
use Tfts\Lan;

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
   * @ORM\Column(type="datetime", nullable=true)
   */
  private $special_date;

  /**
   * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\User\User")
   * @ORM\JoinColumn(name="user_id", referencedColumnName="uID")
   */
  private $user;

  /**
   * @ORM\ManyToOne(targetEntity="Tfts\Lan", inversedBy="specials")
   * @ORM\JoinColumn(name="lan_id", referencedColumnName="lan_id")
   */
  private $lan;

  public function __construct(Lan $lan, User $user, string $description, int $points) {
    $this->lan = $lan;
    $this->user = $user;
    $this->special_description = $description;
    $this->special_points = $points;
    $this->special_date = new \DateTime("now");
  }

  public function getId(): int {
    return $this->special_id;
  }

  public function getLan(): Lan {
    return $this->lan;
  }

  public function getUser(): User {
    return $this->user;
  }

  public function getDescription(): string {
    return $this->special_description;
  }

  public function getDate(): \DateTime {
    return $this->special_date;
  }

  public function getPoints(): int {
    return $this->special_points;
  }

}
