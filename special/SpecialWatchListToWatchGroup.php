<?php
/**
 * @licence GNU GPL v3+ 
 *
 * @author vivekkumarbagaria <vivekee047@gmail.com>
 *
 * Adds all the pages from the users current WatchList to a WatchGroup
 */
class SpecialWatchListToWatchGroup extends UnlistedSpecialPage{

	protected $user ;
	//Name of the Group which will contain all the watchlist pages
	protected $WatchListGroupName = "WatchListGroup" ;
	public function __construct() {
			parent::__construct( 'WatchListToWatchGroup' );
	}

	
	public function execute( $mode ) {
		$watchlist = $this->getWatchlist() ;
		$WatchListGroupPagelink = Linker::linkKnown(
				SpecialPage::getTitleFor( 'WatchGroupPages', $this->WatchListGroupName ),
				$this->WatchListGroupName) ;
		SpecialWatchGroups::addNewGroup($this->getUser(), "WatchListGroup") ;
		$current = SpecialWatchGroupPages::extractWatchPages($this->getUser(), $this->WatchListGroupName) ;
		$add = array_diff( $watchlist, $current );
		$remove = array_diff( $current, $watchlist );
		SpecialEditWatchGroupPages::watchPages($this->getUser(), $this->WatchListGroupName, $add) ;
		SpecialEditWatchGroupPages::unwatchPages($this->getUser(), $this->WatchListGroupName, $remove) ;
		$this->getOutput()->addHTML(wfMsg('watchgroup-updatefrom-watchlist')) ;
		$this->addViewSubtitle() ;
	}
	
	
	//Borrowed from SpecialEditWatchList
	private function getWatchlist() {
		$list = array();
		$dbr = wfGetDB( DB_MASTER );
		$res = $dbr->select(
			'watchlist',
			'*',
			array(
				'wl_user' => $this->getUser()->getId(),
			),
			__METHOD__
		);
		if( $res->numRows() > 0 ) {
			foreach ( $res as $row ) {
				$title = Title::makeTitleSafe( $row->wl_namespace, $row->wl_title );
				if ( $this->checkTitle( $title, $row->wl_namespace, $row->wl_title )
					&& !$title->isTalkPage()
				) {	
					$list[] = $title->getPrefixedText();
				}
			}
			$res->free();
		}
		return $list;
	}


	
	public function addViewSubtitle() {
		global $wgLang ;
		$subtitle[] = Linker::linkKnown(
				SpecialPage::getTitleFor( "WatchGroups" ), "ViewAllWatchGroup"  	);
		$subtitle[] = Linker::linkKnown(
				SpecialPage::getTitleFor( "WatchGroupPages" ,$this->WatchListGroupName), "WatchList"  	);
		$this->getOutput()->addSubtitle( $wgLang->pipeList($subtitle )) ;
		
	}
	
	
	//Borrowed from SpecialEditWatchList
	private function checkTitle( $title, $namespace, $dbKey ) {
		if ( $title
			&& ( $title->isExternal()
				|| $title->getNamespace() < 0
			)
		) {
			$title = false; // unrecoverable
		}
		if ( !$title
			|| $title->getNamespace() != $namespace
			|| $title->getDBkey() != $dbKey
		) {
			$this->cleanupWatchTitle($title, $namespace, $dbKey) ;
		}
		return (bool)$title;
	}


	//Borrowed from SpecialEditWatchList
	private function cleanupWatchTitle($title, $namespace, $dbKey) {
		$dbw = wfGetDB( DB_MASTER );
		
		wfDebug( "User {$this->getUser()} has broken watchlist item ns($namespace):$dbKey, "
			. ( $title ? 'cleaning up' : 'deleting' ) . ".\n"
		);

		$dbw->delete( 'watchlist',
			array(
				'wl_user' => $this->getUser()->getId(),
				'wl_namespace' => $namespace,
				'wl_title' => $dbKey,
			),
			__METHOD__
		);
	}
}