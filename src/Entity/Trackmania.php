<?php

namespace Tfts;

use Concrete\Core\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;
use Tfts\Map;

/**
 * @ORM\Entity
 * @ORM\Table(name="tftsTrackmania")
 */
class Trackmania {

  /**
   * @ORM\Column(type="datetime", nullable=false)
   */
  private $tm_datetime;

  /**
   * @ORM\Column(type="integer", length=10, nullable=false)
   */
  private $tm_record;

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Tfts\Map")
   * @ORM\JoinColumn(name="map_id", referencedColumnName="map_id", nullable=false)
   */
  private $map;

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\User\User")
   * @ORM\JoinColumn(name="user_id", referencedColumnName="uID", nullable=false)
   */
  private $user;

  public function __construct(User $user, Map $map, \DateTime $datetime, int $record) {
    $this->user = $user;
    $this->map = $map;
    $this->tm_datetime = $datetime;
    $this->tm_record = $record;
  }

  public function getMap(): Map {
    return $this->map;
  }

  public function getUser(): User {
    return $this->user;
  }

  public function getDateTime(): \DateTime {
    return $this->tm_datetime;
  }

  public function getRecord(): int {
    return $this->tm_record;
  }

  public function setDateTime(\DateTime $datetime) {
    $this->tm_datetime = $datetime;
  }

  public function setRecord(int $record) {
    $this->tm_record = $record;
  }

  public static function compare(Trackmania $trackmania1, Trackmania $trackmania2): int {
    return $trackmania1->getRecord() <=> $trackmania2->getRecord();
  }

}
