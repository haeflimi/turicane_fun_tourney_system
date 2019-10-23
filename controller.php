<?php

namespace Concrete\Package\TuricaneFunTourneySystem;

use Concrete\Core\Asset\AssetList;
use Concrete\Core\Database\CharacterSetCollation\Exception;
use Concrete\Core\Http\Response;
use Concrete\Core\Package\Package;
use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\User\User;
use Concrete\Core\View\View;
use Concrete\Core\Support\Facade\Events;
use Pusher\Pusher;
use Tfts\Tfts;
use Core;
use Config;

class Controller extends Package {

  protected $pkgHandle = 'turicane_fun_tourney_system';
  protected $appVersionRequired = '8.4';
  protected $pkgVersion = '0.120.49';
  protected $em;
  protected $pkgAutoloaderRegistries = array(
      'src/Tfts' => '\Tfts',
      'src/Entity' => '\Tfts',
  );

  public function getPackageName() {
    return t('Turicane Fun Tourney System Package');
  }

  public function getPackageDescription() {
    return t('');
  }

  public function on_start() {
    // Makte the Notification Class available everywhere
    Core::singleton('helper/tfts/ui', function () {
      return new \Tfts\Notification();
    });

    // Load some stuff
    $view = new View();
    $al = AssetList::getInstance();
    $pkg = Package::getByHandle($this->pkgHandle);


    // Register JS Assets for TFTS and c5 Backend Stuff for Notifications
    $al->register('javascript', 'tfts', 'js/tfts.js',
            array('minify' => false, 'combine' => false), $pkg
    );
    $view->requireAsset('javascript', 'tfts');
    $view->requireAsset('core/app');

    // Register JS Assets that are used for the live Ranking
    $al->register('javascript', 'pusher', 'js/pusher.min.js',
            array('minify' => false, 'combine' => false, 'position' => \Concrete\Core\Asset\Asset::ASSET_POSITION_HEADER), $pkg
    );
    $al->register('javascript', 'vue', 'js/vue.js',
            array('minify' => false, 'combine' => false, 'position' => \Concrete\Core\Asset\Asset::ASSET_POSITION_HEADER), $pkg
    );

    $al->register('javascript', 'timeago', 'js/timeago.js',
            array('minify' => false, 'combine' => false), $pkg
    );

    $al->register('javascript', 'slimScroll', 'js/jquery.slimscroll.js',
            array('minify' => false, 'combine' => false), $pkg
    );

    $al->registerGroup('live_ranking', array(
        array('javascript', 'vue'),
        array('javascript', 'pusher'),
        array('javascript', 'slimScroll'),
        array('javascript', 'timeago')
    ));
  }

  public function on_after_packages_start() {
    // Register Routes
    $this->registerRoutes();
    // Register Event Listeners
    $this->registerEventListeners();

    $current_user = new User();
    $view = new View();

    // Check for open user challenges/confirmations to display
    if ($current_user->isLoggedIn() && is_numeric(Config::get('tfts.currentLanId'))) {
      $tfts = new Tfts();
      $tfts->createRankingSnapshot();
      $challenges = $tfts->getOpenUserChallenges($current_user);
      foreach ($challenges as $match) {
        $notification = Core::make('helper/tfts/ui')->showUserChallenge($match);
        $view->addFooterItem($notification);
      }
      $confirmations = $tfts->getOpenUserConfirmations($current_user);
      foreach ($confirmations as $match) {
        $notification = Core::make('helper/tfts/ui')->confirmUserResult($match);
        $view->addFooterItem($notification);
      }
    }
  }

  public function install() {
    $pkg = parent::install();
    $ci = new ContentImporter();
    $ci->importContentFile($pkg->getPackagePath() . '/install.xml');
    $this->installDatabase();
  }

  public function upgrade() {
    parent::upgrade();
  }

  public function uninstall() {
    parent::uninstall();
  }

  public function registerEventListeners() {
    Events::addListener('tfts_on_match_finsh', function ($event) {
//            $match = $event->getArgument('match');
      $options = array(
          'cluster' => Config::get('turicane.pusher.app_cluster'),
          'encrypted' => true
      );
      $pusher = new Pusher(
              Config::get('turicane.pusher.app_key'),
              Config::get('turicane.pusher.app_secret'),
              Config::get('turicane.pusher.app_id'),
              $options
      );
      $data = [];
      // Trigger a update of the ranking List
      $pusher->trigger('rankingList', 'update', json_encode($data));
    });

    // if we do this it might cause exceeding of our pusher limit - so better not do it
    /* Events::addListener('tfts_on_trackmania_add', function () {
      $options = array(
      'cluster' => Config::get('turicane.pusher.app_cluster'),
      'encrypted' => true
      );
      $pusher = new Pusher(
      Config::get('turicane.pusher.app_key'),
      Config::get('turicane.pusher.app_secret'),
      Config::get('turicane.pusher.app_id'),
      $options
      );
      $data = [];
      // Trigger a update of the ranking List
      $pusher->trigger('trackmaniaRankingList', 'update', json_encode($data));
      }); */
  }

  public function registerRoutes() {
    $router = $this->app->make('router');
    // This Route is needed to "capture" the Data sent by the Trackmania result logger
    $router->post('/tfts/api/map', 'Tfts\Tfts::processMapData');
    // Register routes for getting list Data
    $router->get('/tfts/api/rankingList', 'Tfts\Tfts::getRankingList');
    // $router->get('/tfts/api/trackmaniaRankingList', 'Tfts\Tfts::getTrackmaniaRankingList');
    // Register other routes for interface actions and pass them along to the tfts
    $router->post('/tfts/api/action', function () {
      if ($this->validateRequestToken($_POST, $_POST['action'])) {
        $tfts = new Tfts();
        try {
          switch ($_POST['action']) {
            case 'joinPool':
              if ($_POST['is_team'] == 1) {
                $tfts->joinGroupPool($_POST['game_id'], $_POST['id']);
              } else {
                $tfts->joinUserPool($_POST['game_id'], $_POST['id']);
              }
              break;

            case 'leavePool':
              if ($_POST['is_team'] == 1) {
                $tfts->leaveGroupPool($_POST['game_id'], $_POST['id']);
              } else {
                $tfts->leaveUserPool($_POST['game_id'], $_POST['id']);
              }
              break;

            case 'createChallenge':
              if ($_POST['is_team'] == 1) {
                $tfts->challengeGroup($_POST['game_id'], $_POST['challenger_id'], $_POST['challenged_id']);
              } else {
                $tfts->challengeUser($_POST['game_id'], $_POST['challenger_id'], $_POST['challenged_id']);
              }
              break;

            case 'withdrawChallenge':
              if ($_POST['is_team'] == 1) {
                $tfts->withdrawGroupChallenge($_POST['match_id'], $_POST['challenger_id']);
              } else {
                $tfts->withdrawUserChallenge($_POST['match_id'], $_POST['challenger_id']);
              }
              break;

            case 'acceptChallenge':
              if ($_POST['is_team'] == 1) {
                $tfts->acceptGroupChallenge($_POST['match_id'], $_POST['challenged_id']);
              } else {
                $tfts->acceptUserChallenge($_POST['match_id'], $_POST['challenged_id']);
              }
              break;

            case 'declineChallenge':
              if ($_POST['is_team'] == 1) {
                $tfts->declineGroupChallenge($_POST['match_id'], $_POST['challenged_id']);
              } else {
                $tfts->declineUserChallenge($_POST['match_id'], $_POST['challenged_id']);
              }
              break;

            case 'reportResultMatch':
              if ($_POST['is_team'] == 1) {
                $tfts->reportResultGroupMatch($_POST['match_id'], $_POST['id'], $_POST['score1'], $_POST['score2'], is_null($_POST['user_ids']) ? [] : $_POST['user_ids']);
              } else {
                $tfts->reportResultUserMatch($_POST['match_id'], $_POST['id'], $_POST['score1'], $_POST['score2']);
              }
              break;

            case 'cancelMatch':
              if ($_POST['is_team'] == 1) {
                $tfts->cancelGroupMatch($_POST['match_id'], $_POST['id']);
              } else {
                $tfts->cancelUserMatch($_POST['match_id'], $_POST['id']);
              }
              break;
          }
          return new Response('Success');
        } catch (Exception $e) {
          return new Response('Error', 500);
        }
      } else {
        return new Response('Invalid Request Token.', 500);
      }
    });
  }

  /**
   * Validate a Post Request for a token
   *
   * @param $data
   * @param bool $action
   * @return bool|\Concrete\Core\Error\Error
   */
  public function validateRequestToken($data, $action = false) {
    $errors = new \Concrete\Core\Error\Error();
    // we want to use a token to validate each call in order to protect from xss and request forgery
    $token = \Core::make("token");
    if ($action && !$token->validate($action)) {
      $errors->add('Invalid Request, token must be valid.');
    }
    if ($errors->has()) {
      return $errors;
    }
    return true;
  }

}
