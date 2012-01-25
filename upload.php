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
define('IN_XCGALLERY', true);

include "../../mainfile.php";
include_once ICMS_ROOT_PATH."/class/module.errorhandler.php";
include_once ICMS_ROOT_PATH."/include/xoopscodes.php";
$myts =& MyTextSanitizer::getInstance(); // MyTextSanitizer object
//$eh = new ErrorHandler; //ErrorHandler object

require('include/init.inc.php');
function form_alb_list_box()
{
        global $_GET;
        global $user_albums_list, $public_albums_list, $other_user_albums_list;
    $text=_MD_ALBUM;
    $name= 'album';
        $sel_album = isset($_GET['album']) ? $_GET['album'] : 0;
    //var_dump($other_user_albums_list);
        $box= "<select name=\"$name\" class=\"listbox\">";
        if (count($user_albums_list) > 0 ){
        $box.= "<optgroup label=\""._MD_UPL_YOURALB."\">";
        foreach($user_albums_list as $album){
                        $box.="<option label=\"".$album['title'] . "\" value=\"".$album['aid']."\"".($album['aid'] == $sel_album ? ' selected' : '').">".$album['title'] . "</option>";
                }
        $box.="</optgroup>";
        }
        if (count($public_albums_list) > 0 ){
        $box.= "<optgroup label=\""._MD_UPL_ALBPUB."\">";
    foreach($public_albums_list as $album){
                        $box.="<option label=\"".$album['title'] . "\" value=\"".$album['aid']."\"".($album['aid'] == $sel_album ? ' selected' : '').">".$album['title'] . "</option>";
                }
        $box.="</optgroup>";
    }
    if (count($other_user_albums_list) > 0 ){
    $box.= "<optgroup label=\""._MD_UPL_OUSERALB."\">";
    $user_handler = icms::handler('icms_member');
    foreach($other_user_albums_list as $album){
            $alb_owner =& $user_handler->getUser($album['category']-FIRST_USER_CAT);
            if (is_object ($alb_owner)) {
            $box.="<option label=\"".$album['title'] . "(".$alb_owner->uname().")\" value=\"".$album['aid']."\"".($album['aid'] == $sel_album ? ' selected' : '').">".$album['title'] . "(".$alb_owner->uname().")</option>";
            }
                }
        $box.="</optgroup>";
        }
        $box.="</select>";
    return $box;
}

if (GALLERY_ADMIN_MODE) {
    $public_albums = $xoopsDB->query("SELECT aid, title FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE category < ".FIRST_USER_CAT." ORDER BY title");
} else {
        $public_albums = $xoopsDB->query("SELECT aid, title FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE category < ".FIRST_USER_CAT." AND uploads='YES' ORDER BY title");
}
if ($xoopsDB->getRowsNum($public_albums)) {
    $public_albums_list=db_fetch_rowset($public_albums);
} else {
        $public_albums_list = array();
}

if (USER_ID) {
        $user_albums = $xoopsDB->query("SELECT aid, title FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE category='".(FIRST_USER_CAT + USER_ID)."' ORDER BY title");
        if ($xoopsDB->getRowsNum($user_albums)) {
            $user_albums_list=db_fetch_rowset($user_albums);
        } else {
                $user_albums_list = array();
        }
} else {
        $user_albums_list = array();
}
        get_private_album_set();
    $other_user=$xoopsDB->query("SELECT aid, title, category FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE category > ".FIRST_USER_CAT." AND uploads='YES' AND category!='".(FIRST_USER_CAT + USER_ID)."' $ALBUM_SET ORDER BY category");

        //var_dump($other_user);
        if ($xoopsDB->getRowsNum($other_user)) {
            $other_user_albums_list=db_fetch_rowset($other_user);
        } else {
                $other_user_albums_list = array();
        }

if (!count($public_albums_list) && !count($user_albums_list) && !USER_CAN_CREATE_ALBUMS) {
    redirect_header('index.php',2,_MD_UPL_ERR_NO_ALB_UPLOAD);
} elseif (!count($public_albums_list) && !count($user_albums_list) && USER_CAN_CREATE_ALBUMS) {
        $USER['am'] = 1;
        $redirect = "albmgr.php";
        redirect_header($redirect,2,_MD_UPL_ERR_NO_ALB_UPLOAD);
        exit;
}

$xoopsOption['template_main'] = 'xcgal_upload.html';
include ICMS_ROOT_PATH."/header.php";
$xoopsTpl->assign('xoops_module_header', $xcgal_module_header);



ob_start();
$GLOBALS["caption"] = icms_core_DataFilter::htmlSpecialchars("");

xoopsCodeTarea("caption",37,8);
$xoopsTpl->assign('xoops_codes', ob_get_contents());
ob_end_clean();
ob_start();
xoopsSmilies("caption");
$xoopsTpl->assign('xoops_smilies', ob_get_contents());
ob_end_clean();
$xoopsTpl->assign('max_upl',sprintf(_MD_UPL_MAX_FSIZE, $xoopsModuleConfig['max_upl_size']));
$xoopsTpl->assign('lang_upload', _MD_UPL_TITLE);
$xoopsTpl->assign('lang_album', _MD_ALBUM);
$xoopsTpl->assign('lang_picture', _MD_UPL_PICTURE);
$xoopsTpl->assign('lang_picture_title', _MD_UPL_PIC_TITLE);
$xoopsTpl->assign('lang_keywords', _MD_UPL_KEYWORDS);
$xoopsTpl->assign('max_file_size',$xoopsModuleConfig['max_upl_size']<<10);
$xoopsTpl->assign('lang_options', "_MD_OPTIONS");
$xoopsTpl->assign('lang_notify', "_MD_NOTIFYAPPROVE");
$xoopsTpl->assign('lang_description', _MD_UPL_DESCRIPTION);
$xoopsTpl->assign('lang_submit', _SUBMIT);
$xoopsTpl->assign('lang_cancel', _CANCEL);
$xoopsTpl->assign('lang_quote', _QUOTEC);
$xoopsTpl->assign('album_selbox', form_alb_list_box());
$xoopsTpl->assign('user1',$xoopsModuleConfig['user_field1_name']);
$xoopsTpl->assign('user2',$xoopsModuleConfig['user_field2_name']);
$xoopsTpl->assign('user3',$xoopsModuleConfig['user_field3_name']);
$xoopsTpl->assign('user4',$xoopsModuleConfig['user_field4_name']);
user_save_profile();
$xoopsTpl->assign('gallery', $xoopsModule->getVar('name'));
include_once "include/theme_func.php";
main_menu();
do_footer();
include_once "../../footer.php";
?>
