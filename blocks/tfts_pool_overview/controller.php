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
        $tfts = new Tfts();

        $page = Page::getCurrentPage();
        $this->set('is_pool', $page->getAttribute('tfts_game_is_pool'));
        $this->set('is_team', $is_team = $page->getAttribute('tfts_game_is_team'));

        //get the game from the page the block is inserted in
        $game = $em->getRepository('Tfts\Game')->findOneBy(['game_page_id'=>$page->getCollectionId()]);
        $this->set('tfts_game_id', $game->getId());

        //determine if i am member of the pool
        $myRegistration = $tfts->findRegistration($game,$me);
        $this->set('in_pool', (is_object($myRegistration)?true:false));
        //if its a team game we can get our team from there
        //@todo this does not yet work bc. findRegistration doesnt find our team registration yet hardcode for now
        $this->set('myTeam', (is_object($myRegistration)?$myRegistration->getGroup():Group::getByID(70)));

        $this->set('me', $me);
        $this->set('registrations', $tfts->getRegistrations($game));
        $this->set('openChallenges', $tfts->getOpenGameChallenges($game));
        $this->set('openMatches', $tfts->getOpenMatches($game));
        $this->set('closedMatches', $tfts->getClosedMatches($game));
    }
}
