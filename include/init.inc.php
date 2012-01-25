<?php
// $Id$     mcleines 23082005
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

if (file_exists("../../mainfile.php")) include_once "../../mainfile.php";
else include_once "../../../mainfile.php";

//include_once ICMS_ROOT_PATH."/class/xoopsmodule.php";
if ( !defined('IN_XCGALLERY') ) redirect_header(ICMS_URL."/", 3, _NOPERM);

if (ini_get('safe_mode') == 1) {
   define('SILLY_SAFE_MODE',1);
}
//define('SILLY_SAFE_MODE',1);

$xcgal_module_header = '<link rel="stylesheet" type="text/css" href="xcgalstyle.css" />';

// used for timing purpose
$query_stats = array();

function getmicrotime(){
        list($usec, $sec) = explode(" ",microtime());
        return ((float)$usec + (float)$sec);
}
$time_start = getmicrotime();

// Do some cleanup in GET, POST and cookie data and un-register global vars

        if (is_array($_POST)) {
                foreach ($_POST as $key => $value){
                        if (!is_array($value))
                                $_POST[$key] = $value;
                        if (isset($$key)) unset($$key);
                }
        }

        if (is_array($_GET)) {
                foreach ($_GET as $key => $value){
                        $_GET[$key] = $value;
                        if (isset($$key)) unset($$key);
                }
        }

        if (is_array($_COOKIE)) {
                foreach ($_COOKIE as $key => $value){
                        if (isset($$key)) unset($$key);
                }
        }


// Initialise the $CONFIG array and some other variables
$CONFIG=array();
$PHP_SELF = isset($_SERVER['REDIRECT_URL']) ? $_SERVER['REDIRECT_URL'] : $_SERVER['SCRIPT_NAME'];
$REFERER = urlencode( $PHP_SELF . (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] ? '?'.$_SERVER['QUERY_STRING'] : ''));
$ALBUM_SET ='';
$FORBIDDEN_SET ='';
$CURRENT_CAT_NAME ='';

// Define some constants
define('USER_GAL_CAT', 1);
define('FIRST_USER_CAT', 10000);
define('RANDPOS_MAX_PIC', 200);
define('RANDPOS_INTERVAL',5);
//define('TEMPLATE_FILE', 'template.html');

$IMG_TYPES = array(
        1 => 'GIF',
        2 => 'JPG',
        3 => 'PNG',
        4 => 'SWF',
        5 => 'PSD',
        6 => 'BMP',
        7 => 'TIFF',
        8 => 'TIFF',
        9 => 'JPC',
        10 => 'JP2',
        11 => 'JPX',
        12 => 'JB2',
        13 => 'SWC',
        14 => 'IFF'
);

$MOV_TYPES = array(
    1 => 'MPG',
    2 => 'MPEG',
    3 => 'WMV',
    4 => 'QT',
    5 => 'SWF',
    6 => 'AVI',
    7 => 'MOV'
    );

$SND_TYPES = array(1 => 'MP3',
    2 => 'MIDI',
    3 => 'MID',
    4 => 'WMA',
    5 => 'WAV'
    );

$DOC_TYPES = array(1 => 'DOC',
    2 => 'TXT',
    3 => 'RTF',
    4 => 'PDF',
    5 => 'XLS',
    6 => 'PPT',
    7 => 'ZIP',
    8 => 'RAR'
    );

// Include config and functions files
$xcgalDir = basename(dirname(dirname(__FILE__)));
require ICMS_ROOT_PATH."/modules/".$xcgalDir."/include/functions.inc.php";

// Parse cookie stored user profile
user_get_profile();

// Authenticate
if (is_object($xoopsUser)) {
                $cookie_uid  = $xoopsUser->getVar('uid');
        //$cookie_pass = substr(addslashes($_COOKIE[$CONFIG['cookie_name'] . '_pass']), 0, 32);


} else {
    $cookie_uid  = 0;
        $cookie_pass = '*';
}
if (is_object($xoopsUser)){
    $usergroups = $xoopsUser->getGroups();
    $usergroup= implode(",",$usergroups);
    $mygroup= $xoopsUser->getGroups();
    $sql = "SELECT * FROM ".$xoopsDB->prefix("xcgal_usergroups")." WHERE xgroupid IN ({$usergroup})";
        $results = $xoopsDB->query($sql);
    $USER_DATA['can_send_ecards'] = 0;
    $USER_DATA['can_rate_pictures'] = 0;
    $USER_DATA['can_post_comments'] = 0;
    $USER_DATA['can_upload_pictures'] = 0;
    $USER_DATA['can_create_albums'] = 0;
    $USER_DATA['pub_upl_need_approval'] = 1;
    $USER_DATA['priv_upl_need_approval'] = 1;
    $USER_DATA['group_quota'] = 0;
    $USER_DATA['group_id'] = $usergroups;
        while($ugroup=$xoopsDB->fetchArray($results)){
                    if ($ugroup['can_send_ecards'] == 1) $USER_DATA['can_send_ecards'] = $ugroup['can_send_ecards'];
            if ($ugroup['can_rate_pictures'] == 1) $USER_DATA['can_rate_pictures'] = $ugroup['can_rate_pictures'];
            if ($ugroup['can_post_comments'] == 1) $USER_DATA['can_post_comments'] = $ugroup['can_post_comments'];
            if ($ugroup['can_upload_pictures'] == 1) $USER_DATA['can_upload_pictures'] = $ugroup['can_upload_pictures'];
            if ($ugroup['can_create_albums'] == 1) $USER_DATA['can_create_albums'] = $ugroup['can_create_albums'];
            if ($ugroup['pub_upl_need_approval'] == 0) $USER_DATA['pub_upl_need_approval'] = $ugroup['pub_upl_need_approval'];
            if ($ugroup['priv_upl_need_approval'] == 0) $USER_DATA['priv_upl_need_approval'] = $ugroup['priv_upl_need_approval'];     #mcleines
            if ($ugroup['group_quota'] > $USER_DATA['group_quota']) $USER_DATA['group_quota'] = $ugroup['group_quota'];
            } // while
        $xoopsModule = XoopsModule::getByDirname($xcgalDir);
    if($xoopsUser->isAdmin($xoopsModule->getVar('mid'))) define('USER_IS_ADMIN', 1);
    else define('USER_IS_ADMIN', 0);
        //$USER_DATA = $xoopsDB->fetchArray($results);
        $USER_DATA['user_email']= $xoopsUser->getVar('email');
    define('USER_ID', $xoopsUser->getVar('uid'));
    define('USER_NAME', $xoopsUser->getVar('uname'));
    define('USER_CAN_SEND_ECARDS', (int)$USER_DATA['can_send_ecards']);
    define('USER_CAN_RATE_PICTURES', (int)$USER_DATA['can_rate_pictures']);
    define('USER_CAN_POST_COMMENTS', (int)$USER_DATA['can_post_comments']);
    define('USER_CAN_UPLOAD_PICTURES', (int)$USER_DATA['can_upload_pictures']);
    define('USER_CAN_CREATE_ALBUMS', (int)$USER_DATA['can_create_albums']);
    define('USER_CAN_SEE_FULL', 1);
        $xoopsDB->freeRecordSet($results);
} else {
    $results = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xcgal_usergroups")." WHERE group_id = ".XOOPS_GROUP_ANONYMOUS."");
        if (!($xoopsDB->getRowsNum($results))) die('<b>Coppermine critical error</b>:<br />The group table does not contain the Anonymous group !');
        $USER_DATA = $xoopsDB->fetchArray($results);
    define('USER_ID', 0);
    define('USER_NAME', $xoopsConfig['anonymous']);
    define('USER_IS_ADMIN', 0);
    define('USER_CAN_SEND_ECARDS', (int)$USER_DATA['can_send_ecards']);
    define('USER_CAN_RATE_PICTURES', (int)$USER_DATA['can_rate_pictures']);
    define('USER_CAN_POST_COMMENTS', (int)$USER_DATA['can_post_comments']);
    define('USER_CAN_UPLOAD_PICTURES', (int)$USER_DATA['can_upload_pictures']);
    define('USER_CAN_CREATE_ALBUMS', 0);
    define('USER_CAN_SEE_FULL', 0);
        $xoopsDB->freeRecordSet($results);
}

// Test if admin mode
$USER['am'] = isset($USER['am']) ? (int)$USER['am'] : 0;
define('GALLERY_ADMIN_MODE', USER_IS_ADMIN && $USER['am']);
define('USER_ADMIN_MODE', USER_ID && USER_CAN_CREATE_ALBUMS && $USER['am'] && !GALLERY_ADMIN_MODE);



?>