<?php

namespace Tfts\Entity;

use Concrete\Core\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;
use Tfts\Entity\TrackmaniaMap;

/**
 * @ORM\Entity
 * @ORM\Table(name="tftsTrackmania")
 */
class Trackmania {

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
   * @ORM\ManyToOne(targetEntity="Tfts\Entity\TrackmaniaMap")
   * @ORM\JoinColumn(name="map_id", referencedColumnName="map_id", nullable=false)
   */
  private $map;

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\User\User")
   * @ORM\JoinColumn(name="user_id", referencedColumnName="uID", nullable=false)
   */
  private $user;

  public function __construct(User $user, TrackmaniaMap $map, \DateTime $datetime, int $record) {
    $this->user = $user;
    $this->map = $map;
    $this->map_datetime = $datetime;
    $this->map_record = $record;
  }

  public function getMap() {
    return $this->map;
  }

  public function getUser() {
    return $this->user;
  }

  public function getDateTime() {
    return $this->map_datetime;
  }

  public function getRecord() {
    return $this->map_record;
  }

  public function setDateTime(\DateTime $datetime) {
    $this->map_datetime = $datetime;
  }

  public function setRecord(int $record) {
    $this->map_record = $record;
  }

}
