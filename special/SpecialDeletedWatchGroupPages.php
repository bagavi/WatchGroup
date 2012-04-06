<?php
/**
 * Yet To Code
 * @licence GNU GPL v3+ 
 * @author vivekkumarbagaria   <vivekee047@gmail.com>
 *
 * We need to keep seperate table for deleted pages because , if we keep the deleted pages
 * in the main watchgroup table, the queries will get costilier as user uses the watchgroup.
 *
 * 		Database model
 * 		TABLE : watchgroup_deleted
 * 		----------------------------------------------------------------
 * 		|	wg_user		|	wg_title	|	wg_group|
 * 		----------------------------------------------------------------
 * 		|				|			|			|
 * 		|				|			|			|
 * 		|				|			|			|
 * 		----------------------------------------------------------------
 * 		wg_user 	= 	User_Id
 * 		wg_title	=	title of the page
 * 		wg_group	=	Watch group id of the user
 *
 * What does this Specialpage do?
 * This displays all the pages which the user has deleted from his watchlist
 *
 * Currently i have given a basic code structure for this class,
 * more features and options to be included during the Planning Time
 */
    class SpecialDeletedWatchGroupPages extends UnlistedSpecialPage {

	public function __construct() {
			parent::__construct( 'DeletedWatchPages' );
	}

	public function execute() {
		/**
		 * Check if user is anonymous?
		 * If User is Anon return with a msg displaying to login
		 */
		$this->setHeaders();
		$this->outputHeader();
		// Add feed links to the ATOM. This will give the list of pages which are deleted by the user
		// from his watchgroup
		$deletedpages = extractDeletedPages() ;

		/*
		 * Display the information in $deletedpages
		 */

	}

	/*
	*	This function will be executed when the submit button of the form is clicked
	*/
	public function addBackPages() {
		/*
		 * Check the pages which have been checked in the form.
		 * Remove these pages from the watchgroup_deleted table
		 * Add those pages to the watchgroup table
		 */
		
	}

	public function extractDeletedPages() {
		/**
		 * Database query
		 */
		$res = $dbr->select(
				'watchgroup_deleted',
				'*',
				array(
					'wl_user' => $this->getUser()->getId(),
				),
				__METHOD__
			);
		$title = array() ;
		/**
		 * Loop through res , check which of the pages are valid(some pages may have been deleted)
		 * Add all the titles in the array $titles
		 *
		 */
		return $title ;
	}
}
