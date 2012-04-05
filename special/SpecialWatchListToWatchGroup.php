<?php

class SpecialWatchListToWatchGroup extends SpecialPage {
	
	protected $output ;
	protected $user ;
	protected $request ;
	//Name of the Group which will contain all the watchlist pages
	protected $WatchListGroupName = "WatchListGroup" ;
	public function __construct() {
			parent::__construct( 'WatchListToWatchGroup' );
	}
	public function execute( $mode ) {
		$this->user 	= $this->getUser() ;
		$this->request = $this->getRequest() ;
		$this->output = $this->getOutput() ;
			
		$watchlist = $this->getWatchlist() ;
		if(SpecialWatchGroups::addNewGroup($this->user, "WatchListGroup")){
			foreach ($watchlist as $titleText) {
				$title = Title::newFromText($titleText) ;
				$watchgroupitem = WatchedGroupItem::fromUserTitleGroupname( $this->user, $title, $this->WatchListGroupName ) ;
				$watchgroupitem->addWatchGroupPage() ;
			}
			$this->output->addHTML(wfMsg('watchgroup-watchlist-is-shifted')) ;
		}
		else{
			$this->output->addHTML(wfMsg('watchgroup-watchlist-was-shifted')) ;
		}
	}
	
	
	//Borrowed from SpecialEditWatchList
	private function getWatchlist() {
		$list = array();
		$dbr = wfGetDB( DB_MASTER );
		$res = $dbr->select(
			'watchlist',
			'*',
			array(
				'wl_user' => $this->user->getId(),
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
			cleanupWatchTitle($title, $namespace, $dbKey) ;
		}
		return (bool)$title;
	}
	
	
	//Borrowed from SpecialEditWatchList	
	private function cleanupWatchTitle($title, $namespace, $dbKey) {
		$dbw = wfGetDB( DB_MASTER );
		
		wfDebug( "User {$this->user} has broken watchlist item ns($namespace):$dbKey, "
			. ( $title ? 'cleaning up' : 'deleting' ) . ".\n"
		);

		$dbw->delete( 'watchlist',
			array(
				'wl_user' => $this->user->getId(),
				'wl_namespace' => $namespace,
				'wl_title' => $dbKey,
			),
			__METHOD__
		);
	}
}

