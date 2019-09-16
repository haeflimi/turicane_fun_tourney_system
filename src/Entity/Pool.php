<?php

namespace Tfts\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tftsPools")
 */
class Pool
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $pool_id;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $pool_name;

    /**
     * @ORM\Column(type="integer", length=10, nullable=false)
     */
    private $user_id;

    /**
     * 
     */
    private $pool_host_id;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false, options={"default":0})
     */
    private $pool_is_played;

    /**
     * @ORM\Column(type="string", length=20, nullable=false, options={"default":0})
     */
    private $pool_parent_ids;

    /**
     * @ORM\OneToMany(targetEntity="Tfts\Entity\PoolAllocation", mappedBy="pool")
     */
    private $poolAllocations;

    /**
     * @ORM\ManyToOne(targetEntity="Tfts\Entity\Game", inversedBy="pools")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="game_id")
     */
    private $game;
}