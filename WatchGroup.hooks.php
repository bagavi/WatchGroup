<?php
/**
 * 
 */

class WatchGroupHooks{

	public static function onSchemaUpdate( DatabaseUpdater $updater ){
		$dir = dirname( __FILE__ ).'/' ;
		
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