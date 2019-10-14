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
use Tfts\Tfts;
use Core;

class Controller extends Package
{

    protected $pkgHandle = 'turicane_fun_tourney_system';
    protected $appVersionRequired = '8.4';
    protected $pkgVersion = '0.120.33';
    protected $em;
    protected $pkgAutoloaderRegistries = array(
        'src/Tfts' => '\Tfts',
        'src/Entity' => '\Tfts',
    );

    public function getPackageName()
    {
        return t('Turicane Fun Tourney System Package');
    }

    public function getPackageDescription()
    {
        return t('');
    }

    public function on_start()
    {
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
    }

    public function on_after_packages_start()
    {
        // Register Routes
        $this->registerRoutes();
        // Load some stuff
        $u = new User();
        $em = $this->getPackageEntityManager();
        $view = new View();
        // Check TFTS for Open Challanges to display as Notifications
        if ($u->isLoggedIn()) {
            $tfts = new Tfts();
            $challenges = $tfts->getOpenUserChallenges($u);
            foreach ($challenges as $match) {
                $notification = Core::make('helper/tfts/ui')->challenge($match);
                $view->addFooterItem($notification);
            }
            $confirmations = $tfts->getOpenUserConfirmations($u);
            foreach ($confirmations as $match) {
                $notification = Core::make('helper/tfts/ui')->confirm($match);
                $view->addFooterItem($notification);
            }
        };
    }

    public function install()
    {
        $pkg = parent::install();
        $ci = new ContentImporter();
        $ci->importContentFile($pkg->getPackagePath() . '/install.xml');
        $this->installDatabase();
    }

    public function upgrade()
    {
        parent::upgrade();
    }

    public function uninstall()
    {
        parent::uninstall();
    }

    public function registerRoutes()
    {
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
                            $resp = $tfts->joinUserPool($_POST['game_id'], $_POST['user_id']);
                            break;

                        case 'leaveUserPool':
                            $resp = $tfts->leaveUserPool($_POST['game_id'], $_POST['user_id']);
                            break;

                        case 'challengeUser':
                            $resp = $tfts->challengeUser($_POST['game_id'], $_POST['challenger_id'], $_POST['challenged_id']);
                            break;

                        case 'acceptUserChallenge':
                            $resp = $tfts->acceptUserChallenge($_POST['match_id'], $_POST['challenged_id']);
                            break;

                        case 'declineUserChallenge':
                            $resp = $tfts->declineUserChallenge($_POST['match_id'], $_POST['challenged_id']);
                            break;

                        case 'reportResultUserMatch':
                            $resp = $tfts->reportResultUserMatch($_POST['match_id'], $_POST['user_id'], $_POST['user1_score'], $_POST['user2_score']);
                            break;

                        case 'confirmResultUserMatch':
                            $resp = $tfts->confirmResultUserMatch($_POST['match_id'], $_POST['user_id']);
                            break;

                        case 'declineResultUserMatch':
                            $resp = $tfts->declineResultUserMatch($_POST['match_id'], $_POST['user_id']);
                            break;
                    }
                    if ($resp) {
                        return new Response('success');
                    } else {
                        return new Response('Something went wrong', 500);
                    }
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
    public function validateRequestToken($data, $action = false)
    {
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
