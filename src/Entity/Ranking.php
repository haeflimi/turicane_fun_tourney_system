<?php

namespace Tfts\Entity;

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
  private $points;

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

}
