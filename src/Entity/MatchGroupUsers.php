<?php

namespace Tfts\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="tftsMatchGroupUsers",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="match_id", columns={"match_id","group_id","user_id"})}
 * )
 */
class MatchGroupUser {

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Tfts\Entity\Match", inversedBy="matchPlayers")
     * @ORM\JoinColumn(name="match_id", referencedColumnName="match_id", nullable=false)
     */
    private $match;

    /**
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\User\User", inversedBy="matchPlayers")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="uID", nullable=false)
     */
    private $user;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=10, nullable=false))
     */
    private $group_id;

}
