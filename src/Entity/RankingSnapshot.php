<?php

namespace Tfts\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tftsRankingSnapshots")
 */
class RankingSnapshot
{
    /**
     * @ORM\Id
     * @ORM\Column(type="datetime", options={"default":"CURRENT_TIMESTAMP"})
     */
    private $timestamp;

    /**
     * @ORM\Column(type="integer", length=10, nullable=true, options={"default":0})
     */
    private $points;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\User\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="uID", nullable=false)
     */
    private $user;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Tfts\Entity\Lan", inversedBy="rankingSnapshots")
     * @ORM\JoinColumn(name="lan_id", referencedColumnName="lan_id", nullable=false)
     */
    private $lan;
}