<?php
namespace Concrete\Package\TuricaneTfts;

use Concrete\Core\Database\EntityManager\Provider\ProviderInterface;
use Concrete\Core\Database\EntityManagerFactory;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Package,
    Concrete\Core\Backup\ContentImporter,
    Core,
    Config,
    Events;

class Controller extends Package implements ProviderInterface
{
    protected $pkgHandle = 'turicane_tfts';
    protected $appVersionRequired = '8.4';
    protected $pkgVersion = '0.91';
    protected $em;

    protected $pkgAutoloaderRegistries = array(
        'src/Tfts' => '\TuricaneTfts'
    );

    public function getPackageName()
    {
        return t('Turicane Fun Tourney System Theme Package');
    }

    public function getPackageDescription()
    {
        return t('');
    }

    public function getDrivers()
    {
        return [];
    }

    public function on_start()
    {
        $this->registerEntityManager();
        $this->registerEntityEvents();
        $this->registerRoutes();
    }

    public function install()
    {
        $pkg = parent::install();
        $ci = new ContentImporter();
        $ci->importContentFile($pkg->getPackagePath() . '/install.xml');

        $this->registerEntityManager();
        $this->refreshProxyClasses();
    }

    public function upgrade()
    {
        parent::upgrade();

        $this->registerEntityManager();
    }

    public function uninstall(){

    }

    public function registerRoutes()
    {
        $router = $this->app->make('router');
        // This Route is needed to "capture" the Data sent by the Trackmania result logger
        $router->get('/api/trackmania', 'TuricaneTfts\Tfts::processTrackmaniaData');
    }


    /**
     * In Order to use Doctrine Entities with our separate TFTS Database we need a Custom EntityManager instance.
     * We are creating that here.
     */
    public function registerEntityManager()
    {
        //$this->app->singleton();

        /*$this->app->singleton(Concrete\Package\TuricaneTfts\TftsEntityManager::class, function ($app) {
            $driver = new AnnotationDriver($this->app->make('orm/cachedAnnotationReader'), [DIR_BASE . '/' . DIRNAME_PACKAGES . '/turicane_tfts/src/Entity/Tfts']);
        $driverChain = new MappingDriverChain();
        $driverChain->addDriver($driver, '\Concrete\Package\TuricaneTft\Entity\Tfts');
        $ormConfiguration = $this->app->make(\Doctrine\ORM\Configuration::class);
        $ormConfiguration->setMetadataDriverImpl($driverChain);
        $databaseManager = $this->app->make(\Concrete\Core\Database\DatabaseManager::class);
        $connection = $databaseManager->connection('turicane_tfts');

        $entityManager = new EntityManagerFactory($connection, $ormConfiguration, $connection->getEventManager());
        return $entityManager;
        });*/
    }

    private function registerEntityEvents()
    {
        $app = $this->app;
        $director = $app->make(\Symfony\Component\EventDispatcher\EventDispatcher::class);
        $director->addListener('on_list_package_entities', function ($event) use ($app) {
            $event->addEntityManager($app->make(Concrete\Package\TuricaneTfts\TftsEntityManager::class));
        });
        $director->addListener('on_refresh_package_entities', function ($event) use ($app) {
            $app->make(Concrete\Package\TuricaneTfts\TftsEntityManager::class)->refreshProxyClasses();
        });
    }
}