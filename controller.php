<?php
namespace Concrete\Package\TuricaneTfts;

use Package,
    Concrete\Core\Backup\ContentImporter,
    Core,
    Config,
    Events;

class Controller extends Package
{
    protected $pkgHandle = 'turicane_tfts';
    protected $appVersionRequired = '8.4';
    protected $pkgVersion = '0.9';

    public function getPackageName()
    {
        return t('Turicane Fun Tourney System Theme Package');
    }

    public function getPackageDescription()
    {
        return t('');
    }

    public function on_start()
    {

    }

    public function install()
    {
        $pkg = parent::install();
        $ci = new ContentImporter();
        $ci->importContentFile($pkg->getPackagePath() . '/install.xml');
    }

    public function upgrade()
    {
        parent::upgrade();
    }

    public function uninstall(){

    }
}