<?php
/**
 * @author vivekkumarbagaria  <vivekee047@gmail.com>
 *
 * Function:
 * This SpecialPage will display all the pages of a	<groupname>
 *
 * To Do
 * Have relevant hooks between the code
 */

class SpecialWatchParticulargroup extends UnlistedSpecialPage {

	protected $user ;
	protected $request ;
	protected $output ;
	public function __construct( $page = 'WatchParticularGroup' ) {
		parent::__construct( $page );
	}

	function execute( $par ) {

		/**
		 * 	Check if user is anonymous?
		 *  If User is Anon return with a msg displaying to login
		 */

		$this->user = $this->getUser() ;
		$this->output = $this->getOutput() ;

		if ( $this->user->isAnon() ) {
			SpecialWatchGroup::userIsAnon() ;
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
		// given namespace zero ,just  for testing. Should work for everynamespace
		$res = $dbr->select(
					'watchpages',
					 '*',
					array(
						'wp_user'		=>	$user->getId() ,
						'wp_groupname'	=> 	$groupname ,
						'wp_namespace'	=>  0
					),
				 __METHOD__ );
		foreach ( $res as $row ) {
			// To check the validity
			$list[] = $row->wp_title ;
		}
		return $list ;
	}

	public function validateGroupName( $groupname ) {
		$dbr = wfGetDB( DB_SLAVE, 'watchgroups' );
		$res = $dbr->select(
				'watchgroups',
				'*',
				array(
					'wtg_user' 		=>	$this->user->getId(),
					'wtg_groupname'	=>	$groupname,
				),
				__METHOD__
			);
		foreach ( $res as $group ) {
			return $group->wtg_groupname ;
		}

		return null ;

	}
	public function getUserWatchPages( $group ) {

		$list = array() ;
		$dbr = wfGetDB( DB_MASTER ) ;
		$res = $dbr->select(
			'watchpages' ,
			'*' ,
			array(
				'wtg_user' => $this->getUser()->getId() ,
				'wtg_groupname' => $group
			) ,
			__METHOD__
		) ;
		foreach ( $res as $row ) {
		// Add the valid pages from $res to list
			$list[] = $row->wtg_title ;
		}

		return $list;
	}

	/**
	Other basic functions to be defined
		a)Check the validity of the title
		b)Get the time, when this article last edited.
	*/
}