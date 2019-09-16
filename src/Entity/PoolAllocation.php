<?php

namespace Tfts\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="tftsPoolAllocations",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="user_id", columns={"user_id","pool_id"})}
 * )
 */
class PoolAllocation
{
    /**
     * @ORM\Column(type="integer", length=10, nullable=false, options={"default":0})
     */
    private $rank;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Tfts\Entity\Pool", inversedBy="poolAllocations")
     * @ORM\JoinColumn(name="pool_id", referencedColumnName="pool_id", nullable=false)
     */
    private $pool;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\User\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="uID", nullable=false)
     */
    private $user;
}