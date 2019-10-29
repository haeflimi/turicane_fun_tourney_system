<?php

namespace Concrete\Package\TuricaneFunTourneySystem\Controller\SinglePage\Dashboard\Tfts;

use Concrete\Core\Entity\User\User as UserEntity;
use Concrete\Core\User\UserList;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Support\Facade\Config;
use Tfts\Tfts;
use Tfts\Game;
use Tfts\Map;
use Tfts\MapRecord;

class Maps extends DashboardPageController {

  public function __construct($obj = null) {
    parent::__construct($obj);
    $this->db = \Database::connection();
    $this->qb = $this->db->createQueryBuilder();
    $this->em = $this->db->getEntityManager();
  }

  public function getCollectionDescription() {
    return t("Manage the maps within the Turicane Fun Tourney System");
  }

  public function view() {
    $repository = $this->em->getRepository(Game::class);
    $games = $repository->findBy(['lan' => Config::get('tfts.currentLanId')]);

    $mapGames = [];
    foreach ($games as $game) {
      if (sizeof($game->getMaps()) > 0) {
        $mapGames[] = $game;
      }
    }

    $userList = new UserList();
    $userList->sortByUserName();
    $this->set('users', $userList->getResults());

    $this->set('tfts', new Tfts());
    $this->set('games', $games);
    $this->set('mapGames', $mapGames);
  }

  public function addMap() {
    try {
      $game = Game::getById($this->post('game_id'));
      $this->em->persist(new Map($game, $this->post('map_name'), $this->post('map_data_resolution'), $this->post('map_data_unit')));
      $this->em->flush();
    } catch (Exception $ex) {
      // @TODO: show exception in a user-readable form
      throw $ex;
    }
    $this->reloadPage();
  }

  public function deleteMap() {
    try {
      $this->em->remove($this->em->find(Map::class, $this->post('map_id')));
      $this->em->flush();
    } catch (Exception $ex) {
      // @TODO: show exception in a user-readable form
      throw $ex;
    }
    $this->reloadPage();
  }

  public function processMap() {
    $tfts = new Tfts();
    try {
      $tfts->processMap($this->post('map_id'));
    } catch (Exception $ex) {
      // @TODO: show exception in a user-readable form
      throw $ex;
    }
    $this->reloadPage();
  }

  public function addRecord() {
    try {
      $user = $this->em->find(UserEntity::class, $this->post('user_id'));
      $map = $this->em->find(Map::class, $this->post('map_id'));
      $this->em->persist(new MapRecord($user, $map, new \DateTime("now"), $this->post('record')));
      $this->em->flush();
    } catch (Exception $ex) {
      // @TODO: show exception in a user-readable form
      throw $ex;
    }
    $this->reloadPage();
  }

  public function modifyRecord() {
    try {
      $repository = $this->em->getRepository(MapRecord::class);
      $record = $repository->findOneBy(['map' => $this->post('map_id'), 'user' => $this->post('user_id')]);
      if ($this->post('delete') == 'Delete') {
        $this->em->remove($record);
      } else if ($this->post('update') == 'Update') {
        $record->setRecord($this->post('record'));
        $this->em->persist($record);
      } else {
        $this->reloadPage();
        return;
      }
      $this->em->flush();
    } catch (Exception $ex) {
      // @TODO: show exception in a user-readable form
      throw $ex;
    }
    $this->reloadPage();
  }

  private function reloadPage() {
    $page = \Page::getCurrentPage();
    $this->redirect($page->getCollectionPath());
  }

}
