<?php
/**
 * @licence GNU GPL v3+ 
 *
 * @author vivekkumarbagaria <vivekee047@gmail.com>
 * Assumption - If the user has watched an title from any group, it is considered to be watched
 * 
 * This class is a object for creating watchedgroupitems for a title
 */

class WatchedGroupItem extends WatchedItem {

	var $groupname;
	/**
	 * Create a WatchedItem object with the given user and title
	 * @param $user User: the user to use for (un)watching
	 * @param $title Title: the title we're going to (un)watch
	 * @return WatchedItem object
	 */

	public static function fromUserTitleGroupname( $user, $title , $groupname ) {

		$wg = new WatchedGroupItem;
		$wg->mUser = $user;
		$wg->mTitle = $title;
		$wg->userId = $user->getId();
		$wg->ns = $title->getNamespace();
		$wg->titleKey = $title->getDBkey();
		$wg->groupname = $groupname ;
		return $wg;
	}


	/**
	 * Return an array of conditions to select or update the appropriate database
	 * row.
	 *
	 * @return array
	 */
	private function dbCond() {
		return array('user' 	=> $this->userId,
					'namespace' => $this->ns,
					'title' 	=> $this->titleKey,
					'groupname' => $this->groupname
				 );
	}


	/*
	 * Returns true if the given page is there in the given group
	 */
	public function checkWatchGroupPage(){
		
		$dbw = wfGetDB( DB_SLAVE );
		$res = $dbw->selectRow( 'watchpages',
			'*',
			array(
			'user' => $this->userId,
			'title' => $this->titleKey,
			'namespace' => MWNamespace::getSubject( $this->ns ),
			'groupname'	=> $this->groupname 

		), __METHOD__, 'IGNORE' );		
		if( isset($res->id)  ){
			return True ;
		}
		else{
			return False ;
		}
	}
	
	/**
	 * Given a title ,groupname and user (assumes the object is setup), add the watch to the
	 * database.
	 * @return bool (always true)
	 */
	
	public function addWatchGroupPage() {
		//Return if the given page exists in the given WatchGroup
		if($this->checkWatchGroupPage()){
			return ;
		}
		wfProfileIn( __METHOD__ );
		$dbw = wfGetDB( DB_MASTER );
		$dbw->insert( 'watchpages',
			array(
			'user' => $this->userId,
			'title' => $this->titleKey,
			'namespace' => MWNamespace::getSubject( $this->ns ),
			'groupname'	=> $this->groupname ,
			'notifytimestamp' => null

		), __METHOD__, 'IGNORE' );

		// Every single watched page needs now to be listed in watchlist;
		// namespace:page and namespace_talk:page need separate entries:
		$dbw->insert( 'watchpages',
			array(
			'user' 		=> $this->userId,
			'title' 		=> $this->titleKey,
			'namespace' 	=> MWNamespace::getTalk( $this->ns ),
			'groupname'	=> $this->groupname ,
			'notifytimestamp' => null
		), __METHOD__, 'IGNORE' );

		$this->watched = true;

		wfProfileOut( __METHOD__ );
		return true;
	}

	/**
	 * Same as addWatchGroup, only the opposite.
	 * @return bool
	 */
	public function removeWatchGroupPage() {
		wfProfileIn( __METHOD__ );
		$success = false;
		$dbw = wfGetDB( DB_MASTER );
		$dbw->delete( 'watchpages',
			array(
			'user' 		=> $this->userId,
			'title' 		=> $this->titleKey,
			'namespace' 	=> MWNamespace::getSubject( $this->ns ),
			'groupname' 	=> $this->groupname,
			), __METHOD__
		);
		if ( $dbw->affectedRows() ) {
			$success = true;
		}

		$dbw->delete( 'watchpages',
			array(
			'user' 		=> $this->userId,
			'title' 		=> $this->titleKey,
			'namespace' 	=> MWNamespace::getTalk( $this->ns ),
			'groupname' 	=> $this->groupname
			), __METHOD__
		);

		if ( $dbw->affectedRows() ) {
			$success = true;
		}
		$this->watched = false;
		wfProfileOut( __METHOD__ );
		return $success;
	}
}
