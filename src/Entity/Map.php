<?php

namespace Tfts;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

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
   * @ORM\Column(type="integer", length=10, nullable=false)
   */
  private $map_data_resolution = 0;

  /**
   * @ORM\Column(type="integer", length=10, nullable=false)
   */
  private $map_data_unit = 0;

  /**
   * @ORM\Column(type="integer", length=1, nullable=false, options={"default":0})
   */
  private $map_is_processed = 0;

  /**
   * @ORM\OneToMany(targetEntity="Tfts\MapRecord", mappedBy="map")
   */
  private $mapRecords;

  /**
   * @ORM\ManyToOne(targetEntity="Tfts\Game", inversedBy="maps")
   * @ORM\JoinColumn(name="game_id", referencedColumnName="game_id", nullable=false)
   */
  private $game;

  public function __construct(Game $game, string $map_name, int $map_data_resolution, int $map_data_unit) {
    $this->game = $game;
    $this->map_name = $map_name;
    $this->map_data_resolution = $map_data_resolution;
    $this->map_data_unit = $map_data_unit;
  }

  public function getId(): int {
    return $this->map_id;
  }

  public function getName(): string {
    return $this->map_name;
  }

  public function getRecords(): Collection {
    return $this->mapRecords;
  }
  
  public function getDataResolution(): int {
    return $this->map_data_resolution;
  }
  
  public function getDataUnit(): int {
    return $this->map_data_unit;
  }

  public function isProcessed(): bool {
    return $this->map_is_processed == 1;
  }

  public function setProcessed(bool $processed) {
    $this->map_is_processed = $processed ? 1 : 0;
  }

  public function getGame(): Game {
    return $this->game;
  }

}
