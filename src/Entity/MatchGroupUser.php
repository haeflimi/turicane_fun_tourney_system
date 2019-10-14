<?php

namespace Tfts;

use Concrete\Core\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;
use Tfts\Match;

/**
 * @ORM\Entity
 * @ORM\Table(name="tftsMatchGroupUsers")
 */
class MatchGroupUser {

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Tfts\Match", inversedBy="matchPlayers")
   * @ORM\JoinColumn(name="match_id", referencedColumnName="match_id", nullable=false)
   */
  private $match;

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\User\User", inversedBy="matchPlayers")
   * @ORM\JoinColumn(name="user_id", referencedColumnName="uID", nullable=false)
   */
  private $user;

  /**
   * @ORM\Id
   * @ORM\Column(type="integer", length=10, nullable=false))
   */
  private $group_id;

  public function __construct(Match $match, User $user, int $group_id) {
    $this->match = $match;
    $this->user = $user;
    $this->group_id = $group_id;
  }

  public function getMatch(): Match {
    return $this->match;
  }

  public function getUser(): User {
    return $this->user;
  }

  public function getGroupId(): int {
    return $this->group_id;
  }

}
