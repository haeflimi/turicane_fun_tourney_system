<?php

namespace Tfts\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Tfts\Entity\Lan;

/**
 * @ORM\Entity
 * @ORM\Table(name="tftsMaps")
 */
class Map {

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
   * @ORM\Column(type="integer", length=1, nullable=false, options={"default":0})
   */
  private $map_is_processed = 0;

  /**
   * @ORM\OneToMany(targetEntity="Tfts\Entity\Trackmania", mappedBy="map")
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

  public function getId(): int {
    return $this->map_id;
  }

  public function getName(): String {
    return $this->map_name;
  }

  public function getTrackmanias(): Collection {
    return $this->trackmanias;
  }

  public function isProcessed(): bool {
    return $this->map_is_processed == 1;
  }

  public function setProcessed(bool $processed) {
    $this->map_is_processed = $processed ? 1 : 0;
  }

  public function getLan(): Lan {
    return $this->lan;
  }

}
