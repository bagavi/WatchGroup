<?php
/**
 * @author Vivek Kumar Bagaria <vivekee047@gmail.com>
 * @licence GNU GPL v3+
 */


if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}


define( 'VERSION', '0.0 alpha' );

$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'WatchGroup',
	'version' => 0.0,
	'author' => array(
		'[http://www.mediawiki.org/wiki/User:Bagariavivek Vivek Kumar Bagaria]',
	),
	"url" => "http://www.mediawiki.org/wiki/Extension:WatchGroup",
	'descriptionmsg' => 'watchgroup-desc'
);

$dir = dirname( __FILE__ ) . '/';
// i18n
$wgExtensionMessagesFiles['WatchGroup'] = $dir . '/WatchGroup.i18n.php';
// AutoLoadFiles

$wgAutoloadClasses['WatchGroupHooks'] 				=	$dir . 'WatchGroup.hooks.php';
$wgAutoloadClasses['ApiQueryWatchGroup'] 			=	$dir . 'api/ApiQueryWatchGroup.php';
$wgAutoloadClasses['ApiQueryWatchGroupPages'] 		=	$dir . 'api/ApiQueryWatchGroupPages.php';
$wgAutoloadClasses['SpecialWatchGroups']			=	$dir . 'special/SpecialWatchGroups.php';
$wgAutoloadClasses['SpecialWatchGroupPages']		=	$dir . 'special/SpecialWatchGroupPages.php';
$wgAutoloadClasses['SpecialWatchListToWatchGroup'] 	=	$dir . 'special/SpecialWatchListToWatchGroup.php';
$wgAutoloadClasses['SpecialEditWatchGroups']		=	$dir . 'special/SpecialEditWatchGroups.php';
$wgAutoloadClasses['SpecialEditWatchGroupPages']	=	$dir . 'special/SpecialEditWatchGroupPages.php' ;
$wgAutoloadClasses['WatchedGroupItem']				=	$dir . 'WatchedGroupItem.php';
$wgAutoloadClasses['WatchGroupAction']				=	$dir . 'action/WatchGroupAction.php';
$wgAutoloadClasses['UnWatchGroupAction']			=	$dir . 'action/WatchGroupAction.php';
// APIs
$wgAPIListModules['watchgroups']					=	'ApiQueryWatchGroup';
$wgAPIListModules['watchgrouppages']				=	'ApiQueryWatchGroupPages';

// SpecialClasses
$wgSpecialPages['WatchGroups']						=	'SpecialWatchGroups';
$wgSpecialPages['WatchGroupPages']					=	'SpecialWatchGroupPages';
$wgSpecialPages['EditWatchGroups']					=	'SpecialEditWatchGroups';
$wgSpecialPages['EditWatchGroupPages']				=	'SpecialEditWatchGroupPages';
$wgSpecialPages['WatchListToWatchGroup']			=	'SpecialWatchListToWatchGroup';
// Hooks
$wgHooks['LoadExtensionSchemaUpdates'][]			=	'WatchGroupHooks::onSchemaUpdate';

// Actions
$wgActions['watchgroup'] = 'WatchGroupAction';
$wgActions['unwatchgroup'] = 'UnWatchGroupAction';

// Resource Loader Modules
$moduleTemplate = array(
	'localBasePath' => $dir . 'resources',
	'remoteExtPath' => 'WatchGroup/resources'
);
$wgResourceModules['watchgroup.table'] = $moduleTemplate + array(
	'styles' => array(
		'watchgroup.table.css',
	)
);

$wgResourceModules += array(
	'watchgroup.table' => $moduleTemplate + array(
		'styles' => 'watchgroup.table.css'
	)
);