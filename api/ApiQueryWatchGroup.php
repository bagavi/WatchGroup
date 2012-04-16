<?php
/**
 * @author Vivek Kumar Bagaria <vivekee047@gmail.com>
 *
 * Yet To complete the Generator part
 */

/**
 * Returns the list of WatchGroups a user
 * @licence GNU GPL v3+ 
 * @ingroup API 
 */
class ApiQueryWatchGroup extends ApiQueryGeneratorBase {

	protected $owner, $params;
	public function __construct( $main, $action ) {
		parent::__construct( $main, $action );
	}

	public function execute() {
		$this->run();
	}

	public function executeGenerator( $resultPageSet ) {
		$this->run( $resutlPageSet ) ;
	}

	public function run( $resutlPageSet = null ) {
		$user = $this->getUser();

		if ( !$user->isLoggedIn() ) {
			$this->dieUsage( 'You must be logged-in to have a watchlist', 'notloggedin' );
		}

		$this->params = $this->extractRequestParams();

		// $this->owner = $this->getWatchlistUser($params);
		$this->owner = $user ;
		$this->getOutput()->addHTML( "HRLL" ) ;
		$watchgroups = $this->ExtractFilteredWatchGroups( $user ) ;
		$result = $this->getResult() ;
		foreach ( $watchgroups as $groups ) {
			$result->addValue( $this->getModuleName() , null , $groups );
		}
		$this->getResult()->setIndexedTagName_internal( $this->getModuleName(), '' );
		return ;

	}

	public function ExtractFilteredWatchGroups() {

		$list = array() ;
		$prop = array_flip( (array)$this->params['prop'] );
		$this->selectNamedDB( 'watchgroups', DB_SLAVE, 'watchgroups' );
		$this->addTables( 'watchgroups' ) ;
		$this->addWhereFld( 'user', $this->owner->getId() );
		$this->addFields( array( 'groupname', 'visible_group' , 'public_editable' ) );

		// Adding visible and editable params
		if ( isset( $prop['onlypublicview'] ) ) {
			$this->addWhereIf( 'visible_group', 1 );
		}
		elseif ( isset( $prop['skippublicview'] ) ) {
			$this->addWhereIf( 'visible_group', 0 );
		}


		if ( isset( $prop['onlypubliceditable'] ) ) {
			$this->addWhereIf( 'public_editable', 1 );
		}

		elseif ( isset( $prop['skippubliceditable'] ) ) {
			$this->addWhereIf( 'public_editable', 0 );
		}
		$res = $this->select( __METHOD__ );

		foreach ( $res as $row ) {
			$test['Group Name']	=	$row->groupname ;
			$test['Visible']	=	$row->visible_group ;
			$test['Editable']	=	$row->public_editable ;
			$list[] = $test ;
		}
		return $list;
	}


	public function getAllowedParams() {
		return array(
			'owner' => array(
				ApiBase::PARAM_TYPE => 'user'
			),
			'token' => array(
				ApiBase::PARAM_TYPE => 'string'
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
				ApiBase::PARAM_DFLT => '',
				ApiBase::PARAM_TYPE => array(
					'changed',
					'shared',
					'onlypublicview',
					'onlypubliceditable',
					'skippublicview',
					'skippubliceditable'
				)
			),
			'time' => array(
				ApiBase::PARAM_DFLT => 1,
			),

		);
	}

	public function getParamDescription() {
		return array(
			'owner'	=> 'User of the WatchGroup',
			'token'	=> 'Security code to access other uses"s WatchGroups' ,
			'limit'	=> 'Number of watchgroups to be displayed',
			'prop'	=> array(
				'Additonal filtering of the watchgroups',
				'Return WatchGroups in which there are changed pages in time "T" from now',
				'Return watchGroups which can be viewed by any user',
				'Return watchGroups which can be edited by any user'
			),
			'time'	=> 'time T , applicable only when prop=>changed is given'
		);
	}

	public function getDescription() {
		return 'Get WatchGroups of the user';
	}

	public function getExamples() {
		return array(
			'api.php?action=query&list=watchgroups',
			'api.php?action=query&list=watchgroups&prop=skippublicview'
		);
	}
	public function getVersion() {
		return __CLASS__ ;
	}
}