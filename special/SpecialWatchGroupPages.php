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

	protected $user ;
	protected $request ;
	protected $output ;
	public function __construct( $page = 'WatchGroupPages' ) {
		parent::__construct( $page );
	}

	function execute( $par ) {


		$this->user = $this->getUser() ;
		$this->output = $this->getOutput() ;

		if ( $this->user->isAnon() ) {
			SpecialWatchGroups::userIsAnon() ;
			return ;
		}

		$this->setHeaders() ;
		$this->outputHeader() ;


		$args = func_get_args();
		$groupname = $args[0] ; ;
		$groupExists = $this->validateGroupName( $groupname ) ;
		if ( is_null( $groupExists ) ) {
			$this->output->addHTML( "No such group exists" );
			return ;
		}
		$this->output->setPageTitle( $groupname ) ;
		$watchPages = $this->extractWatchPages( $this->user , $groupname ) ;

		$this->displayPages( $watchPages ) ;

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
		$this->output->addHTML( $output ) ;

	}


	public static function extractWatchPages( $user , $groupname ) {
		$list = array() ;
		$dbr = wfGetDB( DB_SLAVE, 'watchgroups' );
		// given namespace zero ,just  for testing. Should work for every namespace
		$res = $dbr->select(
					'watchpages',
					 '*',
					array(
						'user'		=>	$user->getId() ,
						'groupname'	=>	$groupname ,
						'namespace'	=>	0
					),
				 __METHOD__ );
		foreach ( $res as $row ) {
			// Yet to check the validity
			$title = Title::newFromText( $row->title) ;
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
					'user' 		=>	$this->user->getId(),
					'groupname'	=>	$groupname,
				),
				__METHOD__
			);
		foreach ( $res as $group ) {
			return $group->groupname ;
		}

		return null ;

	}
	/**
	Other basic functions to be defined
		a)Check the validity of the title
		b)Get the time, when this article last edited.
		c)etc
	*/
}
