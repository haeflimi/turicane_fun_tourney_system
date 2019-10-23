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
   * @ORM\Column(type="integer", length=1, nullable=false, options={"default":0})
   */
  private $map_is_processed = 0;

  /**
   * @ORM\OneToMany(targetEntity="Tfts\MapRecord", mappedBy="map")
   */
  private $mapRecords;

  /**
   * @ORM\ManyToOne(targetEntity="Tfts\Game", inversedBy="mapRecords")
   * @ORM\JoinColumn(name="game_id", referencedColumnName="game_id", nullable=false)
   */
  private $game;

  public function __construct(Game $game, String $map_name) {
    $this->game = $game;
    $this->map_name = $map_name;
  }

  public function getId(): int {
    return $this->map_id;
  }

  public function getName(): String {
    return $this->map_name;
  }

  public function getRecords(): Collection {
    return $this->mapRecords;
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
