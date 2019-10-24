<?php

namespace Tfts;

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Page\Page;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tftsGames")
 */
class Game {

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
   * @ORM\Column(type="integer", length=10, nullable=false)
   */
  private $game_page_id;

  /**
   * @ORM\ManyToOne(targetEntity="Tfts\Lan", inversedBy="games")
   * @ORM\JoinColumn(name="lan_id", referencedColumnName="lan_id", nullable=false)
   */
  private $lan;

  /**
   * @ORM\OneToMany(targetEntity="Tfts\Registration", mappedBy="game")
   */
  private $registrations;

  /**
   * @ORM\OneToMany(targetEntity="Tfts\Match", mappedBy="game")
   */
  private $matches;

  /**
   * @ORM\OneToMany(targetEntity="Tfts\Pool", mappedBy="game")
   */
  private $pools;

  /**
   * @ORM\OneToMany(targetEntity="Tfts\MapRecord", mappedBy="game")
   */
  private $mapRecords;
  private $game_page;

  public function __construct($game_handle) {
    $app = Application::getFacadeApplication();
    $this->em = $app->make('Doctrine\ORM\EntityManager');
    $game = $this->em->getRepository(Game::class)->findOneBy(['game_handle' => $game_handle]);
    // Init Collections
    $this->pools = new ArrayCollection();
    $this->matches = new ArrayCollection();
    $this->registrations = new ArrayCollection();
    return $game;
  }

  public static function getById($game_id): ?Game {
    $app = Application::getFacadeApplication();
    $em = $app->make('Doctrine\ORM\EntityManager');
    return $em->find(Game::class, $game_id);
  }

  public function getId(): int {
    return $this->game_id;
  }

  public function getLan(): Lan {
    return $this->lan;
  }

  public function getRegistrations(): Collection {
    return $this->registrations;
  }

  public function setLan(Lan $lan): Game {
    $this->lan = $lan;
    return $this;
  }

  public function setGameHandle($game_handle): Game {
    $this->game_handle = $game_handle;
    return $this;
  }

  public function setGamePageId($game_page_id): Game {
    $this->game_page_id = $game_page_id;
    return $this;
  }

  public static function addGame(Lan $lan, $game_handle, $game_page_id): Game {
    $game = new Game();
    $game->setLan($lan)
            ->setGameHandle($game_handle)
            ->setGamePageId($game_page_id);
    $app = Application::getFacadeApplication();
    $em = $app->make('Doctrine\ORM\EntityManager');
    $em->persist($game);
    $em->flush();
    return $game;
  }

  /**
   * The Following Getter Methods make use of c5 specific Features to store Game Information in Pages
   */
  public function getName(): string {
    if (!is_object($game_page = $this->getGamePage())) {
      return $this->game_handle;
    }
    return $game_page->getCollectionName();
  }

  public function getPointsWin(): int {
    if (!is_object($game_page = $this->getGamePage())) {
      return 0;
    }
    return (int) $game_page->getAttribute('tfts_game_points_win');
  }

  public function getPointsLoss(): int {
    if (!is_object($game_page = $this->getGamePage())) {
      return 0;
    }
    return (int) $game_page->getAttribute('tfts_game_points_loss');
  }

  public function isPool(): bool {
    if (!is_object($game_page = $this->getGamePage())) {
      return false;
    }
    return (bool) $game_page->getAttribute('tfts_game_is_pool');
  }

  public function isGroup(): bool {
    if (!is_object($game_page = $this->getGamePage())) {
      return false;
    }
    return (bool) $game_page->getAttribute('tfts_game_is_team');
  }

  public function isMass(): bool {
    if (!is_object($game_page = $this->getGamePage())) {
      return false;
    }
    return (bool) $game_page->getAttribute('tfts_game_is_mass');
  }

  public function getGroupSize(): int {
    if (!is_object($game_page = $this->getGamePage())) {
      return false;
    }
    return (int) $game_page->getAttribute('tfts_game_players');
  }

  public function getGamePage(): Page {
    if (empty($this->game_page_id)) {
      return null;
    }
    if (empty($this->game_page)) {
      $this->game_page = Page::getById($this->game_page_id);
    }
    return $this->game_page;
  }

  public function getGamePageURL() {
    if (!is_object($game_page = $this->getGamePage())) {
      return null;
    }
    return $game_page->getCollectionLink();
  }

  public function getMatches(): Collection {
    return $this->matches;
  }

  public function getOpenChallenges(): Collection {
    return $this->matches
                    ->filter(function(Match $match) {
                      return !$match->isAccepted();
                    });
  }

  public function getOpenMatches(): Collection {
    return $this->matches
                    ->filter(function(Match $match) {
                      return $match->isAccepted() && is_null($match->getFinishDate());
                    });
  }

  public function getClosedMatches(): Collection {
    return $this->matches
                    ->filter(function(Match $match) {
                      return !is_null($match->getFinishDate());
                    });
  }

  public function getPools(): Collection {
    return $this->pools;
  }

  public function getOpenPools(): Collection {
    return $this->pools
                    ->filter(function(Pool $pool) {
                      return !$pool->isPlayed();
                    });
  }

  public function getFinalPools(): Collection {
    return $this->pools
                    ->filter(function(Pool $pool) {
                      return $pool->isPlayed() && sizeof($pool->getChildren()) == 0;
                    });
  }

  public function getMapRecords(): Collection {
    return $this->mapRecords;
  }

}
