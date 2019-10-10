<?php
namespace Concrete\Package\TuricaneFunTourneySystem\Block\TftsPoolOverview;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Page\PageList;
use Concrete\Core\User\User;
use Site;
use Group;
use Core;
use Database;
use Page;
use Permissions;
use \DateTime;
use Tfts\Tfts;

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
        $app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
        $em = $app->make('Doctrine\ORM\EntityManager');
        $me = new User();
        $this->requireAsset('javascript', 'tfts');

        $page = Page::getCurrentPage();
        $this->set('is_pool', $page->getAttribute('tfts_game_is_pool'));

        $game = $em->getRepository('Tfts\Entity\Game')->findOneBy(['game_page_id'=>$page->getCollectionId()]);
        $myRegistration = $em->getRepository('Tfts\Entity\Registration')->findOneBy(['user'=>$me->getUserId()]);
        $tfts = new Tfts();
        $this->set('in_pool', (is_object($myRegistration)?true:false));
        $this->set('registrations', $tfts->getRegistrations($game));
        $this->set('openChallenges', $tfts->getOpenChallenges($game));
        $this->set('openMatches', $tfts->getOpenMatches($game));
        $this->set('closedMatches', $tfts->getClosedMatches($game));
    }
}
