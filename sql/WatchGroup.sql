-- MySQL version of the database schema for the WatchGroup Extension
-- Licence: GNU GPL v3+
-- Author Vivek Kumar Bagaria

 
CREATE TABLE IF NOT EXISTS watchpages(
	wp_id				INT unsigned			NOT NULL auto_increment PRIMARY KEY ,
	wp_user				INT unsigned			NOT NULL , 			-- 	UserID ,
	wp_title				VARCHAR(255)		NOT NULL ,			--	Title of the page
	wp_groupname 		VARCHAR(255)		NOT NULL , 			-- 	Name of the group
 	wp_namespace		INT unsigned			NOT NULL ,			--	Namespace number of the page
 	wp_notifytimestamp	VARCHAR(15)							
) ;

CREATE UNIQUE INDEX wp_id			ON watchpages(wp_id) ;
CREATE UNIQUE INDEX wp_user			ON watchpages(wp_id) ;
CREATE UNIQUE INDEX wp_title			ON watchpages(wp_id) ;
CREATE UNIQUE INDEX wp_groupname		ON watchpages(wp_id) ;
CREATE UNIQUE INDEX wp_namespace		ON watchpages(wp_id) ;
CREATE UNIQUE INDEX wp_notifytimestamp	ON watchpages(wp_id) ;


CREATE TABLE IF NOT EXISTS watchgroups (
	wg_id				INT unsigned			NOT NULL auto_increment PRIMARY KEY ,
	wg_user				INT unsigned			NOT NULL , 			-- 	UserID
	wg_groupname 		VARCHAR(255)		NOT NULL , 			-- 	Name of the group
	wg_visible_group		SMALLINT unsigned	NOT NULL default 0 ,	-- 	Boolean - Can be viewed by any user
	wg_public_editable	SMALLINT unsigned	NOT NULL default 0	-- 	Boolean - Can be edited by any user
/*Yet to include more columns for preferences of each watchgroup*/
) ;

CREATE UNIQUE INDEX wg_id 	ON 	watchgroups (wg_id) ;
CREATE INDEX wg_user 		ON 	watchgroups (wg_user) ;
CREATE INDEX wg_groupname	ON	watchgroups (wg_groupname) ;
CREATE INDEX wg_visible_group 	ON 	watchgroups (wg_visible_group) ;
CREATE INDEX wg_public_editable ON 	watchgroups (wg_public_editable) ;


