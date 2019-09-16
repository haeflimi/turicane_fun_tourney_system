<?php

namespace Tfts\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="tftsMatchesPlayers",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="match_id", columns={"match_id","team_id","user_id"})}
 * )
 */
class MatchPlayer
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=10, options={"default":0})
     */
    private $user_id;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Tfts\Entity\Match", inversedBy="matchPlayers")
     * @ORM\JoinColumn(name="match_id", referencedColumnName="match_id", nullable=false)
     */
    private $match;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Tfts\Entity\Team", inversedBy="matchPlayers")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="team_id", nullable=false)
     */
    private $team;
}