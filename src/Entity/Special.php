<?php

namespace Tfts\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tftsSpecials")
 */
class Special
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $special_id;

    /**
     * @ORM\Column(type="integer", length=10, nullable=false)
     */
    private $special_points;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $special_desc;

    /**
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\User\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="uID")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Tfts\Entity\Lan", inversedBy="specials")
     * @ORM\JoinColumn(name="lan_id", referencedColumnName="lan_id")
     */
    private $lan;
}