<?php

namespace Concrete\Package\TuricaneFunTourneySystem;

use Concrete\Core\Asset\AssetList;
use Concrete\Core\Database\CharacterSetCollation\Exception;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\Response;
use Concrete\Core\Package\Package;
use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Database\EntityManager\Provider\ProviderAggregateInterface;
use Concrete\Core\Database\EntityManager\Provider\StandardPackageProvider;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User;
use Concrete\Core\View\View;
use Tfts\Game;
use Tfts\Match;
use Concrete\Core\Foundation\ClassLoader;
use Concrete\Core\Support\Facade\Events;
use Tfts\Tfts;
use Core;

class Controller extends Package {

  protected $pkgHandle = 'turicane_fun_tourney_system';
  protected $appVersionRequired = '8.4';
  protected $pkgVersion = '0.120.36';
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

    Events::addListener('tfts_on_match_finsh', function($match) {
          //@todo: Do stuff like sending the event to pusher for UI Refreshes, trigger Popups and so on.
    });

    // Register JS Assets for TFTS and c5 Backend Stuff for Notifications
    $al->register('javascript', 'tfts', 'js/tfts.js',
            array('minify' => false, 'combine' => false), $pkg
    );
    $view->requireAsset('javascript', 'tfts');
    $view->requireAsset('core/app');
  }

  public function on_after_packages_start() {
    // Register Routes
    $this->registerRoutes();
    
    $current_user = new User();
    $view = new View();
    
    // Check for open user challenges/confirmations to display
    if ($current_user->isLoggedIn()) {
      $tfts = new Tfts();
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
    
    // @TODO: Check for open user challenges/confirmations to display
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

  public function registerRoutes() {
    $router = $this->app->make('router');
    // This Route is needed to "capture" the Data sent by the Trackmania result logger
    $router->post('/tfts/api/trackmania', 'Tfts\Tfts::processTrackmaniaData');
    // Register other routes for interface actions and pass them along to the tfts
    $router->post('/tfts/api/action', function () {
      if ($this->validateRequestToken($_POST, $_POST['action'])) {
        $tfts = new Tfts();
        try {
          switch ($_POST['action']) {
            case 'joinUserPool':
              $tfts->joinUserPool($_POST['game_id'], $_POST['user_id']);
              break;

            case 'leaveUserPool':
              $tfts->leaveUserPool($_POST['game_id'], $_POST['user_id']);
              break;

            case 'challengeUser':
              $tfts->challengeUser($_POST['game_id'], $_POST['challenger_id'], $_POST['challenged_id']);
              break;

            case 'withdrawUserChallenge':
              $tfts->withdrawUserChallenge($_POST['match_id'], $_POST['challenger_id']);
              break;

            case 'acceptUserChallenge':
              $tfts->acceptUserChallenge($_POST['match_id'], $_POST['challenged_id']);
              break;

            case 'declineUserChallenge':
              $tfts->declineUserChallenge($_POST['match_id'], $_POST['challenged_id']);
              break;

            case 'reportResultUserMatch':
              $tfts->reportResultUserMatch($_POST['match_id'], $_POST['user_id'], $_POST['score1'], $_POST['score2']);
              break;

            case 'cancelUserMatch':
              $tfts->cancelUserMatch($_POST['match_id'], $_POST['user_id']);
              break;

            case 'joinGroupPool':
              $tfts->joinGroupPool($_POST['game_id'], $_POST['group_id']);
              break;

            case 'leaveGroupPool':
              $tfts->leaveGroupPool($_POST['game_id'], $_POST['group_id']);
              break;

            case 'challengeGroup':
              $tfts->challengeGroup($_POST['game_id'], $_POST['challenger_id'], $_POST['challenged_id']);
              break;

            case 'withdrawGroupChallenge':
              $tfts->withdrawGroupChallenge($_POST['match_id'], $_POST['challenger_id']);
              break;

            case 'acceptGroupChallenge':
              $tfts->acceptGroupChallenge($_POST['match_id'], $_POST['challenged_id']);
              break;

            case 'declineGroupChallenge':
              $tfts->declineGroupChallenge($_POST['match_id'], $_POST['challenged_id']);
              break;

            case 'reportResultGroupMatch':
              $tfts->reportResultGroupMatch($_POST['match_id'], $_POST['group_id'], $_POST['score1'], $_POST['score2'], $_POST['user_ids']);
              break;

            case 'cancelGroupMatch':
              $tfts->cancelGroupMatch($_POST['match_id'], $_POST['group_id']);
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
