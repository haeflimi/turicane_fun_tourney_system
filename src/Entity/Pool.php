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
   * @ORM\ManyToMany(targetEntity="Tfts\Entity\Pool")
   * @ORM\JoinTable(
   *     name="tftsPoolParentChild",
   *     joinColumns={@ORM\JoinColumn(name="pool_id", referencedColumnName="parent_id", nullable=false)},
   *     inverseJoinColumns={@ORM\JoinColumn(name="child_id", referencedColumnName="pool_id", nullable=false)}
   * )
   */
  private $parents;

/**
   * @ORM\ManyToMany(targetEntity="Tfts\Entity\Pool")
   * @ORM\JoinTable(
   *     name="tftsPoolParentChild",
   *     joinColumns={@ORM\JoinColumn(name="pool_id", referencedColumnName="child_id", nullable=false)},
   *     inverseJoinColumns={@ORM\JoinColumn(name="parent_id", referencedColumnName="pool_id", nullable=false)}
   * )
   */
  private $children;

}
