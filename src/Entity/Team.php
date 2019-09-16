<?php

namespace Tfts\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tftsTeams", uniqueConstraints={@ORM\UniqueConstraint(name="team_id", columns={"team_id"})})
 */
class Team
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $team_id;

    /**
     * @ORM\Column(type="integer", length=10, nullable=false, options={"default":0})
     */
    private $lan_id;

    /**
     * 
     */
    private $team_lan_id;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $team_name;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $team_password;

    /**
     * 
     */
    private $team_user_id;

    /**
     * @ORM\OneToMany(targetEntity="Tfts\Entity\MatchPlayer", mappedBy="team")
     */
    private $matchPlayers;

    /**
     * @ORM\OneToMany(targetEntity="Tfts\Entity\Registration", mappedBy="team")
     */
    private $registrations;

    /**
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\User\User", inversedBy="teams")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="uID")
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="Concrete\Core\Entity\User\User")
     * @ORM\JoinTable(
     *     name="tftsTeamAllocations",
     *     joinColumns={@ORM\JoinColumn(name="team_id", referencedColumnName="team_id", nullable=false)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="uID", nullable=false)}
     * )
     */
    private $users;
}