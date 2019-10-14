<?php

namespace Tfts\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Tfts\Entity\Lan;

/**
 * @ORM\Entity
 * @ORM\Table(name="tftsSnapshots")
 */
class Snapshot {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer", length=10)
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $snapshot_id;

  /**
   * @ORM\Column(type="datetime", nullable=false)
   */
  private $snapshot_datetime;

  /**
   * @ORM\ManyToOne(targetEntity="Tfts\Entity\Lan", inversedBy="snapshots")
   * @ORM\JoinColumn(name="lan_id", referencedColumnName="lan_id", nullable=false)
   */
  private $lan;

  /**
   * @ORM\OneToMany(targetEntity="Tfts\Entity\RankingSnapshot", mappedBy="snapshot")
   */
  private $rankingSnapshots;

  public function __construct(Lan $lan, \DateTime $datetime) {
    $this->lan = $lan;
    $this->snapshot_datetime = $datetime;
  }

  public function getId(): int {
    return $this->snapshot_id;
  }

  public function getDateTime(): \DateTime {
    return $this->snapshot_datetime;
  }

  public function getLan(): Lan {
    return $this->lan;
  }

  public function getRankingSnapshots(): Collection {
    return $this->rankingSnapshots;
  }

}
