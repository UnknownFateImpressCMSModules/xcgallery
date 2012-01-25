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
include "../../mainfile.php";
define('IN_XCGALLERY', true);


include("include/init.inc.php");

if (!(GALLERY_ADMIN_MODE || USER_ADMIN_MODE)) {
    redirect_header(ICMS_URL."/", 3, _NOPERM);

}

function get_subcat_data($parent, $ident='')
{
    global $CAT_LIST, $xoopsDB;

        $result = $xoopsDB->query("SELECT cid, name, description FROM ".$xoopsDB->prefix("xcgal_categories")." WHERE parent = '$parent' AND cid != 1 ORDER BY pos");
        if ($xoopsDB->getRowsNum($result) > 0){
                $rowset = db_fetch_rowset($result);
                foreach ($rowset as $subcat){
                        $CAT_LIST[]=array($subcat['cid'], $ident.$subcat['name']);
                        get_subcat_data($subcat['cid'], $ident.'&nbsp;&nbsp;&nbsp;');
                }
        }
}

function alb_list_box()
{
        global $album, $PHP_SELF, $xoopsDB;

        if (GALLERY_ADMIN_MODE) {
                $sql = "SELECT category, aid, title ".
                           "FROM ".$xoopsDB->prefix("xcgal_albums")." ".
                           //"LEFT JOIN ".$xoopsDB->prefix("users")." AS u ON category = (".FIRST_USER_CAT." + uid) ".
                           "ORDER BY category";
                $result = $xoopsDB->query($sql);
        } else {
                $result = $xoopsDB->query("SELECT aid, title FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE category = '".(FIRST_USER_CAT + USER_ID)."' ORDER BY title");
        }
    $user_handler = icms::handler('icms_member');
        if ($xoopsDB->getRowsNum($result) > 0 ){
                $lb = "<select name=\"album_listbox\" class=\"listbox\" onChange=\"if(this.options[this.selectedIndex].value) window.location.href='$PHP_SELF?album='+this.options[this.selectedIndex].value;\" />\n";
                while ($row = $xoopsDB->fetchArray($result)) {
                    if ($row['category'] > FIRST_USER_CAT){
                        $alb_owner =& $user_handler->getUser($row['category']-FIRST_USER_CAT);
                if (is_object ($alb_owner)) $row['title']= "(".$alb_owner->uname().") ".$row['title'];
            } else $row['title']= "-".$row['title'];
                        $selected = ($row['aid'] == $album) ? "selected=\"seleceted\"" : "";
                        $lb .= "        <option value=\"" . $row['aid'] . "\" $selected> " . $row['title'] . "</option>\n";
                }
                $lb.= "</select>\n";
                return $lb;
        }
}


if (!isset($_GET['album'])) {
        if (GALLERY_ADMIN_MODE) {
            $results = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE 1 LIMIT 1");
        } else {
            $results = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE category = ".(FIRST_USER_CAT + USER_ID)." LIMIT 1");
        }
        if ($xoopsDB->getRowsNum($results) == 0) redirect_header('index.php',2,_MD_MODIFYALB_ERR_NO_ALB);
        $ALBUM_DATA = $xoopsDB->fetchArray($results);
        $album = $ALBUM_DATA['aid'];
} else {
        $album = (int)$_GET['album'];
        $results = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE aid='$album'");
        if(!$xoopsDB->getRowsNum($results)) redirect_header('index.php',2,_MD_NON_EXIST_AP);
        $ALBUM_DATA = $xoopsDB->fetchArray($results);
}

$cat = $ALBUM_DATA['category'];
$actual_cat = $cat;

if (!GALLERY_ADMIN_MODE && $ALBUM_DATA['category'] != FIRST_USER_CAT + USER_ID) {
    redirect_header('index.php',2,_MD_PERM_DENIED);
}
$xoopsOption['template_main'] = 'xcgal_modifyalb.html';
include ICMS_ROOT_PATH."/header.php";
$xoopsTpl->assign('xoops_module_header', $xcgal_module_header);
$album_lb =alb_list_box();
$xoopsTpl->assign('update', _MD_MODIFYALB_UPDATE);
$xoopsTpl->assign('album_lb', $album_lb);
$xoopsTpl->assign('album', $album);

$xoopsTpl->assign('general_settings', _MD_MODIFYALB_GEN_SET);
$xoopsTpl->assign('alb_title', _MD_MODIFYALB_ALB_TITLE);
$xoopsTpl->assign('album_data_title', $ALBUM_DATA['title']);
$xoopsTpl->assign('alb_cat', _MD_MODIFYALB_ALB_CAT);
if (!GALLERY_ADMIN_MODE || $ALBUM_DATA['category'] > FIRST_USER_CAT){
        $xoopsTpl->assign('user_galleries', 1);
        $xoopsTpl->assign('user_gal', _MD_MODIFYALB_USER_GAL);
        $xoopsTpl->assign('album_data_category', $ALBUM_DATA['category']);
        }
else {
        $CAT_LIST = array();
        $CAT_LIST[] = array(0, _MD_MODIFYALB_NO_CAT);
        get_subcat_data(0,'');
    $cat_list='';
        foreach($CAT_LIST as $category){
                $cat_list.= '<option value="'.$category[0].'"'.($ALBUM_DATA['category'] == $category[0] ? ' selected="selected"': '').">".$category[1]."</option>\n";
            }
        $xoopsTpl->assign('cat_list', $cat_list);
        $xoopsTpl->assign('user_galleries', 0);
        $xoopsTpl->assign('user_gal', '');
        $xoopsTpl->assign('album_data_category', '');
        }

$xoopsTpl->assign('alb_desc', _MD_MODIFYALB_ALB_DESC);
$xoopsTpl->assign('album_data_description', $ALBUM_DATA['description']);
$xoopsTpl->assign('alb_thumb', _MD_MODIFYALB_ALB_THUMB);
$results=$xoopsDB->query("SELECT pid, filepath, filename, url_prefix FROM ".$xoopsDB->prefix("xcgal_pictures")." WHERE aid='$album' AND approved='YES' ORDER BY filename");
if ($xoopsDB->getRowsNum($results) == 0) {
    $xoopsTpl->assign('no_pic', 1);
    $xoopsTpl->assign('alb_empty', _MD_MODIFYALB_ALB_EMPTY);
    }
else {
    $initial_thumb_url = 'images/nopic.jpg';
        $img_list=array(0 => _MD_MODIFYALB_LAST_UPL);
        $pic_url='';
        $xoopsTpl->assign('no_pic', '');
        while($picture=$xoopsDB->fetchArray($results)){
                $thumb_url = get_pic_url($picture, 'thumb');
                $pic_url.= "Pic[{$picture['pid']}] = '".$thumb_url."'\n";
                if ($picture['pid'] == $ALBUM_DATA['thumb']) $initial_thumb_url = $thumb_url;
                $img_list[$picture['pid']] = htmlspecialchars($picture['filename']);
        }
        $xoopsTpl->assign('pic_url', $pic_url);
        $xoopsTpl->assign('thumb_cell_height', $xoopsModuleConfig['thumb_width'] + 17);
        $xoopsTpl->assign('initial_thumb_url', $initial_thumb_url);
        $thumbs='';
        foreach($img_list as $pid => $pic_name){
        $thumbs.= '<option value="'.$pid.'"'.($pid == $ALBUM_DATA['thumb'] ? ' selected="selected"':'').'>'.$pic_name."</option>\n";
        }
        $xoopsTpl->assign('thumbs', $thumbs);
}
$xoopsTpl->assign('alb_perm',_MD_MODIFYALB_ALB_PERM);
$xoopsTpl->assign('can_view',_MD_MODIFYALB_CAN_VIEW);
if (!$xoopsModuleConfig['allow_private_albums']) {
        $xoopsTpl->assign('no_private',1);
        }
else{
    $xoopsTpl->assign('no_private','');
    if (GALLERY_ADMIN_MODE) {
                   $options = array(0 => _MD_MODIFYALB_PUB_ALB,FIRST_USER_CAT + USER_ID => _MD_MODIFYALB_ME_ONLY);
                if ($ALBUM_DATA['category'] > FIRST_USER_CAT) {
                    $result = $xoopsDB->query("SELECT uname FROM ".$xoopsDB->prefix("users")." WHERE uid='".($ALBUM_DATA['category'] - FIRST_USER_CAT)."'");
                        if ($xoopsDB->getRowsNum($result)) {
                            $user = $xoopsDB->fetchArray($result);
                                $options[$ALBUM_DATA['category']] = sprintf(_MD_MODIFYALB_OWNER_ONLY, $user['uname']);
                        }
                }
                $result = $xoopsDB->query("SELECT group_id, group_name FROM ".$xoopsDB->prefix("xcgal_usergroups")." WHERE 1");
                while($group = $xoopsDB->fetchArray($result)){
                        $options[$group['group_id']] = sprintf(_MD_MODIFYALB_GROUP_ONLY, $group['group_name']);
                } // while
        } else {
                $options = array(
                        0 => _MD_MODIFYALB_PUB_ALB,
                        FIRST_USER_CAT + USER_ID => _MD_MODIFYALB_ME_ONLY,
                );
                $member_handler = icms::handler('icms_member');
                $usergroups= $xoopsUser->getgroups();
                //var_dump($usergroups);
                foreach ($usergroups as $ugr){
                    $group =& $member_handler->getGroup($ugr);
                    $name=$group->getVar('name');
            $options[$ugr] = sprintf(_MD_MODIFYALB_GROUP_ONLY, $name);

            }
        }
        $view_options='';
        foreach ($options as $value => $caption){
                $view_options.= '<option value ="'.$value.'"'.($ALBUM_DATA['visibility'] == $value ? ' selected="selected"': '').'>'.$caption."</option>\n";
        }
        $xoopsTpl->assign('view_options',$view_options);
    }
$xoopsTpl->assign('can_upload_pic',_MD_MODIFYALB_CAN_UPLOAD);
if (USER_ADMIN_MODE){
        $xoopsTpl->assign('user_admin_mode',1);
        $xoopsTpl->assign('album_data_uploads',$ALBUM_DATA['uploads']);
        }
else {
    $xoopsTpl->assign('user_admin_mode','');
    $value = isset($ALBUM_DATA['uploads']) ? $ALBUM_DATA['uploads'] : false;
        $yes_selected_upload = $value=='YES' ? 'selected="selected"' : '';
        $no_selected_upload  = $value=='NO' ? 'selected="selescted"' : '';
    $xoopsTpl->assign('yes_selected_upload',$yes_selected_upload);
    $xoopsTpl->assign('no_selected_upload',$no_selected_upload);
    }
$xoopsTpl->assign('lang_yes', _YES);
$xoopsTpl->assign('lang_no', _NO);

$xoopsTpl->assign('can_post_comments', _MD_MODIFYALB_CAN_COM);
$value = isset($ALBUM_DATA['comments']) ? $ALBUM_DATA['comments'] : false;
$yes_selected_comments = $value=='YES' ? 'selected="selected"' : '';
$no_selected_comments  = $value=='NO' ? 'selected="selescted"' : '';
$xoopsTpl->assign('yes_selected_comments',$yes_selected_comments);
$xoopsTpl->assign('no_selected_comments',$no_selected_comments);

$xoopsTpl->assign('can_rate', _MD_MODIFYALB_CAN_RATE);
$value = isset($ALBUM_DATA['votes']) ? $ALBUM_DATA['votes'] : false;
$yes_selected_votes = $value=='YES' ? 'selected="selected"' : '';
$no_selected_votes  = $value=='NO' ? 'selected="selescted"' : '';
$xoopsTpl->assign('yes_selected_votes',$yes_selected_votes);
$xoopsTpl->assign('no_selected_votes',$no_selected_votes);


user_save_profile();
$xoopsTpl->assign('gallery', $xoopsModule->getVar('name'));
include_once "include/theme_func.php";
main_menu();
//$xoopsTpl->assign('xcgal_footer', pagefooter());
do_footer();
include_once "../../footer.php";
?>
