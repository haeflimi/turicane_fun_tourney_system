<?php

namespace Tfts\Entity;

use Concrete\Core\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;
use Tfts\Entity\Pool;

/**
 * @ORM\Entity
 * @ORM\Table(name="tftsPoolUsers")
 */
class PoolUser {

  /**
   * @ORM\Column(type="integer", length=10, nullable=false)
   */
  private $rank = 0;

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Tfts\Entity\Pool", inversedBy="poolAllocations")
   * @ORM\JoinColumn(name="pool_id", referencedColumnName="pool_id", nullable=false)
   */
  private $pool;

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\User\User")
   * @ORM\JoinColumn(name="user_id", referencedColumnName="uID", nullable=false)
   */
  private $user;

  public function __construct(Pool $pool, User $user) {
    $this->pool = $pool;
    $this->user = $user;
  }

  public function getRank(): ?int {
    return $this->rank;
  }

  public function setRank(int $rank) {
    $this->rank = $rank;
  }

  public function getPool(): Pool {
    return $this->pool;
  }

  public function getUser(): User {
    return $this->user;
  }

}
