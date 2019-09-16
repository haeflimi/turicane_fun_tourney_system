<?php
namespace Concrete\Package\TuricaneFunTourneySystem\Block\TftsRankingList;

use SimpleXMLElement;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Package\Package;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\UserList;
use Concrete\Core\User\User;
use Tfts\Entity\Lan;
use Concrete\Core\Support\Facade\Config;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Block Controller Class.
 *
 * Start building standards complient concrete5 blocks from me.
 *
 * @author   Oliver Green <oliver@c5labs.com>
 * @license  See attached license file
 */
class Controller extends BlockController
{
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
    protected $btTable = 'btRankingList';

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
    public function __construct($obj = null)
    {
        parent::__construct($obj);

        // Get the Connection to the right MySQL Database. configured in /application/config/database.php
        $this->db = \Database::connection('turicane_fun_tourney_system');
        // Create a Query Builder Instance using our new Connection
        $this->qb = $this->db->createQueryBuilder();
        // The EntityManager is used to work with Doctrine Entities
        $this->em = $this->db->getEntityManager();
    }

    /**
     * Runs when the blocks view template is rendered.
     * 
     * @return void
     */
    public function view()
    {


        // An example on how to use c5 Groups/ Users
        // https://documentation.concrete5.org/developers/users-groups/groups/overview
        // Complete c5 API Doc: https://documentation.concrete5.org/api/8.4.5/
        $g = Group::getByName('Turicane 17');
        $tn = $g->getGroupMembers();

        $ul = new UserList();
        $ul->filterByUserName('Freezer');
        $u_freezer = $ul->getResults()[0]->getUserObject();
        // when we know the id it's easier of course
        $u_tuborg = new User(1);
        $u_buddha = new User(2);

        // Use the entity Manager to get the Lan Entity
        $lan = $this->em->find('Tfts\Entity\Lan', Config::get('tfts.currentLanId'));

        // Instantiate our Custom TFTS Class
        $tfts = new \Tfts\Tfts();
        // Then use it to retrieve Data
        $bsp1 = $tfts->getUserRankingSQL($u_freezer, $event = 'Turicane 17');
        $bsp2 = $tfts->getUserRankingQueryBuilder($u_tuborg, $event = 'Turicane 17');
        $bsp3 = $tfts->getUserRanking($u_buddha, $lan);

        // everything we want to pass along to the Block View Layer must be set like this:
        $this->set('bsp1',$bsp1[0]);
        $this->set('bsp2',$bsp2[0]);
        $this->set('bsp3',$bsp3->getPoints());
        $this->set('bsp4_username', $bsp2[0]['user_name']);
        $this->set('bsp4_points', $bsp2[0]['points']);
        $this->set('bsp5', $u_freezer);
        // we also load and pass on the c5 Form Helper to generate form elements for us
        // https://documentation.concrete5.org/tutorials/how-to-use-the-form-widget-in-concrete5-5-7
        $this->set('fh', $this->app->make('helper/form'));
    }

    /**
     * We can use Action Methods to process AJAX Calls and Form submits within our block
     */
    public function action_addPoints(){
        // if the data is valid, we process it
        $errors = $this->validate($this->post(),'addPoints');
        if($errors === true){
            $tfts = new \Tfts\Tfts();
            $user = new User(1);
            $lan_id = 17;
            $result = $tfts->addPointsQB($user,$lan_id,$this->post('pointsValue'));
            $tfts->addPoints(new User(1),1,$this->post('pointsValue'));
            $this->set('showMsg', true);
        } else {
            $result = $errors;
            $this->set('errors', $errors);
        };

        // if it is a Ajax call we return a result as Json, if it is not it will proceed rendering the
        // block view
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
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
    public function add()
    {
        $this->form();
    }

    /**
     * Run when the blocks edit template is rendered.
     *
     * @return void
     */
    public function edit()
    {
        $this->form();
    }

    /**
     * Called by the add and edit templates are rendered, as they often share logic.
     *
     * @return void
     */
    public function form()
    {
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
    public function validate($data, $action = false)
    {
        $errors = new \Concrete\Core\Error\Error();

        // we want to use a token to validate each call in order to protect from xss and request forgery
        $token = \Core::make("token");
        if($action && !$token->validate($action)){
            $errors->add('Invalid Request, token must be valid.');
        }

        // validate the action addPonts
        if($action == 'addPoints'){
            if(empty($data['pointsValue'])){
                $errors->add('No Points Value set.');
            }
            if(!is_numeric($data['pointsValue'])){
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
    public function save($data)
    {
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
    public function duplicate($newBlockId)
    {
        return parent::duplicate($newBlockId);
    }

    /**
     * Runs when a block is deleted. This may not happen very often since a
     * block is only completed deleted when all versions that reference
     * that block, including the original, have themselves been deleted.
     *
     * @return [type] [description]
     */
    public function delete()
    {
        parent::delete();
    }

    /**
     * Provides text for the page search indexing routine. This method should
     * return simple, unformatted plain text, not HTML.
     *
     * @return string
     */
    public function getSearchableContent()
    {
        return '';
    }

    /* @section advanced */

    /**
     * Runs when a block is being exported.
     *
     * @param  SimpleXMLElement $blockNode
     * @return void
     */
    public function export(SimpleXMLElement $blockNode)
    {
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
    public function import($page, $areaHandle, SimpleXMLElement $blockNode)
    {
        parent::import($page, $areaHandle, $blockNode);
    }

    /* @endsection advanced */
}
