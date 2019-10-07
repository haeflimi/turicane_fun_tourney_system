<?php

namespace Tfts\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tftsLans", uniqueConstraints={@ORM\UniqueConstraint(name="lan_id", columns={"lan_id"})})
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
     * @ORM\OneToMany(targetEntity="Tfts\Entity\Game", mappedBy="lan")
     */
    private $games;

    /**
     * @ORM\OneToMany(targetEntity="Tfts\Entity\Ranking", mappedBy="lan")
     */
    private $rankings;

    /**
     * @ORM\OneToMany(targetEntity="Tfts\Entity\RankingSnapshot", mappedBy="lan")
     */
    private $rankingSnapshots;

    /**
     * @ORM\OneToMany(targetEntity="Tfts\Entity\Special", mappedBy="lan")
     */
    private $specials;

    /**
     * @ORM\OneToMany(targetEntity="Tfts\Entity\Trackmania", mappedBy="lan")
     */
    private $trackmanias;
    
    public function __construct($lan_id, $handle, $page_id) {
        $this->lan_id = $lan_id;
        $this->handle = $handle;
        $this->page_id = $page_id;
    }

    public function getId() {
        return $this->lan_id;
    }

    public function getHandle() {
        return $this->handle;
    }

    public function getPageId() {
        return $this->page_id;
    }
    
    public function getGames() {
        return $this->games;
    }
    
    public function getRankings() {
        return $this->rankings;
    }

    public function getRankingSnapshots() {
        return $this->rankingSnapshots;
    }

    public function getSpecials() {
        return $this->specials;
    }

    public function getTrackmanias() {
        return $this->trackmanias;
    }
}
