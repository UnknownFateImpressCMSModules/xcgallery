# phpMyAdmin MySQL-Dump
# version 2.2.6
# http://phpwizard.net/phpMyAdmin/
# http://www.phpmyadmin.net/ (download page)
#
# Host: localhost
# Erstellungszeit: 07. November 2003 um 16:10
# Server Version: 4.00.01
# PHP-Version: 4.3.1
# Datenbank : `xoops`
# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `xcgal_albums`
#

CREATE TABLE xcgal_albums (
  aid int(11) NOT NULL auto_increment,
  title varchar(255) NOT NULL default '',
  description text NOT NULL,
  visibility int(11) NOT NULL default '0',
  uploads enum('YES','NO') NOT NULL default 'NO',
  comments enum('YES','NO') NOT NULL default 'YES',
  votes enum('YES','NO') NOT NULL default 'YES',
  pos int(11) NOT NULL default '0',
  category int(11) NOT NULL default '0',
  pic_count int(11) NOT NULL default '0',
  thumb int(11) NOT NULL default '0',
  last_addition datetime NOT NULL default '0000-00-00 00:00:00',
  stat_uptodate enum('YES','NO') NOT NULL default 'NO',
  PRIMARY KEY  (aid),
  KEY alb_category (category)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `xcgal_categories`
#

CREATE TABLE xcgal_categories (
  cid int(11) NOT NULL auto_increment,
  owner_id int(11) NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  description text NOT NULL,
  pos int(11) NOT NULL default '0',
  parent int(11) NOT NULL default '0',
  subcat_count int(11) NOT NULL default '0',
  alb_count int(11) NOT NULL default '0',
  pic_count int(11) NOT NULL default '0',
  stat_uptodate enum('YES','NO') NOT NULL default 'NO',
  PRIMARY KEY  (cid),
  KEY cat_parent (parent),
  KEY cat_pos (pos),
  KEY cat_owner_id (owner_id)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `xcgal_ecard`
#

CREATE TABLE xcgal_ecard (
  e_id varchar(25) NOT NULL default '0',
  sess_id varchar(32) NOT NULL default '',
  sender_ip varchar(15) NOT NULL default '',
  sender_uid mediumint(8) NOT NULL default '0',
  sender_name varchar(60) NOT NULL default '',
  sender_email varchar(60) NOT NULL default '',
  recipient_name varchar(60) NOT NULL default '',
  recipient_email varchar(60) NOT NULL default '',
  greetings varchar(250) NOT NULL default '',
  message text NOT NULL,
  s_time int(10) NOT NULL default '0',
  pid int(11) NOT NULL default '0',
  picked tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (e_id)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `xcgal_pictures`
#

CREATE TABLE xcgal_pictures (
  pid int(11) NOT NULL auto_increment,
  aid int(11) NOT NULL default '0',
  filepath varchar(255) NOT NULL default '',
  filename varchar(255) NOT NULL default '',
  filesize int(11) NOT NULL default '0',
  total_filesize int(11) NOT NULL default '0',
  pwidth smallint(6) NOT NULL default '0',
  pheight smallint(6) NOT NULL default '0',
  hits int(10) NOT NULL default '0',
  mtime int(11) NOT NULL default '0',
  ctime int(11) NOT NULL default '0',
  owner_id int(11) NOT NULL default '0',
  owner_name varchar(40) NOT NULL default '',
  pic_rating int(11) NOT NULL default '0',
  votes int(11) NOT NULL default '0',
  title varchar(255) NOT NULL default '',
  caption text NOT NULL,
  keywords varchar(255) NOT NULL default '',
  approved enum('YES','NO') NOT NULL default 'NO',
  user1 varchar(255) NOT NULL default '',
  user2 varchar(255) NOT NULL default '',
  user3 varchar(255) NOT NULL default '',
  user4 varchar(255) NOT NULL default '',
  url_prefix tinyint(4) NOT NULL default '0',
  randpos int(11) NOT NULL default '0',
  ip varchar(15) NOT NULL default '',
  sent_card int(10) NOT NULL default '0',
  PRIMARY KEY  (pid),
  KEY pic_hits (hits),
  KEY pic_rate (pic_rating),
  KEY aid_approved (aid,approved),
  KEY randpos (randpos),
  KEY pic_aid (aid)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `xcgal_usergroups`
#

CREATE TABLE xcgal_usergroups (
  group_id int(11) NOT NULL auto_increment,
  group_name varchar(255) NOT NULL default '',
  group_quota int(11) NOT NULL default '0',
  has_admin_access tinyint(4) NOT NULL default '0',
  can_rate_pictures tinyint(4) NOT NULL default '0',
  can_send_ecards tinyint(4) NOT NULL default '0',
  can_post_comments tinyint(4) NOT NULL default '0',
  can_upload_pictures tinyint(4) NOT NULL default '0',
  can_create_albums tinyint(4) NOT NULL default '0',
  pub_upl_need_approval tinyint(4) NOT NULL default '1',
  priv_upl_need_approval tinyint(4) NOT NULL default '1',
  xgroupid smallint(5) NOT NULL default '0',
  PRIMARY KEY  (group_id)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `xcgal_votes`
#

CREATE TABLE xcgal_votes (
  pic_id mediumint(9) NOT NULL default '0',
  ip varchar(60) NOT NULL default '',
  vote_time int(11) NOT NULL default '0',
  v_uid int(11) NOT NULL default '0',
  PRIMARY KEY  (pic_id,ip)
) TYPE=MyISAM;

INSERT INTO xcgal_categories VALUES (1, 0, 'User galleries', 'This category contains albums that belong to Coppermine users.', 1, 0, 0, 0, 0, 'NO'); 
