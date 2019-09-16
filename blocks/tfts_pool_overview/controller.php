<?php
namespace Concrete\Package\TuricaneFunTourneySystem\Block\TftsPoolOverview;

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
    protected $btHandle = 'tfts_pool_overview';

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
        return t("TFTS Pool Overview");
    }

    public function save($args)
    {
        parent::save($args);
    }

    public function view()
    {
        // Here we could make use of the Notification System to send Team Invites to Users and having
        // them accept or decline them: https://documentation.concrete5.org/tutorials/how-to-create-alert-notifications-and-modals

    }
}
