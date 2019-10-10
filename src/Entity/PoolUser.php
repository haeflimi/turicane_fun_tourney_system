<?php

namespace Tfts\Entity;

use Concrete\Core\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;
use Tfts\Entity\Pool;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="tftsPoolUsers",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="user_id", columns={"user_id","pool_id"})}
 * )
 */
class PoolUser {

  /**
   * @ORM\Column(type="integer", length=10, nullable=true)
   */
  private $rank;

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

  public function __construct(User $user) {
    $this->user = $user;
  }

  public function getRank(): int {
    return $this->rank;
  }

  public function setRank(int $rank) {
    $this->rank = $rank;
  }

  public function getPool(): Pool {
    return $this->pool;
  }

  public function setPool(Pool $pool) {
    $this->pool = $pool;
  }

  public function getUser(): User {
    return $this->user;
  }

}
