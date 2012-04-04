<?php
/**
 *	@author Vivek Kumar Bagaria <vivekee047@gmail.com>
 * 
 */

/** 
 *	Returns the list of WatchGroups a user
 * @ingroup API
 */
class ApiQueryWatchGroup extends ApiQueryGeneratorBase {

	public function __construct( $main, $action ) {
		parent::__construct( $main, $action );
	}
	
	public function execute(){
		$this->run(null);	
	}
	
	public function executeGenerator($resultPageSet){
		$this->run($resutlPageSet) ;
	}
	
	public function run($resutlPageSet) {
		$user = $this->getUser();
		if ( !$user->isLoggedIn() ) {
			$this->dieUsage( 'You must be logged-in to have a watchlist', 'notloggedin' );
		}

		$params = $this->extractRequestParams();
		
		$user = $this->getWatchlistUser($params);
		
		$watchGroups = $this->ExtractWatchGroups($user) ;
		
		$filteredWatchGroups = $this->FilterWatchGroups($watchGroups, $params) ;
		
		$result = $this->getResult() ;
		
		/*
		 *Add the $filteredWatchGroups in $result till $params['limit']
		 *give a continue value
		 */
		

	}

	public function ExtractWatchGroups($user){
		
		$list = array() ;
		//Query the database for the users watch groups
		//Add valid watchgroups in the list
		
	}
	
	public function FilterWatchGroups($list , $params){
		$newlist = array();
		return $newlist;
	}
	public function mustBePosted() {
		return true;
	}

	public function isWriteMode() {
		return true;
	}

	public function needsToken() {
		return true;
	}

	public function getTokenSalt() {
		
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
					'publicview',
					'public editable',
				)
			),
			'time' => array(
				ApiBase::PARAM_DFLT => 1,
			),
			
		);
	}
	
	public function getParamDescription() {
		return array(
			'owner' => 'User of the WatchGroup',
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

	public function getVersion() {
		return __CLASS__ ;
	}
}