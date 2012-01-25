<?php
// $Id$
//  ------------------------------------------------------------------------ //
//                    xcGal 2.0 - XOOPS Gallery Modul                        //
//  ------------------------------------------------------------------------ //
//  Based on      xcGallery 1.1 RC1 - XOOPS Gallery Modul                    //
//                    Copyright (c) 2003 Derya Kiran                         //
//  ------------------------------------------------------------------------ //
//  Based on Coppermine Photo Gallery 1.10 http://coppermine.sourceforge.net///
//                      developed by Grégory DEMAR                           //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
define("_AM_CONFIG","xcGallery Configuration");
define("_AM_GENERALCONF","General Configuration");
define("_AM_CATMNGR","Categories Manager");
define("_AM_USERMNGR","User Manager");
define("_AM_GROUPMNGR","Group Manager");
define("_AM_BATCHADD","Batch Add Pictures");
define("_AM_ECARDMNGR","Ecard Manager");
define("_AM_PICAPP","Pictures Waiting for approval");

define("_AM_PARAM_MISSING","Script called without the required parameter(s).");


// ------------------------------------------------------------------------- //
// File usermgr.php
// ------------------------------------------------------------------------- //
define("_AM_USERMGR_TITLE","xcGallery User manager");
define("_AM_USERMGR_USHOW","Show all users with albums/pics");
define("_AM_USERMGR_USHOWDEL","Show albums of all deleted users");
define("_AM_USERMGR_ULIST","User list");
define("_AM_USERMGR_USER","User");
define("_AM_USERMGR_ALBUMS","Albums");
define("_AM_USERMGR_PICS","Pictures");
define("_AM_USERMGR_QUOTA","Used quota");
define("_AM_USERMGR_ALB","Album");
define("_AM_USERMGR_DELUID","Del. user id");
define("_AM_USERMGR_OPT","Operations");
define("_AM_USERMGR_NOTMOVE","** Don't Move **");
define("_AM_USERMGR_DEL","Delete");
define("_AM_USERMGR_PROPS","Properties");
define("_AM_USERMGR_EDITP","Edit Pics");

define("_AM_USERMGR_UONPAGE","%d users on %d page(s)");
define("_AM_USERMGR_NOUSER","No User found!");

// ------------------------------------------------------------------------- //
// File searchnew.php
// ------------------------------------------------------------------------- //
define("_AM_SRCHNEW_TITLE","Search new pictures");
define("_AM_SRCHNEW_SEL_DIR","Select directory");
define("_AM_SRCHNEW_SEL_DIR_MSG","This function allows you to add a batch of picture that your have uploaded on your server by FTP.<br /><br />Select the directory where you have uploaded your pictures");
define("_AM_SRCHNEW_NO_PIC_ADD","There is no picture to add");
define("_AM_SRCHNEW_NEED_ONE_ALB","You need at least one album to use this function");
define("_AM_SRCHNEW_WARNING","Warning");
define("_AM_SRCHNEW_CHG_PERM","the script can't write in this directory, you need to change its mode to 755 or 777 before trying to add the pictures!");
define("_AM_SRCHNEW_TARGET_ALB","Put pictures of &quot;</b>%s<b>&quot; into </b>%s");
define("_AM_SRCHNEW_FOLDER","Folder");
define("_AM_SRCHNEW_IMAGE","Image");
define("_AM_SRCHNEW_ALB","Album");
define("_AM_SRCHNEW_RESULT","Result");
define("_AM_SRCHNEW_DIR_RO","Not writable. ");
define("_AM_SRCHNEW_CANT_READ","Not readable. ");
define("_AM_SRCHNEW_INSERT","Adding new pictures to the gallery");
define("_AM_SRCHNEW_LIST_NEW","List of new pictures");
define("_AM_SRCHNEW_INS_SEL","Insert selected pictures");
define("_AM_SRCHNEW_NO_PIC","No new picture was found");
define("_AM_SRCHNEW_PATIENT","Please be patient, the script needs time to add the pictures");
define("_AM_SRCHNEW_NOTES","<ul><li><b>OK</b> : means that the picture was succesfully added<li><b>DP</b> : means that the picture is a duplicate and is already in the database<li><b>PB</b> : means that the picture could not be added, check your configuration and the permission of directories where the pictures are located<li>If the OK, DP, PB 'signs' does not appear click on the broken picture to see any error message produced by PHP<li>If your browser timeout, hit the reload button</ul>");


// ------------------------------------------------------------------------- //
// File groupmgr.php
// ------------------------------------------------------------------------- //

define("_AM_GRPMGR_KB","KB");
define("_AM_GRPMGR_NAME","Group name");
define("_AM_GRPMGR_QUOTA","Disk quota");
define("_AM_GRPMGR_RATE","Can rate pictures");
define("_AM_GRPMGR_SENDCARD","Can send ecards");
define("_AM_GRPMGR_COM","Can post comments");
define("_AM_GRPMGR_UPLOAD","Can upload pictures");
define("_AM_GRPMGR_PRIVATE","Can have a personal gallery");
define("_AM_GRPMGR_APPLY","Apply modifications");
define("_AM_GRPMGR_MANAGE","Manage user groups");
define("_AM_GRPMGR_PUB_APPR","Pub. Upl. approval (1)");
define("_AM_GRPMGR_PRIV_APPR","Priv. Upl. approval (2)");
define("_AM_GRPMGR_PUB_NOTE","<b>(1)</b> Uploads in a public album need admin approval");
define("_AM_GRPMGR_PRIV_NOTE","<b>(2)</b> Uploads in an album that belong to the user need admin approval");
define("_AM_GRPMGR_NOTES","Notes");
define("_AM_GRPMGR_SYN","Synchronize");
define("_AM_GRPMGR_SYN_NOTE","Click 'Synchronize' for sychronizing xcGallery groups with Xoops groups");
define("_AM_GRPMGR_EMPTY","xcGallery Group table was empty !<br /><br />Default groups created.");

// ------------------------------------------------------------------------- //
// File catmgr.php
// ------------------------------------------------------------------------- //

define("_AM_CAT_MISS_PARAM","Parameters required for '%s' operation not supplied!");
define("_AM_CAT_UNKOWN","Selected category does not exist in database");
define("_AM_CAT_UGAL_CAT_RO","User galleries category can't be deleted!");
define("_AM_CAT_MNGCAT","Manage categories");
define("_AM_CAT_CONF_DEL","Are you sure you want to DELETE this category");
define("_AM_CAT_CAT","Category");
define("_AM_CAT_OP","Operations");
define("_AM_CAT_MOVE","Move into");
define("_AM_CAT_UPCR","Update/Create category");
define("_AM_CAT_PARENT","Parent category");
define("_AM_CAT_TITLE","Category title");
define("_AM_CAT_DESC","Category description");
define("_AM_CAT_NOCAT","* No Category *");

// ------------------------------------------------------------------------- //
// File ecardmgr.php
// ------------------------------------------------------------------------- //

define("_AM_CARDMGR_TITLE","xcGallery ecard manager");
define("_AM_CARDMGR_TIME","Date");
define("_AM_CARDMGR_SUNAME","Sender username");
define("_AM_CARDMGR_SEMAIL","Sender email");
define("_AM_CARDMGR_SIP","Sender ip");
define("_AM_CARDMGR_PID","Picture ID");
define("_AM_CARDMGR_STATUS","Picked");
define("_AM_CARDMGR_DEL_SELECTED","Delete selected ecards");
define("_AM_CARDMGR_DEL_ALL","Delete all ecards");
define("_AM_CARDMGR_DEL_PICKED","Delete all picked ecards");
define("_AM_CARDMGR_DEL_UNPICKED","Delete all unpicked ecards");
define("_AM_CARDMGR_CONPAGE","%d ecards on %d page(s)");

?>
