<?php
/**
 * Yet To Code
 *
 * @author vivekkumarbagaria <vivekee047@gmail.com>
 * @licence GNU GPL v3+
 * Function : To Edit the pages of <groupname>
 *
 */
	class SpecialEditWatchGroupPages extends SpecialPage {

	public function __construct() {
		parent::__construct( 'EditParticularWatchGroup' );
	}

	public function execute() {

	 	/**
		 * 	Check if user is anonymous?
		 * 	If User is Anon return with a msg displaying to login
		 */

		$this->setHeaders();
		$this->outputHeader();
		/*
		 * Extract the groupname from the url and check if such a group exists
		 * If it doesnt exists, give appropriate message and link to create one
		 */


		/*
		 * Get the mode of edit(raw or normal)
		 * If mode == rawedit
		 * 		Call function rawEdit().
		 * If mode == normaledit
		 * 		Call function normalEdit().
		 */

		// Display the success(or error) msg
		// Display the pages which have been deleted
	}

	/**
	*	To edit the pages in a raw format
	*	by displaying names of pages in a text box
	*/
	public function rawEdit() {
	/*
	 * Extract page list from the text box.
	 * Extract pages names from the database by the function defined in SpecialWatchGroup .
	 * Compare them and add/remove pages to database according to the difference of above to lists.
	 * Add the deleted pages to Table:watchgroup_deleted
	 */
	}

	/**
	*	To display pages with checkbox next to them.
	*	Delete all the groups marked with the checkbox
	*/
	public function NormalEdit() {
	/*
	 * Extract pages checked from the form
	 *
	 * Remove those pages from the Table:watchgroup
	 * Add the deleted pages to Table:watchgroup_deleted
	 */
	}
}
