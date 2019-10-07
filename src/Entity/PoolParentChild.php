<?php

namespace Tfts\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *    name="tftsPoolParentChild",
 *    uniqueConstraints={@ORM\UniqueConstraint(name="child_id", columns={"child_id","parent_id"})}
 * )
 */
class PoolParentChild {

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Tfts\Entity\Pool", inversedBy="poolChildren")
   * @ORM\JoinColumn(name="parent_id", referencedColumnName="pool_id", nullable=false)
   */
  private $parent;

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Tfts\Entity\Pool", inversedBy="poolParents")
   * @ORM\JoinColumn(name="child_id", referencedColumnName="pool_id", nullable=false)
   */
  private $child;

}
