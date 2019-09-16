<?php

namespace Tfts\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="tftsMatches",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="match_id", columns={"match_id","match_game_id"})}
 * )
 */
class Match
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $match_id;

    /**
     * @ORM\Column(type="integer", length=10, nullable=true, options={"default":0})
     */
    private $match_part1;

    /**
     * @ORM\Column(type="integer", length=10, nullable=true, options={"default":0})
     */
    private $match_part2;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $match_challenge_date;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true, options={"default":0})
     */
    private $match_accepted;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $match_accept_date;

    /**
     * @ORM\Column(type="integer", length=10, nullable=true, options={"default":0})
     */
    private $match_point1;

    /**
     * @ORM\Column(type="integer", length=10, nullable=true, options={"default":0})
     */
    private $match_point2;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true, options={"default":0})
     */
    private $match_confirmed1;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true, options={"default":0})
     */
    private $match_confirmed2;

    /**
     * @ORM\Column(type="integer", length=10, nullable=true, options={"default":0})
     */
    private $match_compute1;

    /**
     * @ORM\Column(type="integer", length=10, nullable=true, options={"default":0})
     */
    private $match_compute2;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true, options={"default":0})
     */
    private $match_cancelled;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $match_finish_date;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false, options={"default":0})
     */
    private $match_published;

    /**
     * @ORM\OneToMany(targetEntity="Tfts\Entity\MatchPlayer", mappedBy="match")
     */
    private $matchPlayers;

    /**
     * @ORM\ManyToOne(targetEntity="Tfts\Entity\Game", inversedBy="matches")
     * @ORM\JoinColumn(name="match_game_id", referencedColumnName="game_id")
     */
    private $game;
}