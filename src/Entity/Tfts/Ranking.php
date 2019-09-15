<?php
namespace Concrete\Package\TuricaneTfts\Entity\Tfts;

defined('C5_EXECUTE') or die(_("Access Denied."));

/**
 * @Entity()
 * @Table(name="t_lan_tfts_ranking")
 *
 */
class Ranking
{
    /**
     * @Id @Column(type="integer", options={"unsigned"=true})
     */
    protected $user_id;

    /**
     * @Id @Column(type="integer", options={"unsigned"=true})
     */
    protected $lan_id;

    /**
     * @Column(type="integer", nullable=true)
     */
    protected $points;


    public function getData()
    {
        return $this;
    }
}