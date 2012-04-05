<?php
/**
 *
 * @author vivekkumarbagaria <vivekee047@gmail.com>
 *
 * Function : To view/change preference of <groupname>
 *
 * Basic list of preferences
 *	1)Share the WatchGroup	- This would allow other users to subscribe the watchgroup
 *	2)Public Editable   	- This will allow any user to edit the watchgroup
 *	3)Default conditions while displaying the pages of the group
 *		a)Hide minor edit
 *		b)Hide Bot Edit
 *		c)Hide My Edits
 *		d)TimeStamp
 *		e)etc.

 * Database model
 * 		TABLE : watchgroup
 * 		-------------------------------------------------------------------------------------------------------------------------
 * 		|	wg_user		|	wg_group	| 	wg_multiuser	|	wg_public		| 	wg_editable			| wg_other__preference3	|
 * 		-------------------------------------------------------------------------------------------------------------------------
 * 		|				|				|					|					|						|						|
 * 		|				|				|					|					|						|						|
 * 		|				|				|					|					|						|						|
 * 		|				|				|					|					|						|						|
 * 		|				|				|					|					|						|						|
 * 		------------------------------------------------------------------------------------------------------------------------
 * 		wg_user 			= 	User_Id
 * 		wg_group			=	Watch group id of the user
 * 		wg_multiuser		=	To allow multiple users share the group(the group is not public)
 * 		wg_public			= 	Boolen : to make the watchlist public or not
 * 		wg_editable			= 	To allow any user to edit the watchgroup
 *
 * 		How to incorporate multiple users sharing a watchgroup?
 * 			1)Any user can subscribe to a Wac=tchGroup
 * 			2)The admin of the watchgroup can grant the subcribed user to view his watchlist.
 * 				 	the admin can make the subcribed user the admin too(we add another row of same data with wg_user changed)
 *
 *
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

		// Display the success msg
	}

	public function restoreDefault() {
		// Restore default options for the given group
	}
}