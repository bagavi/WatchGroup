<?php
/**
 * @licence GNU GPL v3+
 * @author vivekkumarbagaria <vivekee047@gmail.com>
 *
 * Function:
 * This SpecialPage will display all the pages of a <groupname>
 *
 * To Do
 * Have relevant hooks between the code
 */

class SpecialWatchgroupPages extends UnlistedSpecialPage {
	
	public function __construct( $page = 'WatchGroupPages' ) {
		parent::__construct( $page );
	}

	function execute( $par ) {

		if ( $this->getUser()->isAnon() ) {
			$this->userIsAnon() ;
			return ;
		}

		$this->setHeaders() ;
		$this->outputHeader();
		$args = explode('/', $par);	
		$groupname = $args[0];
		$groupExists = $this->validateGroupName( $groupname ) ;
		if ( !$groupExists ) {
			$this->getOutput()->addHTML( "No such group exists" );
			return ;
		}
		$this->getOutput()->setPageTitle( $groupname ) ;
		$watchPages = $this->extractWatchPages( $this->getUser() , $groupname ) ;

		$this->displayPages( $watchPages ) ;
		$this->addViewSubtitle() ;
		// To add feed links to the ATOM. This will give the list of pages of the particular group.
		//To add conditions on the type of page. For eg, bot-edited, minor edit.
	}

	public function displayPages( $watchPages ) {
		$output = "<ul>\n";
		foreach ( $watchPages as $page ) {
			$title = Title::newFromText( $page ) ;
			$output .= "<li>"
					. Linker::link( $title )
					. ' (' . Linker::link( $title->getTalkPage() )
					. ")</li>\n";
		}
		$output .= "</ul>\n";
		$this->getOutput()->addHTML( $output ) ;

	}


	public static function extractWatchPages( $user , $groupname ) {
		$list = array() ;
		$dbr = wfGetDB( DB_SLAVE, 'watchgroups' );
		$res = $dbr->select(
					'watchpages',
					 '*',
					array(
						'user'		=>	$user->getId() ,
						'groupname'	=>	$groupname ,
						'namespace'	=>	0,
					),
				 __METHOD__ );
		foreach ( $res as $row ) {
			// Yet to check the validity
			$title = Title::makeTitleSafe($row->namespace, $row->title) ;
			$list[] = $title->getPrefixedText() ;
		}
		return $list ;
	}

	public function validateGroupName( $groupname ) {
		$dbr = wfGetDB( DB_SLAVE, 'watchgroups' );
		$res = $dbr->select(
				'watchgroups',
				'*',
				array(
					'user' 		=>	$this->getUser()->getId(),
					'groupname'	=>	$groupname,
				),
				__METHOD__
			); 
		return (bool)$res;
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
	

	public function addViewSubtitle() {
		$subtitle = Linker::linkKnown(
				SpecialPage::getTitleFor( "WatchGroups" ), "ViewWatchGroup"  	);
		$this->getOutput()->addSubtitle( $subtitle ) ;
	}
	
	/**
	Other basic functions to be defined
		a)Check the validity of the title
		b)Get the time, when this article last edited.
		c)etc
	*/
}