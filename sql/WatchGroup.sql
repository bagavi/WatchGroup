-- MySQL version of the database schema for the WatchGroup Extension
-- Licence: GNU GPL v3+
-- Author Vivek Kumar Bagaria

 
CREATE TABLE IF NOT EXISTS watchpages(
	wp_id			INT unsigned			NOT NULL auto_increment PRIMARY KEY ,
	wp_user			INT unsigned			NOT NULL ,			--	UserID ,
	wp_title			VARCHAR(255)		NOT NULL ,			--	Title of the page
	wp_groupname		VARCHAR(255)		NOT NULL ,			--	Name of the group
	wp_namespace		INT unsigned			NOT NULL ,			--	Namespace number of the page
	wp_notifytimestamp	VARCHAR(15)							
);

CREATE UNIQUE INDEX wp_id			ON watchpages(wp_id);
CREATE UNIQUE INDEX wp_user			ON watchpages(wp_id);
CREATE UNIQUE INDEX wp_title			ON watchpages(wp_id);
CREATE UNIQUE INDEX wp_groupname		ON watchpages(wp_id);
CREATE UNIQUE INDEX wp_namespace		ON watchpages(wp_id);
CREATE UNIQUE INDEX wp_notifytimestamp	ON watchpages(wp_id);


CREATE TABLE IF NOT EXISTS watchgroups (
	wtg_id				INT unsigned			NOT NULL auto_increment PRIMARY KEY ,
	wtg_user				INT unsigned			NOT NULL ,			--	UserID
	wtg_groupname 		VARCHAR(255)		NOT NULL ,			--	Name of the group
	wtg_visible_group		SMALLINT unsigned	NOT NULL default 0,	--	Boolean - Can be viewed by any user
	wtg_public_editable	SMALLINT unsigned	NOT NULL default 0	--	Boolean - Can be edited by any user
/*Yet to include more columns for preferences of each watchgroup*/
);

CREATE UNIQUE INDEX wtg_id		ON	watchgroups (wtg_id);
CREATE INDEX wtg_user			ON	watchgroups (wtg_user);
CREATE INDEX wtg_groupname		ON	watchgroups (wtg_groupname);
CREATE INDEX wtg_visible_group		ON	watchgroups (wtg_visible_group);
CREATE INDEX wtg_public_editable	ON	watchgroups (wtg_public_editable);


