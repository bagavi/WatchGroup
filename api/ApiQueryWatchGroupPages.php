<?php
/**
 * @author Vivek Kumar Bagaria <vivekee047@gmail.com>
 * Yet to implement token feature
 */

/**
 * Returns the list of pages in a WatchGroup
 * @licence GNU GPL v3+ 
 * @ingroup API
 */
class ApiQueryWatchGroupPages extends ApiQueryGeneratorBase {

	protected $owner, $params , $output , $result ,$prop;
	
	public function __construct( $main, $action ) {
		parent::__construct( $main, $action ,'');
	}

	public function execute() {
		$this->run();
	}
	
	public function executeGenerator( $resultPageSet ) {
		$this->run( $resutlPageSet );
	}

	public function run( $resutlPageSet = null ) {
		$this->user = $this->getUser();

		if ( !$this->user->isLoggedIn() ) {
			$this->dieUsage( 'login please', 'notloggedin' );
		}

		$this->params	= $this->extractRequestParams();
		$this->owner	= $this->getUser();
		$this->output	= $this->getOutput();
		if ( isset( $show['changed'] ) && isset( $show['!changed'] ) ) {
			$this->dieUsageMsg( 'show' );
		}
		$this->setDatabaseParams();
		$this->result = $this->getResult();
		$this->addData($resutlPageSet);
		return;
	}

	public function setDatabaseParams(){
		
		$this->prop = array_flip( (array)$this->params['prop'] );
		$this->show = array_flip( (array)$this->params['show'] );

		$this->selectNamedDB( 'watchpages', DB_SLAVE, 'watchpages' );
		$this->addTables( 'watchpages' );
		$this->addFields( array( 'wp_namespace', 'wp_title' ) );
		$this->addFieldsIf( 'wp_notifytimestamp', isset( $this->prop['changed'] ) );
		$this->addWhereFld( 'wp_user', $this->user->getId() );
		$this->addWhereFld( 'wp_namespace', $this->params['namespace'] );
		$this->addWhereFld(	'wp_groupname', $this->params['groupname']);
		$this->addWhereIf( 'wp_notifytimestamp IS NOT NULL', isset( $this->show['changed'] ) );
		$this->addWhereIf( 'wp_notifytimestamp IS NULL', isset( $this->show['!changed'] ) );
		$this->addOption( 'LIMIT', $this->params['limit'] + 1 );
		
		//Order by namespace iff more than one namespace is provided 
		if ( count( $this->params['namespace'] ) == 1 ) {
			$this->addOption( 'ORDER BY', 'wp_title' );
		} 
		else {
			$this->addOption( 'ORDER BY', 'wp_namespace, wp_title' );
		}

		if ( isset( $this->params['continue'] ) ) {

			$cont = explode( '|', $this->params['continue'] );
			if ( count( $cont ) != 2 ) {
				$this->dieUsage( "Invalid continue param. You should pass the " .
					"original value returned by the previous query", "_badcontinue" );
			}
			$namespace = intval( $cont[0] );
			$title = $this->getDB()->strencode( $this->titleToKey( $cont[1] ) );
			$this->addWhere(
				"wp_namespace > '$namespace' OR " .
				"(wp_namespace = '$namespace' AND wp_title >= '$title')"
			);
		}
	}

	public function addData($resultPageSet=null){
		$res = $this->select( __METHOD__ );
		$count = 0;
		foreach ( $res as $row ) {
			if ( ++$count > $this->params['limit'] ) {
				$this->setContinueEnumParameter( 'continue',
												 $row->wp_namespace . '|' .
												$this->keyToTitle( $row->wp_title )
											 );
				break;
			}

			$title = Title::makeTitle( $row->wp_namespace, $row->wp_title );

			if ( is_null( $resultPageSet ) ) {
				$vals = array();
				ApiQueryBase::addTitleInfo( $vals, $title );
				if ( isset( $this->prop['changed'] ) && !is_null( $row->wp_notificationtimestamp ) )
				{
					$vals['changed'] = wfTimestamp( TS_ISO_8601, $row->wp_notificationtimestamp );
				}
				$fit = $this->result->addValue( $this->getModuleName(), null, $vals );
			} 
			else {
				$titles[] = $t;
			}
		}
		$this->getResult()->setIndexedTagName_internal( $this->getModuleName(), '' );


	}
	public function getAllowedParams() {
		return array(
			'continue' => null,
			'namespace' => array(
				ApiBase::PARAM_ISMULTI => true,
				ApiBase::PARAM_TYPE => 'namespace',
				ApiBase::PARAM_DFLT => 1
			),
			'limit' => array(
				ApiBase::PARAM_DFLT => 10,
				ApiBase::PARAM_TYPE => 'limit',
				ApiBase::PARAM_MIN => 1,
				ApiBase::PARAM_MAX => ApiBase::LIMIT_BIG1,
				ApiBase::PARAM_MAX2 => ApiBase::LIMIT_BIG2
			),
			'prop' => array(
				ApiBase::PARAM_ISMULTI => true,
				ApiBase::PARAM_TYPE => array(
					'changed',
				)
			),
			'show' => array(
				ApiBase::PARAM_ISMULTI => true,
				ApiBase::PARAM_TYPE => array(
					'changed',
					'!changed',
				)
			),
			'owner' => array(
				ApiBase::PARAM_TYPE => 'user'
			),
			'groupname' =>array(
				ApiBase::PARAM_ISMULTI => true ,
				ApiBase::PARAM_REQUIRED,
				ApiBase::PARAM_TYPE => 'string'
			),
			'token' => array(
				ApiBase::PARAM_TYPE => 'string'
			)
		);
	}

	public function getParamDescription() {
		return array(
			'continue' => 'When more results are available, use this to continue',
			'namespace' => 'Only list pages in the given namespace(s)',
			'limit' => 'How many total results to return per request',
			'prop' => array(
				'Which additional properties to get (non-generator mode only)',
				' changed - Adds timestamp of when the user was last notified about the edit',
			),
			'show' => 'Only list items that meet these criteria',
			'groupname' => 'The name of the WatchGroup(s)',
			'owner' => 'The name of the user whose watchlist you\'d like to access',
			'token' => 'Give a security token (settable in preferences) to allow access to another user\'s watchlist',
		);
	}
	

	public function getDescription() {
		return 'Get Pages of a WatchGroup';
	}

	public function getExamples() {
		return array(
			'api.php?action=query&list=watchgrouppages&groupname=WatchListGroup'
		);
	}
	public function getVersion() {
		return __CLASS__;
	}
}