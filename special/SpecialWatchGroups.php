<?php
/**
 * @licence GNU GPL v3+
 * @author VivekKumarBagaria <vivekee047@gmail.com>
 * Displays users WatchGroups.
*/

/** To Do
 * 1)Edit button next to the groupname(this will redirect the user to edit the Group Preferences)
 * 2)To add CSS and JS.
 */

class SpecialWatchGroups extends SpecialPage {

	public function __construct() {
			parent::__construct( 'WatchGroups' );
	}

	
	public function execute( $mode ) {

		if ( $this->getUser()->isAnon() ) {
			$this->userIsAnon() ;
			return ;
		}
		
		$this->setHeaders();
		$this->outputHeader();
		$this->addEditSubtitle();
		/*
		 * Checking whether a new group is added
		 */
		$newGroup 	= $this->getRequest()->getText( 'newgroup', null ) ;
		if ( !is_null( $newGroup ) && $newGroup != '' ) {
			$visibility	= $this->getRequest()->getBool( 'visible' ) ;
			$editable	= $this->getRequest()->getBool( 'editable' ) ;
			self::addNewGroup( $this->getUser() , $newGroup , $visibility , $editable ) ;
		}

		/*
		 * Display the users Group
		 */
		$list = self::ExtractWatchGroup( $this->getUser() );
		if ( count( $list ) == 0 ) {
			$this->getOutput()->addWikiMsg( 'nowatchgroup' );
		}
		else {
			$this->getOutput()->addWikiMsg( 'watchgroup-head' );
		}
		$this->displayGroupNames( $list ) ;
		$this->addGroupForm();
	}

	/*
	 * Returns true if the there is a watchgroup by the given groupname
	 */
	public static function checkGroupExists($user , $groupname){
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->selectRow(
				'watchgroups',
				'*',
				array(
					'user' => $user->getId(),
					'groupname' => $groupname
				),
				__METHOD__
			);
		return (bool)$res ;
	}
	
	/*
	 * Adds a WatchGroup, if the name is unique
	 */
	public static function addNewGroup( $user , $newGroup , $visibilty = 0 , $editable = 0 ) {
		
		if(self::checkGroupExists($user, $newGroup)){
			return false;
		}
		
		$dbw = wfGetDB( DB_MASTER );
		$rows = array(
					'user' => $user->getId(),
					'groupname' => $newGroup ,
					'visible_group' => $visibilty,
					'public_editable' => $editable,
				);
		return (bool)$dbw->insert( 'watchgroups', $rows, __METHOD__, 'IGNORE' );
	}

	
	public static function removeGroup( $user , $Group ) {
		$dbw = wfGetDB( DB_MASTER );
		$rows = array(
					'user'		=> $user->getId(),
					'groupname'	=> $Group ,
				);
		return (bool)$dbw->delete( 'watchgroups', $rows, __METHOD__, 'IGNORE' );
	}

	//To use HTMLForm instead of Xml.
	public function addGroupForm() {
		$this->addnewline() ;
		$newline = '<br>' ;
		$this->getOutput()->addhtml( Html::rawElement( 'div',	array( 'class' => 'mw-watchgroup-addgroup-title' ), wfMsg( 'watchgroup-add-new' ) ) );
		$form	 = Xml::openElement( 'form', array( 'method'	=> 'post',
													'action'	=> $this->getTitle()->getLocalUrl(),
													'id'		=> 'mw-watchgroup-submit' ) ) ;
		$form	.= Xml::label( wfMsg( 'watchgroup-add-form-groupname' ), 'mw-watchgroup-form-groupname' ) ;
		$form	.= Xml::element( 'input' , array( 'name' => 'newgroup', ) ) . $newline ;
		$form	.= Xml::checkLabel( wfMsg( 'watchgroup-add-form-visible' ), "visible", "visiblecheckbutton", array() );
		$form	.= Xml::checkLabel( wfMsg( 'watchgroup-add-form-editable' ), "editable", "editablecheckbutton", array() ) . $newline;
		$form 	.= Xml::submitButton( wfMsg( 'watchgroup-add-form-add-group' ) ) ;
		$form 	.= Xml::closeElement( 'form' ) ;

		$this->getOutput()->addHTML( $form ) ;
	}

	
	public function userIsAnon() {
		$this->getOutput()->setPageTitle( $this->msg( 'watchgroup-nologin' ) );
			$llink = Linker::linkKnown(
				SpecialPage::getTitleFor( 'Userlogin' ),
				$this->msg( 'loginreqlink' )->escaped(),
				array(),
				array( 'returnto' => $this->getTitle()->getPrefixedText() )
			);
			$this->getOutput()->addHTML( $this->msg( 'watchgroup-listanontext' )->rawParams( $llink )->parse() );
			return;
	}


	public function displayGroupNames( $list ) {
		$this->getOutput()->addHTML( "<ul>" ) ;
		foreach ( $list as $groupname ) {
			$noPages = self::countPages( $this->getUser(), $groupname ) ;
			$tools = "<li>" . Linker::linkKnown(
				SpecialPage::getTitleFor( 'WatchGroupPages', $groupname ),
			$groupname . '(' . $noPages . ')'
			) . '</li>';

			$this->getOutput()->addhtml( Html::rawElement( 'div',
						array( 'class' => 'mw-watchgroup-groupnames' ), $tools )
					);
		}
		$this->getOutput()->addHTML( "</ul>" ) ;
	}

	
	public static function ExtractWatchGroup( $user ) {

		$list = array();
		$dbr = wfGetDB( DB_SLAVE, 'watchgroups' );
		$res = $dbr->select(
				'watchgroups',
				'*',
				array(
					'user' => $user->getId(),
				),
				__METHOD__
			);
		foreach ( $res as $row ) {
			// Yet To check the validity of the groupname
			$list[] = $row->groupname ;
		}
		return $list;
	}

	
	public function addEditSubtitle() {
		global $wgLang ;
		$subtitle[] = Linker::linkKnown(
				SpecialPage::getTitleFor( "EditWatchGroups" ), "EditWatchGroups" );
		$subtitle[] = Linker::linkKnown(
				SpecialPage::getTitleFor( "WatchListToWatchGroup" ), "WatchListToWatchGroup" );
				
		$this->getOutput()->addSubtitle( $wgLang->pipeList($subtitle )) ;
	}

	
	public static function countPages( $user , $groupname ) {
		$dbr = wfGetDB( DB_SLAVE, 'watchpages' );

		# Fetch the raw count
		$res = $dbr->select( 'watchpages', 'COUNT(*) AS count',
			array(
				'user' => $user->getId(),
				'groupname' => $groupname,
			)
			, __METHOD__ );

		$row = $dbr->fetchObject( $res );
		$count = $row->count;
		//Each title has its mainpage and talk page in the WatchGroup, so dividing by two
		return floor( $count / 2 );
	}

	
	public function addnewline() {
		$this->getOutput()->addHTML( '<br>' ) ;
	}
}