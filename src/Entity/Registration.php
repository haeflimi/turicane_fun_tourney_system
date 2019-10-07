<?php

namespace Tfts\Entity;

use Concrete\Core\User\Group\Group;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="tftsRegistrations",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="user_id", columns={"user_id","group_id","game_id"})}
 * )
 */
class Registration {

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Tfts\Entity\Game", inversedBy="registrations")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="game_id", nullable=false)
     */
    private $game;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\User\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="uID", nullable=true)
     */
    private $user;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=10, nullable=true)
     */
    private $group_id;

    /**
     * @ORM\Column(type="integer", length=10, nullable=false)
     */
    private $rnd_number;

    public function __construct(Game $game, User $user = null, Group $group = null) {
        $this->game = $game;
        $this->user = $user;
        $this->group_id = $group->getPermissionObjectIdentifier();
    }

    public function getGame() {
        return $this->game;
    }

    public function getUser() {
        return $this->user;
    }

    public function getGroupId() {
        return $this->group_id;
    }

}
