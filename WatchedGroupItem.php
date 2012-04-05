<?php
/**
 * Assumption - If the user has watched an title from any group, it is considered to be watched
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
		$wg->id = $user->getId();
		$wg->ns = $title->getNamespace();
		$wg->ti = $title->getDBkey();
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
		return array('wp_user' 		=> $this->id,
					 'wp_namespace' => $this->ns,
					 'wp_title' 	=> $this->ti,
					 'wp_groupname' => $this->groupname
				 );
	}


	/**
	 * Given a title ,groupname and user (assumes the object is setup), add the watch to the
	 * database.
	 * @return bool (always true)
	 */
	public function addWatchGroupPage() {
		wfProfileIn( __METHOD__ );

		$dbw = wfGetDB( DB_MASTER );
		$dbw->insert( 'watchpages',
		  array(
			'wp_user' => $this->id,
			'wp_title' => $this->ti,
			'wp_namespace' => MWNamespace::getSubject( $this->ns ),
			'wp_groupname'	=> $this->groupname ,
			'wp_notifytimestamp' => null

		  ), __METHOD__, 'IGNORE' );

		// Every single watched page needs now to be listed in watchlist;
		// namespace:page and namespace_talk:page need separate entries:
		$dbw->insert( 'watchpages',
		  array(
			'wp_user' 		=> $this->id,
			'wp_title' 		=> $this->ti,
			'wp_namespace' 	=> MWNamespace::getTalk( $this->ns ),
			'wp_groupname'	=> $this->groupname ,
			'wp_notifytimestamp' => null
		  ), __METHOD__, 'IGNORE' );

		$this->watched = true;

		wfProfileOut( __METHOD__ );
		return true;
	}

	/**
	 * Same as addWatch, only the opposite.
	 * @return bool
	 */
	public function removeWatchGroupPage() {
		wfProfileIn( __METHOD__ );
		$success = false;
		$dbw = wfGetDB( DB_MASTER );
		$dbw->delete( 'watchpages',
			array(
				'wp_user' 		=> $this->id,
				'wp_title' 		=> $this->ti,
				'wp_namespace' 	=> MWNamespace::getSubject( $this->ns ),
				'wp_groupname' 	=> $this->groupname,
			), __METHOD__
		);
		if ( $dbw->affectedRows() ) {
			$success = true;
		}

		$dbw->delete( 'watchpages',
			array(
				'wp_user' 		=> $this->id,
				'wp_title' 		=> $this->ti,
				'wp_namespace' 	=> MWNamespace::getTalk( $this->ns ),
				'wp_groupname' 	=> $this->groupname
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