<?php

namespace Tfts\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tftsLfg")
 */
class Lfg
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $lfg_id;

    /**
     * @ORM\Column(type="integer", length=10, nullable=false, options={"default":0})
     */
    private $created;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false, options={"default":0})
     */
    private $mode;

    /**
     * @ORM\ManyToOne(targetEntity="Tfts\Entity\Lan", inversedBy="lfgs")
     * @ORM\JoinColumn(name="lan_id", referencedColumnName="lan_id")
     */
    private $lan;

    /**
     * @ORM\ManyToOne(targetEntity="Tfts\Entity\Game", inversedBy="lfgs")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="game_id")
     */
    private $game;

    /**
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\User\User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="uID")
     */
    private $user;
}