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
//include "../../mainfile.php";
define('IN_XCGALLERY', true);

require('include/init.inc.php');
//include "comment_delete.php";
if (!(USER_IS_ADMIN || USER_ADMIN_MODE)) redirect_header('index.php',2,_MD_ACCESS_DENIED);

define('UPLOAD_APPROVAL_MODE', isset($_GET['mode']));
define('EDIT_PICTURES_MODE', !isset($_GET['mode']));
include_once ICMS_ROOT_PATH."/include/xoopscodes.php";
$myts =& MyTextSanitizer::getInstance(); // MyTextSanitizer object
if (isset($_GET['album'])) {
        $album_id = (int)$_GET['album'];
} elseif (isset($_GET['album'])) {
        $album_id = (int)$_POST['album'];
} else {
        $album_id = -1;
}

if (UPLOAD_APPROVAL_MODE && !USER_IS_ADMIN) redirect_header('index.php',2,_MD_ACCESS_DENIED);

if (EDIT_PICTURES_MODE) {
    $result = $xoopsDB->query("SELECT title, category FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE aid = '$album_id'");
        if (!$xoopsDB->getRowsNum($result)) redirect_header('index.php',2,_MD_NON_EXIST_AP);
        $ALBUM_DATA=$xoopsDB->fetchArray($result);
        $xoopsDB->freeRecordSet($result);
        $cat = $ALBUM_DATA['category'];
        $actual_cat = $cat;
        if ($cat != FIRST_USER_CAT + USER_ID && !GALLERY_ADMIN_MODE) redirect_header('index.php',2,_MD_PERM_DENIED);
} else {
        $ALBUM_DATA = array();
}

$THUMB_ROWSPAN=5;
if ($xoopsModuleConfig['user_field1_name'] != '') $THUMB_ROWSPAN++;
if ($xoopsModuleConfig['user_field2_name'] != '') $THUMB_ROWSPAN++;
if ($xoopsModuleConfig['user_field3_name'] != '') $THUMB_ROWSPAN++;
if ($xoopsModuleConfig['user_field4_name'] != '') $THUMB_ROWSPAN++;

$USER_ALBUMS_ARRAY=array(0 => array());

function get_post_var($var, $pid)
{
        global $_POST;

        $var_name = $var.$pid;
        if(!isset($_POST[$var_name])) redirect_header('index.php',2,_MD_PARAM_MISSING." ($var_name)");
        return $_POST[$var_name];
}

function process_post_data()
{
        global $_POST, $xoopsModuleConfig, $xoopsDB;
        global $user_albums_list, $xoopsModule, $myts;

        $user_album_set = array();
        foreach($user_albums_list as $album) $user_album_set[$album['aid']] = 1;

        if (!is_array($_POST['pid'])) redirect_header('index.php',2,_MD_PARAM_MISSING);
        $pid_array = &$_POST['pid'];
        foreach($pid_array as $pid){
                $pid = (int)$pid;

                $aid         = (int)get_post_var('aid', $pid);
                $title       = get_post_var('title', $pid);
                $caption     = get_post_var('caption', $pid);
                $keywords    = get_post_var('keywords', $pid);
                $user1       = get_post_var('user1', $pid);
                $user2       = get_post_var('user2', $pid);
                $user3       = get_post_var('user3', $pid);
                $user4       = get_post_var('user4', $pid);

                $delete       = isset($_POST['delete'.$pid]);
                $reset_vcount = isset($_POST['reset_vcount'.$pid]);
                $reset_votes  = isset($_POST['reset_votes'.$pid]);
                $del_comments = isset($_POST['del_comments'.$pid]) || $delete;

                $query = "SELECT category, filepath, filename, owner_id FROM ".$xoopsDB->prefix("xcgal_pictures").", ".$xoopsDB->prefix("xcgal_albums")." WHERE ".$xoopsDB->prefix("xcgal_pictures").".aid = ".$xoopsDB->prefix("xcgal_albums").".aid AND pid='$pid'";
                $result = $xoopsDB->query($query);
                if (!$xoopsDB->getRowsNum($result)) redirect_header('index.php',2,_MD_NON_EXIST_AP);
                $pic = $xoopsDB->fetchArray($result);
                $xoopsDB->freeRecordSet($result);

                if (!USER_IS_ADMIN) {
                        if ($pic['category'] != FIRST_USER_CAT + USER_ID) redirect_header('index.php',2, _MD_PERM_DENIED."<br />(picture category = {$pic['category']}/ $pid)");
                        if (!isset($user_album_set[$aid])) redirect_header('index.php',2,_MD_PERM_DENIED."<br />(target album = $aid)");
                }

                $update  = "aid = '".$aid."'";
                $update .= ", title = '".$myts->addSlashes($title)."'";
                $update .= ", caption = '".$myts->makeTareaData4Save($caption,0)."'";
                $update .= ", keywords = '".$myts->addSlashes($keywords)."'";
                $update .= ", user1 = '".$myts->addSlashes($user1)."'";
                $update .= ", user2 = '".$myts->addSlashes($user2)."'";
                $update .= ", user3 = '".$myts->addSlashes($user3)."'";
                $update .= ", user4 = '".$myts->addSlashes($user4)."'";

                if ($reset_vcount) $update .= ", hits = '0'";
                if ($reset_votes) $update .= ", pic_rating = '0', votes = '0'";

                if (UPLOAD_APPROVAL_MODE) {
                    $approved = get_post_var('approved', $pid);
                        if ($approved == 'YES') {
                                $update .= ", approved = 'YES'";
                        } elseif ($approved == 'DELETE') {
                                $del_comments = 1;
                                $delete = 1;
                        }
                }

                if ($del_comments) {
                        //$query = "DELETE FROM ".$xoopsDB->prefix("xcgal_comments")." WHERE pid='$pid'";
                        //$result =$xoopsDB->query($query);
                        xoops_comment_delete($xoopsModule->getVar('mid'), $pid);
                }

                if ($delete) {
                        $dir=$xoopsModuleConfig['fullpath'].$pic['filepath'];
                        $file=$pic['filename'];

                        if (!is_writable($dir)) redirect_header('index.php',2, sprintf(_MD_DIRECTORY_RO, $dir));

                        $files=array($dir.$file, $dir.$xoopsModuleConfig['normal_pfx'].$file, $dir.$xoopsModuleConfig['thumb_pfx'].$file);
                        foreach ($files as $currFile){
                                if (is_file($currFile)) @unlink($currFile);
                        }

                        $query = "DELETE FROM ".$xoopsDB->prefix("xcgal_pictures")." WHERE pid='$pid' LIMIT 1";
                        $result = $xoopsDB->query($query);
                } else {
                        $query = "UPDATE ".$xoopsDB->prefix("xcgal_pictures")." SET $update WHERE pid='$pid' LIMIT 1";
                        $result = $xoopsDB->query($query);
                        if ($pic['owner_id'] != 0){
                            $submitter = new XoopsUser($pic['owner_id']);
                            $submitter->incrementPost();

                        }
                }
        }
}

function form_pic_info()
{
        global $CURRENT_PIC, $THUMB_ROWSPAN, $xoopsModuleConfig,$xoopsConfig;
    global $filename, $pic_info, $thumb_url, $thumb_link,$myts;

        if (UPLOAD_APPROVAL_MODE) {
            $pic_info = $CURRENT_PIC['pwidth'].'x'.$CURRENT_PIC['pheight'].' - '.($CURRENT_PIC['filesize'] >> 10)._MD_KB;
                $user_handler = icms::handler('icms_member');
                $pic_owner =& $user_handler->getUser($CURRENT_PIC['owner_id']);
        if (is_object ($pic_owner)){
                        $pic_info .= ' - <a href ="'.ICMS_URL.'/userinfo.php?uid='.$pic_owner->uid().'" target="_blank">'.$pic_owner->uname().'</a>';
                } else $pic_info .= ' - '.$xoopsConfig['anonymous'];

        } else {
                $pic_info = sprintf(_MD_EDITPICS_INFOSTR, $CURRENT_PIC['pwidth'], $CURRENT_PIC['pheight'], ($CURRENT_PIC['filesize'] >> 10), $CURRENT_PIC['hits'], $CURRENT_PIC['votes']);
        }

        $thumb_url = get_pic_url($CURRENT_PIC, 'thumb');
        $thumb_link = 'displayimage.php?pid='.$CURRENT_PIC['pid'].'&amp;pos='.(-$CURRENT_PIC['pid']);
        $filename = icms_core_DataFilter::htmlSpecialchars($CURRENT_PIC['filename']);
}

function form_options()
{
        global $CURRENT_PIC, $pic_opt;

        if (UPLOAD_APPROVAL_MODE) {
                $pic_opt= "
                        <b><input type=\"radio\" name=\"approved{$CURRENT_PIC['pid']}\" value=\"YES\" class=\"radio\" />"._MD_EDITPICS_APPROVE."</b>&nbsp;
                        <b><input type=\"radio\" name=\"approved{$CURRENT_PIC['pid']}\" value=\"NO\" class=\"radio\" checked=\"checked\" />"._MD_EDITPICS_PP_APPROVE."</b>&nbsp;
                        <b><input type=\"radio\" name=\"approved{$CURRENT_PIC['pid']}\" value=\"DELETE\" class=\"radio\" />"._MD_EDITPICS_DEL_PIC."</b>&nbsp;";
        } else {
                $pic_opt= "
                        <b><input type=\"checkbox\" name=\"delete{$CURRENT_PIC['pid']}\" value=\"1\" class=\"checkbox\" />"._MD_EDITPICS_DEL_PIC."</b>&nbsp;
                        <b><input type=\"checkbox\" name=\"reset_vcount{$CURRENT_PIC['pid']}\" value=\"1\" class=\"checkbox\" />"._MD_EDITPICS_RVIEW."</b>&nbsp;
                        <b><input type=\"checkbox\" name=\"reset_votes{$CURRENT_PIC['pid']}\" value=\"1\" class=\"checkbox\" />"._MD_EDITPICS_RVOTES."</b>&nbsp;
                        <b><input type=\"checkbox\" name=\"del_comments{$CURRENT_PIC['pid']}\" value=\"1\" class=\"checkbox\" />"._MD_EDITPICS_DCOM."</b>&nbsp;";
        }
}

function form_alb_list_box()
{
        global $xoopsModuleConfig, $CURRENT_PIC;
        global $user_albums_list, $public_albums_list;
    global $sel_name, $alb_opt;
        $sel_album = $CURRENT_PIC['aid'];

        $sel_name = 'aid'.$CURRENT_PIC['pid'];
        $alb_opt='';
    foreach($public_albums_list as $album){
                $alb_opt.= '<option value="'.$album['aid'].'"'.($album['aid'] == $sel_album ? ' selected="selected"' : '').'>'.$album['title'] . "</option>\n";
                }
        foreach($user_albums_list as $album){
                $alb_opt.= '<option value="'.$album['aid'].'"'.($album['aid'] == $sel_album ? ' selected="selected"' : '').'>* '.$album['title'] . "</option>\n";
                }

}

function get_user_albums($user_id)
{
        global $USER_ALBUMS_ARRAY, $user_albums_list, $xoopsDB;

        if (!isset($USER_ALBUMS_ARRAY[$user_id])) {
                $user_albums = $xoopsDB->query("SELECT aid, title FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE category='".(FIRST_USER_CAT + $user_id)."' ORDER BY title");
                if ($xoopsDB->getRowsNum($user_albums)) {
                    $user_albums_list=db_fetch_rowset($user_albums);
                } else {
                        $user_albums_list = array();
                }
                $xoopsDB->freeRecordSet($user_albums);
                $USER_ALBUMS_ARRAY[$user_id] = $user_albums_list;
        } else {
                $user_albums_list = &$USER_ALBUMS_ARRAY[$user_id];
        }
}


if (USER_IS_ADMIN) {
    $public_albums = $xoopsDB->query("SELECT aid, title FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE category < '".FIRST_USER_CAT."' ORDER BY title");
        if ($xoopsDB->getRowsNum($public_albums)) {
            $public_albums_list=db_fetch_rowset($public_albums);
        } else {
                $public_albums_list = array();
        }
        $xoopsDB->freeRecordSet($public_albums);
} else {
        $public_albums_list = array();
}

get_user_albums(USER_ID);

if (count($_POST)) process_post_data();

$start = isset($_GET['start']) ? (int)$_GET['start'] : 0;
$count = isset($_GET['count']) ? (int)$_GET['count'] : 25;
$next_target = $PHP_SELF.'?album='.$album_id.'&amp;start='.($start+$count).'&amp;count='.$count;
$prev_target = $PHP_SELF.'?album='.$album_id.'&amp;start='.max(0,$start-$count).'&amp;count='.$count;
$s50 = $count == 50 ? 'selected="selected"' : '';
$s75 = $count == 75 ? 'selected="selected"' : '';
$s100 = $count == 100 ? 'selected="selected"' : '';

if (UPLOAD_APPROVAL_MODE) {
        $result=$xoopsDB->query("SELECT count(*) FROM ".$xoopsDB->prefix("xcgal_pictures")." WHERE approved = 'NO'");
        $nbEnr = $xoopsDB->fetchArray($result);
        $pic_count = $nbEnr['count(*)'];

    $sql =  "SELECT * ".
                        "FROM ".$xoopsDB->prefix("xcgal_pictures")." ".
                        //"LEFT JOIN ".$xoopsDB->prefix("users")." AS u ON owner_id = uid ".
                        "WHERE approved = 'NO' ".
                        "ORDER BY pid ".
                        "LIMIT $start, $count";
        $result = $xoopsDB->query($sql);
        $form_target = $PHP_SELF.'?mode=upload_approval&amp;start='.$start.'&amp;count='.$count;
        $title = _MD_EDITPICS_UPL_APPROVAL;
} else {
        $result=$xoopsDB->query("SELECT count(*) FROM ".$xoopsDB->prefix("xcgal_pictures")." WHERE aid = '$album_id'");
        $nbEnr = $xoopsDB->fetchArray($result);
        $pic_count = $nbEnr['count(*)'];
        $xoopsDB->freeRecordSet($result);

    $result = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xcgal_pictures")." WHERE aid = '$album_id' ORDER BY filename LIMIT $start, $count");
        $form_target = $PHP_SELF.'?album='.$album_id.'&amp;start='.$start.'&amp;count='.$count;
        $title = _MD_EDITPICS_EDIT;
}
global $HTTP_REFERER;
if (!$xoopsDB->getRowsNum($result)) redirect_header('admin/index.php',2,_MD_NO_IMG_TO_DISPLAY);

if ($start + $count < $pic_count) {
    $next_link = "<a href=\"$next_target\"><b>"._MD_EDITPICS_NEXT."</b></a>&nbsp;&nbsp;-&nbsp;&nbsp;";
} else {
        $next_link = '';
}

if ($start > 0) {
    $prev_link = "<a href=\"$prev_target\"><b>"._MD_EDITPICS_PREV."</b></a>&nbsp;&nbsp;-&nbsp;&nbsp;";
} else {
        $prev_link = '';
}

$pic_count_text = sprintf(_MD_NPICS, $pic_count);
$xoopsOption['template_main'] = 'xcgal_editpics.html';
include ICMS_ROOT_PATH."/header.php";
$xoopsTpl->assign('xoops_module_header', $xcgal_module_header);
//$xoopsTpl->assign('xcgal_header',pageheader($title));
$xoopsTpl->assign('title',$title);
$xoopsTpl->assign('form_target',$form_target);
$xoopsTpl->assign('pic_count_text',$pic_count_text);
$xoopsTpl->assign('prev_link',$prev_link);
$xoopsTpl->assign('next_link',$next_link);
$xoopsTpl->assign('n_of_pic_to_disp',_MD_EDITPICS_NUMDIS);
$xoopsTpl->assign('album_id',$album_id);
$xoopsTpl->assign('start',$start);
$xoopsTpl->assign('s50',$s50);
$xoopsTpl->assign('s75',$s75);
$xoopsTpl->assign('s100',$s100);

$form='';
$xoopsTpl->assign('lang_pic_info',_MD_EDITPICS_PIC_INFO);
$xoopsTpl->assign('rowspan',$THUMB_ROWSPAN);
$xoopsTpl->assign('lang_album',_MD_ALBUM);
$xoopsTpl->assign('lang_title',_MD_EDITPICS_TITLE);
$xoopsTpl->assign('lang_desc',_MD_EDITPICS_DESC);
$xoopsTpl->assign('lang_keywords',_MD_KEYS);
$xoopsTpl->assign('user1',$xoopsModuleConfig['user_field1_name']);
$xoopsTpl->assign('user2',$xoopsModuleConfig['user_field2_name']);
$xoopsTpl->assign('user3',$xoopsModuleConfig['user_field3_name']);
$xoopsTpl->assign('user4',$xoopsModuleConfig['user_field4_name']);


while($CURRENT_PIC = $xoopsDB->fetchArray($result)){
        if (USER_IS_ADMIN) {
        get_user_albums($CURRENT_PIC['owner_id']);
     //   $admin_mode=1;
        }
        //else $admin_mode=0;
        //$form.=create_form($data);
        form_alb_list_box();
    form_pic_info();
    ob_start();
    $GLOBALS["caption{$CURRENT_PIC['pid']}"] = icms_core_DataFilter::htmlSpecialchars($CURRENT_PIC['caption']);
    xoopsCodeTarea("caption{$CURRENT_PIC['pid']}",37,8);
    $xoops_codes= ob_get_contents();
    ob_end_clean();
    ob_start();
    xoopsSmilies(("caption".$CURRENT_PIC['pid']));
    $smilies= ob_get_contents();
    ob_end_clean();
    $value_field1 = icms_core_DataFilter::htmlSpecialchars($CURRENT_PIC['user1']);
        $name_field1 = 'user1'.$CURRENT_PIC['pid'];
    $value_field2 = icms_core_DataFilter::htmlSpecialchars($CURRENT_PIC['user2']);
        $name_field2 = 'user2'.$CURRENT_PIC['pid'];
    $value_field3 = icms_core_DataFilter::htmlSpecialchars($CURRENT_PIC['user3']);
        $name_field3 = 'user3'.$CURRENT_PIC['pid'];
    $value_field4 = icms_core_DataFilter::htmlSpecialchars($CURRENT_PIC['user4']);
        $name_field4 = 'user4'.$CURRENT_PIC['pid'];
    form_options();

        $xoopsTpl->append('pics', array('current' => $CURRENT_PIC['pid'],'filename'=>$filename,'pic_info' => $pic_info,'thumb_url' => $thumb_url,'thumb_link' => $thumb_link,'sel_name' => $sel_name,'alb_opt' => $alb_opt,'title_name'=> ('title'.$CURRENT_PIC['pid']),'title_value'=> icms_core_DataFilter::htmlSpecialchars($CURRENT_PIC['title']),'xoops_codes'=>$xoops_codes,'xoops_smilies'=>$smilies,'keywords_value'=>icms_core_DataFilter::htmlSpecialchars($CURRENT_PIC['keywords']),'keywords_name'=>('keywords'.$CURRENT_PIC['pid']),'name_field1'=>$name_field1,'value_field1'=>$value_field1,'name_field2'=>$name_field2,'value_field2'=>$value_field2,'name_field3'=>$name_field3,'value_field3'=>$value_field3,'name_field4'=>$name_field4,'value_field4'=>$value_field4,'pic_opt'=>$pic_opt));

} // while
$xoopsDB->freeRecordSet($result);
$xoopsTpl->assign('form',$form);
$xoopsTpl->assign('apply',_MD_EDITPICS_APPLY);
user_save_profile();
$xoopsTpl->assign('gallery', $xoopsModule->getVar('name'));
include_once "include/theme_func.php";
main_menu();
//$xoopsTpl->assign('xcgal_footer', pagefooter());
do_footer();
include_once "../../footer.php";
?>