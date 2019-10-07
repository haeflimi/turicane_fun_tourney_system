<?php

namespace Tfts\Entity;

use Doctrine\ORM\Mapping as ORM;

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
   * @ORM\Column(type="time", nullable=false)
   */
  private $map_record;

  /**
   * @ORM\Column(type="integer", length=10, nullable=false)
   */
  private $map_milliseconds;

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

}
