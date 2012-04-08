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

	protected $output ;
	protected $user ;
	protected $request ;
	public function __construct() {
			parent::__construct( 'WatchGroups' );
	}


	public function addnewline() {
		$this->output->addHTML( '<br>' ) ;
	}

	public function execute( $mode ) {
		$this->user 	= $this->getUser() ;
		$this->request = $this->getRequest() ;
		$this->output = $this->getOutput() ;

		if ( $this->user->isAnon() ) {
			self::userIsAnon() ;
			return ;
		}
		
		$this->setHeaders();
		$this->outputHeader();
		$this->addEditSubtitle();
		/*
		 * Checking whether a new group is added
		 */
		$newGroup 	= $this->request->getText( 'newgroup', null ) ;
		if ( !is_null( $newGroup ) && $newGroup != '' ) {
			$visibility	= $this->request->getBool( 'visible' ) ;
			$editable	= $this->request->getBool( 'editable' ) ;
			self::addNewGroup( $this->user , $newGroup , $visibility , $editable ) ;
		}

		/*
		 * Display the users Group
		 */
		$list = self::ExtractWatchGroup( $this->user );
		if ( count( $list ) == 0 ) {
			$this->output->addWikiMsg( 'nowatchgroup' );
		}
		else {
			$this->output->addWikiMsg( 'watchgroup-head' );
		}
		$this->displayGroupNames( $list ) ;
		$this->addGroupForm();
		$this->addTable() ;
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
					'wtg_user' => $user->getId(),
					'wtg_groupname' => $groupname
				),
				__METHOD__
			);
		if(!isset($res->wtg_id)){
			return false;
		}
		else{
			return true ;
		}
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
					'wtg_user' => $user->getId(),
					'wtg_groupname' => $newGroup ,
					'wtg_visible_group' => $visibilty,
					'wtg_public_editable' => $editable,
				);
		$dbw->insert( 'watchgroups', $rows, __METHOD__, 'IGNORE' );
		if ( $dbw->affectedRows() ) {
			return true ;
		}
		else {
			return false ;
		}
	}

	
	public static function removeGroup( $user , $Group ) {
		$dbw = wfGetDB( DB_MASTER );
		$rows = array(
					'wtg_user'		=> $user->getId(),
					'wtg_groupname'	=> $Group ,
				);
		$dbw->delete( 'watchgroups', $rows, __METHOD__, 'IGNORE' );

		$rows = array(
					'wp_user'		=> $user->getId(),
					'wp_groupname'	=> $Group ,
				);
		$dbw->delete( 'watchpages', $rows, __METHOD__, 'IGNORE' );

		if ( $dbw->affectedRows() ) {
			return true ;
		}
		else {
			return false ;
		}

	}

	//To change the creation of the form, using HTML form
	public function addGroupForm() {
		$this->addnewline() ;
		$newline = '<br>' ;
		$this->output->addhtml( Html::rawElement( 'div',	array( 'class' => 'mw-watchgroup-addgroup-title' ), wfMsg( 'watchgroup-add-new' ) ) );
		$form	 = Xml::openElement( 'form', array( 'method'	=> 'post',
													'action'	=> $this->getTitle()->getLocalUrl(),
													'id'		=> 'mw-watchgroup-submit' ) ) ;
		$form	.= Xml::label( wfMsg( 'watchgroup-add-form-groupname' ), 'mw-watchgroup-form-groupname' ) ;
		$form	.= Xml::element( 'input' , array( 'name' => 'newgroup', ) ) . $newline ;
		$form	.= Xml::checkLabel( wfMsg( 'watchgroup-add-form-visible' ), "visible", "visiblecheckbutton", array() );
		$form	.= Xml::checkLabel( wfMsg( 'watchgroup-add-form-editable' ), "editable", "editablecheckbutton", array() ) . $newline;
		$form 	.= Xml::submitButton( wfMsg( 'watchgroup-add-form-add-group' ) ) ;
		$form 	.= Xml::closeElement( 'form' ) ;

		$this->output->addHTML( $form ) ;
	}

	
	public static function userIsAnon() {
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
		$this->output->addHTML( "<ul>" ) ;
		foreach ( $list as $groupname ) {
			$noPages = self::countPages( $this->user, $groupname ) ;
			$tools = "<li>" . Linker::linkKnown(
				SpecialPage::getTitleFor( 'WatchGroupPages', $groupname ),
			$groupname . '(' . $noPages . ')'
			) . '</li>';

			$this->output->addhtml( Html::rawElement( 'div',
						array( 'class' => 'mw-watchgroup-groupnames' ), $tools )
					);
		}
		$this->output->addHTML( "</ul>" ) ;
	}

	
	public static function ExtractWatchGroup( $user ) {

		$list = array();
		$dbr = wfGetDB( DB_SLAVE, 'watchgroups' );
		$res = $dbr->select(
				'watchgroups',
				'*',
				array(
					'wtg_user' => $user->getId(),
				),
				__METHOD__
			);
		foreach ( $res as $row ) {
			// Yet To check the validity of the groupname
			$list[] = $row->wtg_groupname ;
		}
		return $list;
	}

	
	public function addEditSubtitle() {
		global $wgLang ;
		$subtitle[] = Linker::linkKnown(
				SpecialPage::getTitleFor( "EditWatchGroups" ), "EditWatchGroups" );
		$subtitle[] = Linker::linkKnown(
				SpecialPage::getTitleFor( "WatchListToWatchGroup" ), "WatchListToWatchGroup" );
				
		$this->output->addSubtitle( $wgLang->pipeList($subtitle )) ;
	}

	
	public static function countPages( $user , $groupname ) {
		$dbr = wfGetDB( DB_SLAVE, 'watchpages' );

		# Fetch the raw count
		$res = $dbr->select( 'watchpages', 'COUNT(*) AS count',
			array(
				'wp_user' => $user->getId(),
				'wp_groupname' => $groupname,
			)
			, __METHOD__ );

		$row = $dbr->fetchObject( $res );
		$count = $row->count;
		return floor( $count / 2 );
	}


	public function addTable() {
	}
}
