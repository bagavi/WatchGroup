-- MySQL version of the database schema for the WatchGroup Extension
-- Licence: GNU GPL v3+
-- Author Vivek Kumar Bagaria <vivekee047@gmail.com>


CREATE TABLE IF NOT EXISTS watchpages(
	id			INT unsigned		NOT NULL auto_increment PRIMARY KEY ,
	user			INT unsigned		NOT NULL ,		--	UserID ,
	title			VARCHAR(255)		NOT NULL ,		--	Title of the page
	groupname		VARCHAR(255)		NOT NULL ,		--	Name of the group
	namespace		INT unsigned		NOT NULL ,		--	Namespace number of the page
	notifytimestamp	VARCHAR(15)							
);

CREATE UNIQUE INDEX wp_id	ON watchpages(id);
CREATE INDEX wp_user		ON watchpages(user);
CREATE INDEX wp_title		ON watchpages(title);
CREATE INDEX wp_groupname	ON watchpages(groupname);
CREATE INDEX wp_namespace	ON watchpages(namespace);
CREATE INDEX wp_notifytimestamp	ON watchpages(notifytimestamp);


CREATE TABLE IF NOT EXISTS watchgroups (
	id		INT unsigned		NOT NULL auto_increment PRIMARY KEY ,
	user		INT unsigned		NOT NULL ,		--	UserID
	groupname	VARCHAR(255)		NOT NULL ,		--	Name of the group
	visible_group	SMALLINT unsigned	NOT NULL default 0,	--	Boolean - Can be viewed by any user
	public_editable	SMALLINT unsigned	NOT NULL default 0	--	Boolean - Can be edited by any user
/*Yet to include more columns for preferences of each watchgroup*/
);

CREATE UNIQUE INDEX wtg_id		ON	watchgroups (id);
CREATE INDEX wtg_user			ON	watchgroups (user);
CREATE INDEX wtg_groupname		ON	watchgroups (groupname);
CREATE INDEX wtg_visible_group		ON	watchgroups (visible_group);
CREATE INDEX wtg_public_editable	ON	watchgroups (public_editable);
