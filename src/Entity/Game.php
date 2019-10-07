<?php

namespace Tfts\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="tftsGames",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="game_id", columns={"game_id", "lan_id"})}
 * )
 */
class Game {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $game_id;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $game_name;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $game_icon;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $game_logo;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $game_banner;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false, options={"default":0})
     */
    private $game_is_pool;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false, options={"default":0})
     */
    private $game_is_mass;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false, options={"default":0})
     */
    private $game_is_team;

    /**
     * @ORM\Column(type="integer", length=10, nullable=false, options={"default":0})
     */
    private $game_player_count;

    /**
     * @ORM\Column(type="string", length=20, nullable=false)
     */
    private $game_mode;

    /**
     * @ORM\Column(type="integer", length=10, nullable=false)
     */
    private $game_points;

    /**
     * @ORM\Column(type="integer", length=10, nullable=false)
     */
    private $game_points_loss;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $game_rules;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false, options={"default":0})
     */
    private $game_is_deleted;

    /**
     * @ORM\Column(type="integer", length=10, nullable=false, options={"default":0})
     */
    private $game_is_featured;

    /**
     * @ORM\ManyToOne(targetEntity="Tfts\Entity\Lan", inversedBy="games")
     * @ORM\JoinColumn(name="lan_id", referencedColumnName="lan_id", nullable=false)
     */
    private $lan;

    /**
     * @ORM\OneToMany(targetEntity="Tfts\Entity\Registration", mappedBy="game")
     */
    private $registrations;

    /**
     * @ORM\OneToMany(targetEntity="Tfts\Entity\Match", mappedBy="game")
     */
    private $matches;

    /**
     * @ORM\OneToMany(targetEntity="Tfts\Entity\Pool", mappedBy="game")
     */
    private $pools;

    public function __construct($game_id, $game_name) {
        $this->game_id = $game_id;
        $this->game_name = $game_name;
    }
    
    public function getId() {
        return $this->game_id;
    }

    public function getName() {
        return $this->game_name;
    }
    
    public function getLan() {
        return $this->lan;
    }
    
    public function getRegistrations() {
        return $this->registrations;
    }
    
    public function getMatches() {
        return $this->matches;
    }
    
    public function getPools() {
        return $this->pools;
    }

}
