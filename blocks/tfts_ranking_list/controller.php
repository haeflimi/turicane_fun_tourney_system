<?php

namespace Concrete\Package\TuricaneFunTourneySystem\Block\TftsRankingList;

use SimpleXMLElement;
use Concrete\Core\Block\BlockController;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\UserList;
use Concrete\Core\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Tfts\Entity\Game;
use Tfts\Tfts;

//use Concrete\Core\Support\Facade\Config;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Block Controller Class.
 *
 * Start building standards complient concrete5 blocks from me.
 *
 * @author   Oliver Green <oliver@c5labs.com>
 * @license  See attached license file
 */
class Controller extends BlockController {

  /**
   * The block types name.
   *
   * @var string
   */
  protected $btName = 'TFTS Ranking List';

  /**
   * The block types description.
   *
   * @var string
   */
  protected $btDescription = 'A block boilerplate to start building from.';

  /**
   * The block types handle.
   *
   * @var string
   */
  protected $btHandle = 'tfts_ranking_list';

  /**
   * The block types default set within the 'add block' fly out panel.
   * 
   * Valid sets included with the core are: 
   * basic, navigation, forms, social & multimedia.
   *
   * Leaving the value as null will add the block type to the 'other' set.
   *
   * @var string
   */
  protected $btDefaultSet = '';

  /**
   * The block types table name;
   * If left as null, the blocks handle will be used to form the table name.
   *
   * @var string
   */
  protected $btTable = 'btTftsRankingList';

  /**
   * The blocks form width.
   *
   * @var string
   */
  protected $btInterfaceWidth = '400';

  /**
   * The blocks form height.
   *
   * @var string
   */
  protected $btInterfaceHeight = '400';

  /* @section advanced */

  /**
   * Is this an internal block type?
   * If set to true the block will not be shown in the 'add block' flyout panel?
   *
   * @var bool
   */
  protected $btIsInternal = false;

  /**
   * Does the block support inline addition?
   *
   * @var bool
   */
  protected $btSupportsInlineAdd = false;

  /**
   * Does the block support inline editing?
   *
   * @var bool
   */
  protected $btSupportsInlineEdit = false;

  /**
   *  If true, container classes will not be wrapped around this block type in
   *  edit mode (if the theme in question supports a grid framework).
   *
   * @var bool
   */
  protected $btIgnorePageThemeGridFrameworkContainer = false;

  /**
   * Prevents the block from being aliased when duplicating a page or creating
   * a page from defaults, if true the block will be duplicated instead.
   *
   * @var bool
   */
  protected $btCopyWhenPropagate = false;

  /**
   * Returns whether this block type is included in all versions. Default is
   * false - block types are typically versioned but sometimes it makes
   * sense not to do so.
   *
   * @return bool
   */
  protected $btIncludeAll = false;

  /**
   * Here you can defined helpers that the blocks add
   * and edit forms require. They will be loaded automatically.
   *
   * @var array
   */
  protected $helpers = ['form'];

  /**
   * When block caching is enabled, this means that the block's database record
   * data will also be cached.
   *
   * @var bool
   */
  protected $btCacheBlockRecord = true;

  /**
   *  When block caching is enabled, enabling this boolean means that the output
   *  of the block will be saved and delivered without rendering the view()
   *  function or hitting the database at all.
   *
   * @var bool
   */
  protected $btCacheBlockOutput = false;

  /**
   * When block caching is enabled and output caching is enabled for a block,
   * this is the value in seconds that the cache will last before being refreshed.
   * (specified in seconds).
   *
   * @var bool
   */
  protected $btCacheBlockOutputLifetime = 3600;

  /**
   * This determines whether a block will cache its output on POST. Some blocks
   * can cache their output but must serve uncached output on POST in order to
   * show error messages, etc.
   *
   * @var bool
   */
  protected $btCacheBlockOutputOnPost = false;

  /**
   * Determines whether a block that can cache its output will continue to cache
   * its output even if the current user viewing it is logged in.
   *
   * @var bool
   */
  protected $btCacheBlockOutputForRegisteredUsers = false;

  /**
   * When this block is exported, any database columns found in this array will
   * automatically be swapped for links to specific pages. Upon import they will
   * map to the specific page found at that path, regardless of its ID.
   *
   * @var array
   */
  protected $btExportPageColumns = [];

  /**
   * When this block is exported, any database columns found in this array will
   * automatically be swapped for links to specific files, by file name. Upon
   * import they will map to the specific file with that filename, regardless
   * of its file ID.
   *
   * @var array
   */
  protected $btExportFileColumns = [];

  /**
   * When this block is exported, any database columns found in this array will
   * automatically be swapped for references to a particular page type. Upon import
   * they will map to that specific page type ID based on the handle specified.
   *
   * @var array
   */
  protected $btExportPageTypeColumns = [];

  /**
   * When this block is exported, any database columns found in this array will
   * automatically be swapped for a reference to a specific RSS feed object. Upon
   * import they will map to the specific feed, regardless of its ID in the database.
   *
   * @var array
   */
  protected $btExportPageFeedColumns = [];

  /**
   * Wraps the block view in a container element with the class specified here.
   *
   * @var string
   */
  protected $btWrapperClass = '';

  /* @endsection advanced */

  /**
   * Controller constructor.
   * @param null $obj
   */
  public function __construct($obj = null) {
    parent::__construct($obj);
    $app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
    $this->em = $app->make('Doctrine\ORM\EntityManager');
  }

  /**
   * Runs when the blocks view template is rendered.
   *
   * @return void
   */
  public function view() {
    $tfts = new Tfts();

    $usergame = false;
    $teamgame = false;
    $massgame = false;
    $trackmania = false;

    if ($usergame) {
      $this->simulateUserGame($tfts);
    }

    if ($teamgame) {
      $this->simulateGroupGame($tfts);
    }

    if ($massgame) {
      $this->simulateMassGame($tfts);
    }

    if ($trackmania) {
      $this->processTrackmaniaMap($tfts);
    }

    $tfts->createRankingSnapshot();
  }

  private function simulateUserGame(Tfts $tfts) {
    $freezer = $this->getUserByName('Freezer');
    $tuborg = $this->getUserByName('TuBorg');

    $hearthstone = $this->em->find(Game::class, 1);

    $tfts->joinUserPool($hearthstone, $freezer);
    $tfts->joinUserPool($hearthstone, $tuborg);

    $tfts->leaveUserPool($hearthstone, $freezer);
    $tfts->leaveUserPool($hearthstone, $tuborg);

    $tfts->joinUserPool($hearthstone, $freezer);
    $tfts->joinUserPool($hearthstone, $tuborg);

    $match1 = $tfts->challengeUser($hearthstone, $freezer, $tuborg);
    $tfts->withdrawUserChallenge($match1, $freezer);

    $match2 = $tfts->challengeUser($hearthstone, $freezer, $tuborg);
    $tfts->declineUserChallenge($match2, $tuborg);

    $match3 = $tfts->challengeUser($hearthstone, $freezer, $tuborg);
    $tfts->acceptUserChallenge($match3, $tuborg);
    $tfts->cancelUserMatch($match3, $freezer);

    $match4 = $tfts->challengeUser($hearthstone, $freezer, $tuborg);
    $tfts->acceptUserChallenge($match4, $tuborg);
    $tfts->reportResultUserMatch($match4, $freezer, 2, 1);
    $tfts->reportResultUserMatch($match4, $tuborg, 1, 2);
    $tfts->reportResultUserMatch($match4, $freezer, 1, 2);
  }

  private function simulateGroupGame(Tfts $tfts) {
    $fartingAsses = $this->getGroupByName('Farting Asses');
    $munschkin = $this->getGroupByName('Munschkin');

    $freezer = $this->getUserByName('Freezer');
    $tuborg = $this->getUserByName('TuBorg');
    $buddha = $this->getUserByName('Buddha');
    $xelsor = $this->getUserByName('Xelsor');

    if (!$freezer->inGroup($fartingAsses)) {
      $freezer->enterGroup($fartingAsses);
    }
    if (!$tuborg->inGroup($fartingAsses)) {
      $tuborg->enterGroup($fartingAsses);
    }
    if (!$buddha->inGroup($munschkin)) {
      $buddha->enterGroup($munschkin);
    }
    if (!$xelsor->inGroup($munschkin)) {
      $xelsor->enterGroup($munschkin);
    }

    $brawlhalla = $this->em->find(Game::class, 2);

    $tfts->joinGroupPool($brawlhalla, $fartingAsses);
    $tfts->joinGroupPool($brawlhalla, $munschkin);

    $tfts->leaveGroupPool($brawlhalla, $fartingAsses);
    $tfts->leaveGroupPool($brawlhalla, $munschkin);

    $tfts->joinGroupPool($brawlhalla, $fartingAsses);
    $tfts->joinGroupPool($brawlhalla, $munschkin);

    $match1 = $tfts->challengeGroup($brawlhalla, $fartingAsses, $munschkin);
    $tfts->withdrawGroupChallenge($match1, $fartingAsses);

    $match2 = $tfts->challengeGroup($brawlhalla, $fartingAsses, $munschkin);
    $tfts->declineGroupChallenge($match2, $munschkin);

    $match3 = $tfts->challengeGroup($brawlhalla, $fartingAsses, $munschkin);
    $tfts->acceptGroupChallenge($match3, $munschkin);
    $tfts->cancelGroupMatch($match3, $fartingAsses);

    $match4 = $tfts->challengeGroup($brawlhalla, $fartingAsses, $munschkin);
    $tfts->acceptGroupChallenge($match4, $munschkin);
    $tfts->reportResultGroupMatch($match4, $fartingAsses, new ArrayCollection([$freezer, $tuborg]), 2, 1);
    $tfts->reportResultGroupMatch($match4, $munschkin, new ArrayCollection([$buddha, $xelsor]), 1, 2);
    $tfts->reportResultGroupMatch($match4, $fartingAsses, new ArrayCollection([$freezer, $tuborg]), 1, 2);
  }

  private function simulateMassGame(Tfts $tfts) {
    $flatout2 = $this->em->find(Game::class, 5);

    $round = 1;
    if ($round == 1) {
      $tfts->joinUserPool($flatout2, $this->getUserByName('Freezer'));
      $tfts->joinUserPool($flatout2, $this->getUserByName('TuBorg'));
      $tfts->joinUserPool($flatout2, $this->getUserByName('Buddha'));
      $tfts->joinUserPool($flatout2, $this->getUserByName('Xelsor'));
      $tfts->joinUserPool($flatout2, $this->getUserByName('Jackal'));
      $tfts->joinUserPool($flatout2, $this->getUserByName('Yogiman'));
      $tfts->joinUserPool($flatout2, $this->getUserByName('Nando'));
      $tfts->joinUserPool($flatout2, $this->getUserByName('DrAcul'));
      $tfts->createPools($flatout2, 4);
    }

    if ($round == 2) {
      foreach ($flatout2->getOpenPools() as $pool) {
        $rank = 1;
        foreach ($pool->getUsers() as $poolUser) {
          $tfts->setPoolRank($pool, $this->getUserByName($poolUser->getUser()->getUserName()), $rank++);
        }
      }
      $tfts->processPools($flatout2, 2, 1);
    }

    if ($round == 3) {
      foreach ($flatout2->getOpenPools() as $pool) {
        $rank = 1;
        foreach ($pool->getUsers() as $poolUser) {
          $tfts->setPoolRank($pool, $this->getUserByName($poolUser->getUser()->getUserName()), $rank++);
        }
      }
      $tfts->processPools($flatout2, 1, 1);
    }

    if ($round == 4) {
      foreach ($flatout2->getOpenPools() as $pool) {
        $rank = 1;
        foreach ($pool->getUsers() as $poolUser) {
          $tfts->setPoolRank($pool, $this->getUserByName($poolUser->getUser()->getUserName()), $rank++);
        }
      }
      $tfts->processFinalPool($flatout2);
    }
  }

  private function processTrackmaniaMap(Tfts $tfts) {
    $tfts->processMap($this->em->find('Tfts\Entity\Map', 1));
  }

  private function getUserByName(String $user_name) {
    $userList = new UserList();
    $userList->filterByUserName($user_name);
    return $userList->getResults()[0]->getUserObject();
  }

  private function getGroupByName(String $group_name) {
    return Group::getByName($group_name);
  }

  /**
   * We can use Action Methods to process AJAX Calls and Form submits within our block
   */
  public function action_addPoints() {
    // if the data is valid, we process it
    $errors = $this->validate($this->post(), 'addPoints');
    if ($errors === true) {
      $tfts = new \Tfts\Tfts();
      $user = new User(1);
      $lan_id = 17;
      $result = $tfts->addPointsQB($user, $lan_id, $this->post('pointsValue'));
      $tfts->addPoints(new User(1), 1, $this->post('pointsValue'));
      $this->em->persist($tfts);
      $this->em->flush();
      $this->set('showMsg', true);
    } else {
      $result = $errors;
      $this->set('errors', $errors);
    };

    // if it is a Ajax call we return a result as Json, if it is not it will proceed rendering the
    // block view
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
      /* special ajax here */
      die(json_encode($result));
    } else {
      $this->view();
    }
  }

  /**
   * Run when the blocks add template is rendered.
   *
   * @return  void
   */
  public function add() {
    $this->form();
  }

  /**
   * Run when the blocks edit template is rendered.
   *
   * @return void
   */
  public function edit() {
    $this->form();
  }

  /**
   * Called by the add and edit templates are rendered, as they often share logic.
   *
   * @return void
   */
  public function form() {
    /*
     * Set variables for your blocks view here...
     *
     * $this->set('data', $my_data)
     *
     * The in view.php the variable $data will be available with the
     * contents of the $my_data.
     */
  }

  /**
   * Run when the add or edit forms are submitted. This should return true if
   * validation is successful or a Concrete\Core\Error\Error() object if it fails.
   *
   * @param  $data
   * @return bool|Error
   */
  public function validate($data, $action = false) {
    $errors = new \Concrete\Core\Error\Error();

    // we want to use a token to validate each call in order to protect from xss and request forgery
    $token = \Core::make("token");
    if ($action && !$token->validate($action)) {
      $errors->add('Invalid Request, token must be valid.');
    }

    // validate the action addPonts
    if ($action == 'addPoints') {
      if (empty($data['pointsValue'])) {
        $errors->add('No Points Value set.');
      }
      if (!is_numeric($data['pointsValue'])) {
        $errors->add('Invalid Points Value');
      }
    }

    if ($errors->has()) {
      return $errors;
    }

    return true;
  }

  /**
   * Run when the block add or edit form is submitted. The variables
   * within the data array are mapped to columns found in the blocks table. Any
   * post-processing of the blocks data before storage should be completed here.
   *
   * @param  $data
   * @return
   */
  public function save($data) {
    /*
     * if (isset($data['name']) && '' === trim($data['name'])) {
     *     unset($data['name']);
     * }
     */

    parent::save($data);
  }

  /**
   * This happens automatically in Concrete5 when versioning blocks and pages.
   *
   * @param  int $newBlockId
   * @return void|BlockRecord
   */
  public function duplicate($newBlockId) {
    return parent::duplicate($newBlockId);
  }

  /**
   * Runs when a block is deleted. This may not happen very often since a
   * block is only completed deleted when all versions that reference
   * that block, including the original, have themselves been deleted.
   *
   * @return [type] [description]
   */
  public function delete() {
    parent::delete();
  }

  /**
   * Provides text for the page search indexing routine. This method should
   * return simple, unformatted plain text, not HTML.
   *
   * @return string
   */
  public function getSearchableContent() {
    return '';
  }

  /* @section advanced */

  /**
   * Runs when a block is being exported.
   *
   * @param  SimpleXMLElement $blockNode
   * @return void
   */
  public function export(SimpleXMLElement $blockNode) {
    parent::export($blockNode);
  }

  /**
   * Runs when a block is being imported.
   *
   * @param  Page          $page
   * @param  string          $areaHandle
   * @param  SimpleXMLElement $blockNode
   * @return void
   */
  public function import($page, $areaHandle, SimpleXMLElement $blockNode) {
    parent::import($page, $areaHandle, $blockNode);
  }

  /* @endsection advanced */
}
