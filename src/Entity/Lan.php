<?php

namespace Tfts;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tftsLans")
 */
class Lan {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $lan_id;

  /**
   * @ORM\Column(type="string", unique=true, nullable=true)
   */
  private $handle;

  /**
   * @ORM\Column(type="integer", nullable=true)
   */
  private $page_id;

  /**
   * @ORM\OneToMany(targetEntity="Tfts\Game", mappedBy="lan")
   */
  private $games;

  /**
   * @ORM\OneToMany(targetEntity="Tfts\Ranking", mappedBy="lan")
   * @ORM\OrderBy({"ranking_points" = "DESC"})
   */
  private $rankings;

  /**
   * @ORM\OneToMany(targetEntity="Tfts\Snapshot", mappedBy="lan")
   */
  private $snapshots;

  /**
   * @ORM\OneToMany(targetEntity="Tfts\Special", mappedBy="lan")
   */
  private $specials;

  /**
   * @ORM\OneToMany(targetEntity="Tfts\Trackmania", mappedBy="lan")
   */
  private $trackmanias;

  public function __construct($lan_id, $handle, $page_id) {
    $this->lan_id = $lan_id;
    $this->handle = $handle;
    $this->page_id = $page_id;
  }

  public function getId(): int {
    return $this->lan_id;
  }

  public function getHandle(): String {
    return $this->handle;
  }

  public function getPageId(): int {
    return $this->page_id;
  }

  public function getGames(): Collection {
    return $this->games;
  }

  public function getRankings(): Collection {
    return $this->rankings;
  }

  public function getSnapshots(): Collection {
    return $this->snapshots;
  }

  public function getSpecials(): Collection {
    return $this->specials;
  }

  public function getTrackmanias(): Collection {
    return $this->trackmanias;
  }

  public function getLanPage(): Page {
    if (empty($this->page_id)) {
      return null;
    }
    if (empty($this->handle)) {
      $this->handle = Page::getById($this->page_id);
    }
    return $this->handle;
  }

  public function getLanPageURL() {
    if (!is_object($lan_page = $this->getLanPage())) {
      return null;
    }
    return $lan_page->getCollectionLink();
  }

}
