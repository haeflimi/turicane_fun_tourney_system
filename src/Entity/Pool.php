<?php

namespace Tfts\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tftsPools")
 */
class Pool {

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
   * @ORM\Column(type="integer", length=1, nullable=false, options={"default":0})
   */
  private $pool_is_played;

  /**
   * @ORM\ManyToOne(targetEntity="Tfts\Entity\Game", inversedBy="pools")
   * @ORM\JoinColumn(name="game_id", referencedColumnName="game_id", nullable=false)
   */
  private $game;

  /**
   * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\User\User")
   * @ORM\JoinColumn(name="host_id", referencedColumnName="uID", nullable=false)
   */
  private $host;

  /**
   * @ORM\OneToMany(targetEntity="Tfts\Entity\PoolUser", mappedBy="pool")
   */
  private $users;

  /**
   * @ORM\OneToMany(targetEntity="Tfts\Entity\PoolParentChild", mappedBy="pool")
   */
  private $parents;

  /**
   * @ORM\OneToMany(targetEntity="Tfts\Entity\PoolParentChild", mappedBy="pool")
   */
  private $children;

}
