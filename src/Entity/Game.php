<?php

namespace Tfts\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="tftsGames",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="game_id", columns={"game_id", "lan_id"})}
 * )
 */
class Game
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $game_id;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $game_handle;

    /**
     * @ORM\Column(type="integer", length=10)
     */
    private $game_page_id;

    /**
     * @ORM\ManyToOne(targetEntity="Tfts\Entity\Lan", inversedBy="games")
     * @ORM\JoinColumn(name="lan_id", referencedColumnName="lan_id", nullable=false)
     */
    private $lan;

    /**
     * @ORM\OneToMany(targetEntity="Tfts\Entity\Registration", mappedBy="game")
     */
    private $registrations;

    /**
     * @ORM\OneToMany(targetEntity="Tfts\Entity\Match", mappedBy="game")
     */
    private $matches;

    /**
     * @ORM\OneToMany(targetEntity="Tfts\Entity\Pool", mappedBy="game")
     */
    private $pools;

    private $game_page;

    public function __construct($game_handle)
    {
        $app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
        $this->em = $app->make('Doctrine\ORM\EntityManager');
        $game = $this->em->getRepository('Tfts\Entity\Game')->findOneBy(['game_handle' => $game_handle]);
        return $game;
    }

    public static function getById($id)
    {
        $app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
        $em = $app->make('Doctrine\ORM\EntityManager');
        return $em->find('Tfts\Entity\Game', $id);
    }

    public function getId()
    {
        return $this->game_id;
    }

    public function getLan()
    {
        return $this->lan;
    }

    public function getRegistrations()
    {
        return $this->registrations;
    }

    public function getMatches()
    {
        return $this->matches;
    }

    public function getPools()
    {
        return $this->pools;
    }

    public function setLan(Lan $lan)
    {
        $this->lan = $lan;
        return $this;
    }

    public function setGameHandle($game_handle)
    {
        $this->game_handle = $game_handle;
        return $this;
    }

    public function setGamePageId($game_page_id)
    {
        $this->game_page_id = $game_page_id;
        return $this;
    }

    public static function addGame(Lan $lan, $game_handle, $game_page_id)
    {
        $game = new Game();
        $game->setLan($lan)
            ->setGameHandle($game_handle)
            ->setGamePageId($game_page_id);
        $app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
        $em = $app->make('Doctrine\ORM\EntityManager');
        $em->persist($game);
        $em->flush();
        return $game;
    }

    /**
     * The Following Getter Methods make use of c5 specific Features to store Game Information in Pages
     */

    public function getName()
    {
        if (!is_object($game_page = $this->getGamePage())) {
            return $this->game_handle;
        }
        return $game_page->getCollecitonName();

    }

    public function getPointsWin()
    {
        if (!is_object($game_page = $this->getGamePage())) {
            return null;
        }
        return $game_page->getAttributes('tfts_game_points_win');
    }

    public function getPointsLoss()
    {
        if (!is_object($game_page = $this->getGamePage())) {
            return null;
        }
        return $game_page->getAttributes('tfts_game_points_loss');
    }

    public function getIsPool()
    {
        if (!is_object($game_page = $this->getGamePage())) {
            return null;
        }
        return $game_page->getAttributes('tfts_game_is_pool');
    }

    public function getIsTeam()
    {
        if (!is_object($game_page = $this->getGamePage())) {
            return null;
        }
        return $game_page->getAttributes('tfts_game_is_team');
    }

    public function getIsMass()
    {
        if (!is_object($game_page = $this->getGamePage())) {
            return null;
        }
        return $game_page->getAttributes('tfts_game_is_mass');
    }

    public function getGamePage()
    {
        if (empty($this->game_page_id)) {
            return null;
        }
        if (empty($this->game_page)) {
            $this->game_page = Page::getById($this->game_page_id);
        }
        return $this->game_page;
    }

    public function getGamePageURL()
    {
        if (!is_object($game_page = $this->getGamePage())) {
            return null;
        }
        return $game_page->getCollectionLink();
    }


}
