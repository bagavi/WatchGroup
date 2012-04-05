<?php
/**
 * @author vivekkumarbagaria - Potter  <vivekee047@gmail.com>
 *
 * 		Database model
 * 		TABLE : watchpages
 * 		-------------------------------------------------------------------------------------------------------------
 * 		|	wg_user		|	wg_title	|	wg_group	|	wg_namespace	|	wg_notifytimestamp	|	wg_hits		|
 * 		-------------------------------------------------------------------------------------------------------------
 * 		|				|				|				|					|						|				|
 * 		|				|				|				|					|						|				|
 * 		|				|				|				|					|						|				|
 * 		|				|				|				|					|						|				|
 * 		|				|				|				|					|						|				|
 * 		-------------------------------------------------------------------------------------------------------------
 * 		wg_user 			= 	User_Id
 * 		wg_title			=	title of the page
 * 		wg_group			=	Watch group id of the user
 * 		wg_namespce			= 	NameSpace of the given title
 * 		wg_notifytimestamp	= 	Notification TimeStamp
 *
 * 	(may be)wg_hits			= 	Number of time the user goes on this page, this will help us to know
 * 								which oages are more important.
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



		/*
		 * Check the mode(view , rawEdit , checkboxEdit), redirect to
		 * Special:EditWatchGroup/<groupname> if the user wants to edit his <groupname>
		 */

		/*
		* Check the query values from the link eg

		a) Hide Bot , Hide Recent-Edit , Hide Reviewed-Edit etc.
		b) TimeStamp.
		c) NameSpace of the pages.
		*/


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
					'wg_user' 		=>	$this->user->getId(),
					'wg_groupname'	=>	$groupname,
				),
				__METHOD__
			);
		foreach ( $res as $group ) {
			return $group->wg_groupname ;
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
				'wg_user' => $this->getUser()->getId() ,
				'wg_groupname' => $group
			) ,
			__METHOD__
		) ;
		foreach ( $res as $row ) {
			$list[] = $row->wg_title ;
		}
		// Add the valid pages from $res to list
		return $list;
	}


	/**
	Other basic functions
		a)Check the validity of the title
		b)Get the time, when this article last edited.
	*/
}
