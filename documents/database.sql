# MySQL dump 8.16
#
#--------------------------------------------------------
# Server version	4.0.12-max-log

#
# Table structure for table 'admin'
#

CREATE TABLE admin (
  id int(11) unsigned default NULL,
  admin tinyint(4) default NULL
) TYPE=MyISAM;

#
# Dumping data for table 'admin'
#

INSERT INTO admin VALUES (1,1);

#
# Table structure for table 'category'
#

CREATE TABLE category (
  id int(11) unsigned NOT NULL auto_increment,
  name varchar(255) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Dumping data for table 'category'
#

INSERT INTO category VALUES (1,'SOP');
INSERT INTO category VALUES (2,'Training Manual');
INSERT INTO category VALUES (3,'Letter');
INSERT INTO category VALUES (4,'Presentation');

#
# Table structure for table 'data'
#

CREATE TABLE data (
  id int(11) unsigned NOT NULL auto_increment,
  category tinyint(4) unsigned NOT NULL default '0',
  owner int(11) unsigned default NULL,
  realname varchar(255) NOT NULL default '',
  created datetime NOT NULL default '0000-00-00 00:00:00',
  description varchar(255) default NULL,
  comment varchar(255) NOT NULL default '',
  status smallint(6) default NULL,
  department int(11) unsigned default NULL,
  default_rights tinyint(4) default NULL,
  publishable tinyint(4) default NULL,
  reviewer int(11) unsigned default NULL,
  reviewer_comments varchar(255) default NULL,
  PRIMARY KEY  (id),
  KEY data_idx (id,owner),
  KEY id (id),
  KEY id_2 (id),
  KEY publishable (publishable),
  KEY description (description)
) TYPE=MyISAM;

#
# Dumping data for table 'data'
#

#
# Table structure for table 'department'
#

CREATE TABLE department (
  id int(11) unsigned NOT NULL auto_increment,
  name varchar(255) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Dumping data for table 'department'
#

INSERT INTO department VALUES (1,'Information Systems');

#
# Table structure for table 'dept_perms'
#

CREATE TABLE dept_perms (
  fid int(11) unsigned default NULL,
  dept_id int(11) unsigned default NULL,
  rights tinyint(4) NOT NULL default '0',
  KEY rights (rights),
  KEY dept_id (dept_id),
  KEY fid (fid)
) TYPE=MyISAM;

#
# Dumping data for table 'dept_perms'
#


#
# Table structure for table 'dept_reviewer'
#

CREATE TABLE dept_reviewer (
  dept_id int(11) unsigned default NULL,
  user_id int(11) unsigned default NULL
) TYPE=MyISAM;

#
# Dumping data for table 'dept_reviewer'
#

INSERT INTO dept_reviewer VALUES (1,1);

#
# Table structure for table 'log'
#

CREATE TABLE log (
  id int(11) unsigned NOT NULL default '0',
  modified_on datetime NOT NULL default '0000-00-00 00:00:00',
  modified_by varchar(25) default NULL,
  note text,
  revision varchar(255) default NULL,
  KEY id (id),
  KEY modified_on (modified_on)
) TYPE=MyISAM;

#
# Dumping data for table 'log'
#


#
# Table structure for table 'rights'
#

CREATE TABLE rights (
  RightId tinyint(4) default NULL,
  Description varchar(255) default NULL
) TYPE=MyISAM;

#
# Dumping data for table 'rights'
#

INSERT INTO rights VALUES (0,'none');
INSERT INTO rights VALUES (1,'view');
INSERT INTO rights VALUES (-1,'forbidden');
INSERT INTO rights VALUES (2,'read');
INSERT INTO rights VALUES (3,'write');
INSERT INTO rights VALUES (4,'admin');

#
# Table structure for table 'user'
#

CREATE TABLE user (
  id int(11) unsigned NOT NULL auto_increment,
  username varchar(25) NOT NULL default '',
  password varchar(50) NOT NULL default '',
  department int(11) unsigned default NULL,
  phone varchar(20) default NULL,
  Email varchar(50) default NULL,
  last_name varchar(255) default NULL,
  first_name varchar(255) default NULL,
  pw_reset_code CHAR(32) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Dumping data for table 'user'
#

INSERT INTO user VALUES (1,'admin','',1,'5555551212','admin@example.com','User','Admin','');

#
# Table structure for table 'user_perms'
#

CREATE TABLE user_perms (
  fid int(11) unsigned default NULL,
  uid int(11) unsigned NOT NULL default '0',
  rights tinyint(4) NOT NULL default '0',
  KEY user_perms_idx (fid,uid,rights),
  KEY fid (fid),
  KEY uid (uid),
  KEY rights (rights)
) TYPE=MyISAM;

#
# Dumping data for table 'user_perms'
#


# New User Defined Fields Table
#
# field_type describes what type of UDF this is. At the momment
# the valid values are:
#
#   1 = Drop down style list
#   2 = Radio Buttons
#
# table_name names the database table where the allow values are listed
#
# display_name is the label shown to the user

CREATE TABLE udf
(
    id  int(11) auto_increment unique,
    table_name  varchar(16),
    display_name    varchar(16),
    field_type  int
) TYPE=MyISAM;

