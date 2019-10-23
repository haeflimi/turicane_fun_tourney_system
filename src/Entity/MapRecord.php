<?php

namespace Tfts;

use Concrete\Core\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;
use Tfts\Map;

/**
 * @ORM\Entity
 * @ORM\Table(name="tftsMapRecord")
 */
class MapRecord {

  /**
   * @ORM\Column(type="datetime", nullable=false)
   */
  private $map_datetime;

  /**
   * @ORM\Column(type="integer", length=10, nullable=false)
   */
  private $map_record;

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
    $this->map_datetime = $datetime;
    $this->map_record = $record;
  }

  public function getMap(): Map {
    return $this->map;
  }

  public function getUser(): User {
    return $this->user;
  }

  public function getDateTime(): \DateTime {
    return $this->map_datetime;
  }

  public function getRecord(): int {
    return $this->map_record;
  }

  public function setDateTime(\DateTime $datetime) {
    $this->map_datetime = $datetime;
  }

  public function setRecord(int $record) {
    $this->map_record = $record;
  }

  public static function compare(MapRecord $record1, MapRecord $record2): int {
    return $record1->getRecord() <=> $record2->getRecord();
  }

}
