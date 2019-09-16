<?php
namespace Concrete\Package\TuricaneFunTourneySystem\Block\TftsLiveResults;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Page\PageList;
use Site;
use Group;
use Core;
use Database;
use Page;
use Permissions;
use \DateTime;

class Controller extends BlockController
{
    public $collection;
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = false;
    protected $btCacheBlockOutputLifetime = 300;
    protected $btHandle = 'tfts_live_results';

    public function __construct($obj = null)
    {
        parent::__construct($obj);
    }

    public function getBlockTypeDescription()
    {
        return t("");
    }

    public function getBlockTypeName()
    {
        return t("TFTS Live Results");
    }

    public function save($args)
    {
        parent::save($args);
    }

    public function view()
    {


    }
}
