<?php

namespace Tfts;

use Concrete\Core\Entity\User\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tftsRankings",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="INDEX", columns={"ranking_id","user_id","lan_id"})}
 * )
 */
class Ranking {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer", length=10)
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $ranking_id;

  /**
   * @ORM\Column(type="integer", length=10, nullable=false, options={"default":0})
   */
  public $ranking_points = 0;

  /**
   * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\User\User")
   * @ORM\JoinColumn(name="user_id", referencedColumnName="uID")
   */
  private $user;

  /**
   * @ORM\ManyToOne(targetEntity="Tfts\Lan", inversedBy="rankings")
   * @ORM\JoinColumn(name="lan_id", referencedColumnName="lan_id")
   */
  private $lan;

  /**
   * @ORM\OneToMany(targetEntity="Tfts\RankingSnapshot", mappedBy="ranking")
   */
  private $rankingSnapshots;

  public function __construct(Lan $lan, User $user) {
    $this->lan = $lan;
    $this->user = $user;
  }

  public function getId(): int {
    return $this->ranking_id;
  }

  public function getPoints(): int {
    return $this->ranking_points;
  }

  public function setPoints(int $ranking_points) {
    $this->ranking_points = $ranking_points;
  }

  public function getLan(): Lan {
    return $this->lan;
  }

  public function getUser(): User {
    return $this->user;
  }

  public function getRankingSnapshots(): Collection {
    return $this->rankingSnapshots;
  }

  public static function compare(Ranking $ranking1, Ranking $ranking2): int {
    return $ranking2->getPoints() <=> $ranking1->getPoints();
  }

}
