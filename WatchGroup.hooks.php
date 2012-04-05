<?php
/**
 * @licence GNU GPL v3+ 
 *
 * @author vivekkumarbagaria <vivekee047@gmail.com>
 * 
 * Function:
 * Adds two tables to the database, when update.php is run
 */

class WatchGroupHooks {

	public static function onSchemaUpdate( DatabaseUpdater $updater ) {
		$dir = dirname( __FILE__ ) . '/' ;

		$updater->addExtensionTable(
			'watchgroups',
			$dir . 'sql/WatchGroup.sql'
		);

		$updater->addExtensionTable(
			'watchpages',
			$dir . 'sql/WatchGroup.sql'
		);

		return true;
	}
}