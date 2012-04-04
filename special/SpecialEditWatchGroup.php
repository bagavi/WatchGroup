<?php
/**
 * 
 * @author vivekkumarbagaria <vivekee047@gmail.com>
 * 
 * Function : To Edit the groups 
 *
 */
class SpecialEditWatchGroup extends SpecialPage {

	protected $output ;
	protected $user ;
	protected $request ;
	
	public function __construct(){
		parent::__construct( 'EditWatchGroup' );
	}

	public function execute($mode){

		$this->user 	= $this->getUser() ;
		if( $this->user->isAnon() ) {
			SpecialWatchGroup::userIsAnon() ;
			return ;
		}
		
		$this->request = $this->getRequest() ;
		$this->output = $this->getOutput() ;
		$this->setHeaders();
		$this->outputHeader();
		$list = SpecialWatchGroup::ExtractWatchGroup($this->user);
		$this->CreateEditForm($list) ;
		$this->addViewSubtitle();
	}

	
	//This function is taken from SpecialEditWatchList
	public function CreateEditForm($list){
		$titles = implode( $list, "\n" );
		$fields = array(
			'Titles' => array(
				'type' => 'textarea',
				'label-message' => 'watchlistedit-raw-titles',
				'default' => $titles,
			),
		);
		$form = new HTMLForm( $fields, $this->getContext() );
		$form->setTitle( $this->getTitle() );
		$form->setSubmitCallback( array( $this, 'submitRaw' ) );
		$form->show();
	}
	
	
	public function submitRaw( $data ){
		$wanted = explode( "\n" ,trim($data['Titles']));
		$current = SpecialWatchGroup::ExtractWatchGroup($this->user) ;
		if(count($wanted) > 0) {
			$add = array_diff( $wanted, $current );
			$remove = array_diff( $current, $wanted );
			if(count($add) >0){
				$this->addGroups( $add );
			}
			if(count($remove) > 0){
				$this->removeGroups( $remove );	
			}
			$this->removeGroups( $remove );
			$this->user->invalidateCache();

		} else {
			$this->clearWatchGroups();
			$this->getUser()->invalidateCache();
		}
		
		$this->output->addHTML("Groups have been added and removed as you wished") ;
				
	}
	
	
	public function addGroups($list){
		foreach ($list as $group) {
			SpecialWatchGroup::addNewGroup( $this->user ,$group) ;
		}
	}
	

	public function removeGroups($list){
		foreach ($list as $group) {
			SpecialWatchGroup::removeGroup($this->user, $group) ;
		}
	}
	
	
	private function clearWatchGroups(){
		$dbw = wfGetDB( DB_MASTER );
		$dbw->delete(
			'watchgroups',
			array( 'wp_user' => $this->getUser()->getId() ),
			__METHOD__
		);
	}

	public function addViewSubtitle(){
		$subtitle = Linker::linkKnown(
				SpecialPage::getTitleFor( "WatchGroup" ),"ViewWatchGroup"  	);
		$this->output->addSubtitle($subtitle) ;
	}
}