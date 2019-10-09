<?php

namespace Tfts\Entity;

use Doctrine\ORM\Mapping as ORM;
use Tfts\Entity\Lan;

/**
 * @ORM\Entity
 * @ORM\Table(name="tftsTrackmaniaMaps", uniqueConstraints={@ORM\UniqueConstraint(name="map_id", columns={"map_id","lan_id"})})
 */
class TrackmaniaMap {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer", length=10)
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $map_id;

  /**
   * @ORM\Column(type="string", length=100)
   */
  private $map_name;

  /**
   * @ORM\OneToMany(targetEntity="Tfts\Entity\Trackmania", mappedBy="trackmaniamap")
   */
  private $trackmanias;

  /**
   * @ORM\ManyToOne(targetEntity="Tfts\Entity\Lan", inversedBy="trackmanias")
   * @ORM\JoinColumn(name="lan_id", referencedColumnName="lan_id", nullable=false)
   */
  private $lan;

  public function __construct(Lan $lan, String $map_name) {
    $this->lan = $lan;
    $this->map_name = $map_name;
  }

  public function getId() {
    return $this->map_id;
  }

  public function getName() {
    return $this->map_name;
  }

  public function getTrackmanias() {
    return $this->trackmanias;
  }

}
