<?php
/**
 * @licence GNU GPL v3+
 * @author Vivek Kumar Bagaria <vivekee047@gmail.com>
 * 
 * Yet To Code.
 * 
 * Function :
 * Returns the pages deleted from users WatchGroups.
 */


class ApiDeletedPagesWatchGroup extends ApiBase {

	public function __construct( $main, $action ) {
		parent::__construct( $main, $action );
	}

	public function execute() {
		$user = $this->getUser();
		if ( !$user->isLoggedIn() ) {
			$this->dieUsage( 'You must be logged-in to have a watchlist', 'notloggedin' );
		}

		$params = $this->extractRequestParams();

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
		return array();
	}

	public function getParamDescription() {
		return array();
	}

	public function getDescription() {
		return '';
	}

	public function getPossibleErrors() {
		return array() ;
	}

	public function getExamples() {
		return array(
		);
	}

	public function getHelpUrls() {
		return '';
	}

	public function getVersion() {
		return __CLASS__ ;
	}
}