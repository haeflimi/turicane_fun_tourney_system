<?php

namespace Tfts\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tftsGames", uniqueConstraints={@ORM\UniqueConstraint(name="game_id", columns={"game_id"})})
 */
class Game
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $game_id;

    /**
     * @ORM\Column(type="string", length=50, nullable=true, options={"default":0})
     */
    private $game_name;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $game_icon;

    /**
     * @ORM\Column(type="string", length=200, nullable=true, options={"default":0})
     */
    private $game_logo;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $game_banner;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false, options={"default":1})
     */
    private $game_is_pool;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false, options={"default":0})
     */
    private $game_is_mass;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true, options={"default":0})
     */
    private $game_is_team;

    /**
     * @ORM\Column(type="integer", length=10, nullable=true, options={"default":0})
     */
    private $game_player_count;

    /**
     * @ORM\Column(type="string", length=20, nullable=true, options={"default":0})
     */
    private $game_mode;

    /**
     * @ORM\Column(type="integer", length=10, nullable=true, options={"default":10})
     */
    private $game_points;

    /**
     * @ORM\Column(type="integer", length=10, nullable=true, options={"default":1})
     */
    private $game_points_loss;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $game_rules;

    /**
     * @ORM\Column(type="integer", length=10, nullable=true)
     */
    private $game_lan_id;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false, options={"default":0})
     */
    private $game_is_deleted;

    /**
     * @ORM\Column(type="integer", length=10, nullable=false, options={"default":0})
     */
    private $game_is_featured;

    /**
     * @ORM\OneToMany(targetEntity="Tfts\Entity\Lfg", mappedBy="game")
     */
    private $lfgs;

    /**
     * @ORM\OneToMany(targetEntity="Tfts\Entity\Match", mappedBy="game")
     */
    private $matches;

    /**
     * @ORM\OneToMany(targetEntity="Tfts\Entity\Pool", mappedBy="game")
     */
    private $pools;

    /**
     * @ORM\OneToMany(targetEntity="Tfts\Entity\Registration", mappedBy="game")
     */
    private $registrations;

    public function getName()
    {
        return $this->game_name;
    }
}