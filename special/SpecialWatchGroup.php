<?php
/**
 * @author VivekKumarBagaria <vivekee047@gmail.com>
*/ 

/** To Do
 * 1)Edit button next to the groupname(this will redirect the user to edit the Group Preferences)
 * 2)To display  more details of the groups like no of pages etc
 * 3)To add CSS and JS.
 * 4)Eliminate the duplication of groupnames
 */

class SpecialWatchGroup extends SpecialPage {

	protected $output ;
	protected $user ;
	protected $request ;
	public function __construct(){
			parent::__construct('WatchGroup' );
	}
	

	public function addnewline(){
		$this->output->addHTML('<br>') ;
	}	
	
	public function execute($mode){
		/**
		 * 	Check if user is anonymous?
		 *  If User is Anon return with a msg displaying to login
		 */
		$this->user 	= $this->getUser() ;
		$this->request = $this->getRequest() ;
		$this->output = $this->getOutput() ;
		if( $this->user->isAnon() ) {
			self::userIsAnon() ;
			return ;
		}
		/*
		 * Adding headers 
		 */
		$this->setHeaders();
		$this->outputHeader();
		$this->addEditSubtitle();
		/*
		 * Checking whether a new group is added
		 */
		$newGroup 	= $this->request->getText( 'newgroup', null ) ;	
		if(!is_null($newGroup) && $newGroup!=''){
			$visibility	= $this->request->getBool('visible') ; 
			$editable	= $this->request->getBool('editable') ;
			self::addNewGroup($this->user  ,$newGroup ,$visibility , $editable ) ;
		}
		
		/*
		 * Display the users Group
		 */
		$list = self::ExtractWatchGroup($this->user);
		if(count($list) == 0){
			$this->output->addWikiMsg( 'nowatchgroup' );
		}
		else{
			$this->output->addWikiMsg( 'watchgroup-head' );
		}
		$this->displayGroupNames($list) ;
		$this->addGroupForm();		

	}
	
public static function addNewGroup($user ,$newGroup , $visibilty = 0 , $editable = 0 ){
		$dbw = wfGetDB( DB_MASTER );
		$rows = array(
					'wg_user' => $user->getId(),
					'wg_groupname' => $newGroup , 
					'wg_visible_group' => $visibilty,
					'wg_public_editable' => $editable,
				);				
		$dbw->insert( 'watchgroups',$rows,__METHOD__, 'IGNORE');
		
		if($dbw->affectedRows()){
			return true ;
		}
		else{
			return false ;
		}
		
	}
	
	public static function removeGroup($user ,$Group ){
		$dbw = wfGetDB( DB_MASTER );
		$rows = array(
					'wg_user' => $user->getId(),
					'wg_groupname' => $Group , 
				);				
		$dbw->delete( 'watchgroups',$rows,__METHOD__, 'IGNORE');
		
		if($dbw->affectedRows()){
			return true ;
		}
		else{
			return false ;
		}
		
	}
	
	public function addGroupForm(){
		$this->addnewline() ;
		$newline = '<br>' ;
		
		$this->output->addhtml(Html::rawElement( 'div',
						array( 'class' => 'mw-watchgroup-addgroup-title' ), wfMsg('watchgroup-add-new') )
					);

		$form	 = Xml::openElement( 'form', array( 'method'	=> 'post',
													'action'	=> $this->getTitle()->getLocalUrl(),
													'id'		=> 'mw-watchgroup-submit' )) ;
		$form	.= Xml::label( wfMsg('watchgroup-add-form-groupname'), 'mw-watchgroup-form-groupname' ) ;
		$form	.= Xml::element('input' , array('name' => 'newgroup',)).$newline ;
		$form	.= Xml::checkLabel(wfMsg('watchgroup-add-form-visible'), "visible", "visiblecheckbutton", array());
		$form	.= Xml::checkLabel(wfMsg('watchgroup-add-form-editable'), "editable", "editablecheckbutton", array()).$newline;
		$form 	.= Xml::submitButton( wfMsg('watchgroup-add-form-add-group') ) ;
		$form 	.= Xml::closeElement( 'form' ) ;
		
		$this->output->addHTML( $form ) ;
	}
	
	public static function userIsAnon(){
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
	
	
	public function displayGroupNames($list){
		$this->output->addHTML("<ul>") ;
		foreach ($list as $groupnames){
			$tools = "<li>".Linker::linkKnown(
				SpecialPage::getTitleFor( 'WatchParticularGroup', $groupnames ),
			$groupnames
			).'</li>';

			$this->output->addhtml(Html::rawElement( 'div',
						array( 'class' => 'mw-watchgroup-groupnames' ), $tools )
					);
		}
		$this->output->addHTML("</ul>") ;
	}
	public static function ExtractWatchGroup($user){
			
		$list = array();
		$dbr = wfGetDB( DB_SLAVE, 'watchgroups' );		
		$res = $dbr->select(
				'watchgroups',
				'*',
				array(
					'wg_user' => $user->getId(),
				),
				__METHOD__
			);
		foreach ($res as $row){
			// Yet To check the validity of the groupname
			$list[] = $row->wg_groupname ;
		}
		return $list;
	}
	
	public function addEditSubtitle(){
		$subtitle = Linker::linkKnown(
				SpecialPage::getTitleFor( "EditWatchGroup" ),"EditWatchGroup"  	);
		$this->output->addSubtitle($subtitle) ;
	}
}