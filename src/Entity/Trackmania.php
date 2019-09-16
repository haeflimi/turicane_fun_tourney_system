<?php

namespace Tfts\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tftsTrackmania")
 */
class Trackmania
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=100)
     */
    private $map;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $datetime;

    /**
     * @ORM\Column(type="time", nullable=false)
     */
    private $record;

    /**
     * @ORM\Column(type="integer", length=10, nullable=false)
     */
    private $miliseconds;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\User\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="uID", nullable=false)
     */
    private $user;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Tfts\Entity\Lan", inversedBy="trackmanias")
     * @ORM\JoinColumn(name="lan_id", referencedColumnName="lan_id", nullable=false)
     */
    private $lan;
}