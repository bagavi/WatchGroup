-- MySQL version of the database schema for the WatchGroup Extension
-- Licence: GNU GPL v3+
-- Author Vivek Kumar Bagaria

 
CREATE TABLE IF NOT EXISTS watchpages(
	wp_id				INT unsigned			NOT NULL auto_increment PRIMARY KEY ,
	wp_user				INT unsigned			NOT NULL , 			-- 	UserID ,
	wp_title				VARCHAR(255)		NOT NULL ,			--	Title of the page
	wg_groupname 		VARCHAR(255)		NOT NULL , 			-- 	Name of the group
 	wp_namespace		INT unsigned			NOT NULL ,			--	Namespace number of the page
 	wp_notifytimestamp	VARCHAR(15)							
) ;

CREATE UNIQUE INDEX wp_id			ON watchpages(wp_id) ;
CREATE UNIQUE INDEX wp_user			ON watchpages(wp_id) ;
CREATE UNIQUE INDEX wp_title			ON watchpages(wp_id) ;
CREATE UNIQUE INDEX wp_groupname		ON watchpages(wp_id) ;
CREATE UNIQUE INDEX wp_namespace		ON watchpages(wp_id) ;
CREATE UNIQUE INDEX wp_notifytimestamp	ON watchpages(wp_id) ;

