<?php
/**
 * @licence GNU GPL v3+
 * @author vivekkumarbagaria <vivekee047@gmail.com>
 *
 * Yet To Code
 * Function : To view/change preference of <groupname>
 *
 * Basic list of preferences
 *	1)Viewable WatchGroup	- This would allow other users to view the watchgroup
 *	2)Public Editable		- This will allow any user to edit the watchgroup
 *	3)Default conditions while displaying the pages of the group
 *		a)Hide minor edit
 *		b)Hide Bot Edit
 *		c)Hide My Edits
 *		d)TimeStamp
 *		e)etc.

 * 	Other information to be displayed
 * 	WatchGroup Token
 */
class SpecialWatchGroupPreference extends SpecialPage {

	public function __construct() {
		parent::__construct( 'WatchGroupPreference' );
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
		 * Display all the preference details of the group
		 * Display some important creation details of the group(For eg Admin , no of pages)
		 */

		// Create a form to have checkboxes to change the above mentioned preference + submitbutton + restore default button

	}

	/*
	 * This function will be executed by submit button
	 */
	public function updatePreferences() {
		/*
		 * Extract the data from the form and make appropriate changes in the database
		 */
	}

	public function restoreDefault() {
		// Restore default options for the given group
	}
}
