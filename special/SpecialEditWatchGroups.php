<?php
/**
 * @licence GNU GPL v3+
 * @author vivekkumarbagaria <vivekee047@gmail.com>
 *
 * Function : To Edit the users WatchGroups
 *
 */
class SpecialEditWatchGroups extends SpecialPage {

	public function __construct() {
		parent::__construct( 'EditWatchGroups' );
	}

	public function execute( $mode ) {
		if ( $this->getUser()->isAnon() ) {
			$this->userIsAnon() ;
			return ;
		}

		$this->setHeaders();
		$this->outputHeader() ;
		$list = SpecialWatchGroups::ExtractWatchGroup( $this->getUser() );
		$this->CreateEditForm( $list ) ;
		$this->addViewSubtitle();
	}


	// This function is borrowed from SpecialEditWatchList
	public function CreateEditForm( $list ) {
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


	public function submitRaw( $data ) {
		$wanted = explode( "\n" , trim( $data['Titles'] ) );
		$current = SpecialWatchGroups::ExtractWatchGroup( $this->getUser() ) ;
		if ( count( $wanted ) > 0 ) {
			$add = array_diff( $wanted, $current );
			$remove = array_diff( $current, $wanted );
			if ( count( $add ) > 0 ) {
				$this->addGroups( $add );
			}
			if ( count( $remove ) > 0 ) {
				$this->removeGroups( $remove );
			}
			$this->getUser()->invalidateCache();

		} else {
			$this->clearWatchGroups();
			$this->getUser()->invalidateCache();
		}

		$this->getOutput()->addHTML( "Groups have been added and removed as you wished" ) ;
	}


	public function addGroups( $list ) {
		foreach ( $list as $group ) {
			SpecialWatchGroups::addNewGroup( $this->getUser() , $group ) ;
		}
	}


	public function removeGroups( $list ) {
		foreach ( $list as $group ) {
			SpecialWatchGroups::removeGroup( $this->getUser(), $group ) ;
		}
	}


	private function clearWatchGroups() {
		$dbw = wfGetDB( DB_MASTER );
		$dbw->delete(
			'watchgroups',
			array( 'wp_user' => $this->getUser()->getId() ),
			__METHOD__
		);
	}

	
	public function addViewSubtitle() {
		$subtitle = Linker::linkKnown(
				SpecialPage::getTitleFor( "WatchGroups" ), "ViewWatchAllGroup"  	);
		$this->getOutput()->addSubtitle( $subtitle ) ;
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
	
}