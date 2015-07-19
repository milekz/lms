<?php

/*
 *  iNET LMS
 *
 *  (C) Copyright 2001-2012 LMS Developers
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License Version 2 as
 *  published by the Free Software Foundation.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
 *  USA.
 *
 */

$DB->BeginTrans();

$DB->Execute("DROP VIEW IF EXISTS customersview;");
$DB->Execute("DROP VIEW IF EXISTS contractorview;");

$DB->Execute("ALTER TABLE customers CHANGE tv_cust_number tv_cust_number VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_polish_ci NULL DEFAULT NULL;");

$DB->Execute("
	CREATE VIEW customersview AS
	SELECT c.* FROM customers c
	WHERE NOT EXISTS (
	SELECT 1 FROM customerassignments a
	JOIN excludedgroups e ON (a.customergroupid = e.customergroupid)
	WHERE e.userid = lms_current_user() AND a.customerid = c.id) 
	AND c.type IN ('0','1');
");

$DB->Execute("
	CREATE VIEW contractorview AS
	SELECT c.* FROM customers c
	WHERE c.type = '2';
");

$DB->Execute("UPDATE dbinfo SET keyvalue = ? WHERE keytype = ?", array('2015070900', 'dbvex'));

$DB->CommitTrans();

?>