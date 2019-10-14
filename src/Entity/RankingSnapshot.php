<?php

namespace Tfts;

use Doctrine\ORM\Mapping as ORM;
use Tfts\Ranking;
use Tfts\Snapshot;

/**
 * @ORM\Entity
 * @ORM\Table(name="tftsRankingSnapshots")
 */
class RankingSnapshot {

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Tfts\Ranking", inversedBy="rankingSnapshots")
   * @ORM\JoinColumn(name="ranking_id", referencedColumnName="ranking_id", nullable=false)
   */
  private $ranking;

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Tfts\Snapshot", inversedBy="rankingSnapshots")
   * @ORM\JoinColumn(name="snapshot_id", referencedColumnName="snapshot_id", nullable=false)
   */
  private $snapshot;

  /**
   * @ORM\Column(type="integer", length=10, nullable=false, options={"default":0})
   */
  private $rank;

  public function __construct(Ranking $ranking, Snapshot $snapshot, int $rank) {
    $this->ranking = $ranking;
    $this->snapshot = $snapshot;
    $this->rank = $rank;
  }

  public function getTimestamp(): \DateTime {
    return $this->timestamp;
  }

  public function getRanking(): Ranking {
    return $this->ranking;
  }

  public function getSnapshot(): Snapshot {
    return $this->snapshot;
  }

  public function getRank(): int {
    return $this->rank;
  }

}
