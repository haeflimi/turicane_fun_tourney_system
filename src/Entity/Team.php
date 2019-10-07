<?php

namespace Tfts\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tftsTeams", uniqueConstraints={@ORM\UniqueConstraint(name="team_id", columns={"team_id"})})
 */
class Team {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer", length=10)
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $team_id;

  /**
   * @ORM\Column(type="string", length=50, nullable=true)
   */
  private $team_name;

  /**
   * @ORM\Column(type="string", length=100, nullable=true)
   */
  private $team_password;

  /**
   * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\User\User", inversedBy="teams")
   * @ORM\JoinColumn(name="owner_id", referencedColumnName="uID")
   */
  private $owner;

  /**
   * @ORM\OneToMany(targetEntity="Tfts\Entity\Registration", mappedBy="team")
   */
  private $registrations;

  /**
   * @ORM\OneToMany(targetEntity="Tfts\Entity\MatchTeamUser", mappedBy="team")
   */
  private $matchTeamUsers;

  /**
   * @ORM\ManyToOne(targetEntity="Tfts\Entity\Lan", inversedBy="teams")
   * @ORM\JoinColumn(name="team_lan_id", referencedColumnName="lan_id", nullable=false)
   */
  private $lan;

  /**
   * @ORM\ManyToMany(targetEntity="Concrete\Core\Entity\User\User")
   * @ORM\JoinTable(
   *     name="tftsTeamUsers",
   *     joinColumns={@ORM\JoinColumn(name="team_id", referencedColumnName="team_id", nullable=false)},
   *     inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="uID", nullable=false)}
   * )
   */
  private $users;

}
