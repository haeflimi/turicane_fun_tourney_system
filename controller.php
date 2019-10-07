<?php

namespace Concrete\Package\TuricaneFunTourneySystem;

use Concrete\Core\Database\EntityManager\Provider\ProviderInterface;
use Concrete\Core\Package\Package;
use Concrete\Core\Backup\ContentImporter;
use Concrete\Core;
use Concrete\Core\Database\EntityManager\Provider\ProviderAggregateInterface;
use Concrete\Core\Database\EntityManager\Provider\StandardPackageProvider;
use Concrete\Core\Support\Facade\Config;
use Concrete\Core\Support\Facade\Events;

class Controller extends Package implements ProviderAggregateInterface {

  protected $pkgHandle = 'turicane_fun_tourney_system';
  protected $appVersionRequired = '8.4';
  protected $pkgVersion = '0.111';
  protected $em;
  protected $pkgAutoloaderRegistries = array(
      'src/Tfts' => '\Tfts'
  );

  public function getPackageName() {
    return t('Turicane Fun Tourney System Package');
  }

  public function getPackageDescription() {
    return t('');
  }

  public function getEntityManagerProvider() {
    $provider = new StandardPackageProvider($this->app, $this, [
        'src/Entity' => 'Tfts\Entity'
    ]);
    return $provider;
  }

  public function on_start() {
    $this->registerRoutes();
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
    $router->get('/tfts/api/trackmania', 'Tfts\Tfts::processTrackmaniaData');
  }

}
