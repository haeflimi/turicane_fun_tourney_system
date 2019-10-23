<?php

namespace Tfts;

use Concrete\Core\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;
use Tfts\Pool;

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
   * @ORM\ManyToOne(targetEntity="Tfts\Pool", inversedBy="poolAllocations")
   * @ORM\JoinColumn(name="pool_id", referencedColumnName="pool_id", nullable=false)
   */
  private $pool;

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\User\User")
   * @ORM\JoinColumn(name="user_id", referencedColumnName="uID", nullable=false)
   */
  private $user;

  /**
   * @ORM\Column(type="integer", length=10, nullable=false)
   */
  private $rnd_number;

  public function __construct(User $user) {
    $this->user = $user;
    $this->rnd_number = rand(1, 1000000);
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
  
  public function setPool(Pool $pool) {
    $this->pool = $pool;
  }

  public function getUser(): User {
    return $this->user;
  }

  public function getRandomNumber(): int {
    return $this->rnd_number;
  }

  public static function compareRandomNumber(PoolUser $poolUser1, PoolUser $poolUser2): int {
    return $poolUser1->getRandomNumber() <=> $poolUser2->getRandomNumber();
  }

  public static function compareRank(PoolUser $poolUser1, PoolUser $poolUser2): int {
    return $poolUser1->getRank() <=> $poolUser2->getRank();
  }

}
