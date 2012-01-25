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


require('include/init.inc.php');
$myts =& MyTextSanitizer::getInstance(); // MyTextSanitizer object
/**************************************************************************
 * Local functions definition
 **************************************************************************/

$header_printed = false;
$need_caption = false;

function output_table_header()
{
        global $header_printed, $need_caption;

        $header_printed = true;
        $need_caption = true;

}

function output_caption()
{
?>
<tr><td colspan="6" class="even">&nbsp;</td></tr>
<tr><td colspan="6" class="odd"><b><?php echo _MD_DEL_CAPTION ?></b></tr>
<tr><td colspan="6" class="even">
<table cellpadding="1" cellspacing="0">
<tr><td><b>F</b></td><td>:</td><td><?php echo _MD_DEL_FS_PIC ?></td><td width="20">&nbsp;</td><td><img src="images/green.gif" border="0" width="12" height="12" align="middle" alt="" /></td><td>:</td><td><?php echo _MD_DEL_DEL_SUCCESS ?></td></tr>
<tr><td><b>N</b></td><td>:</td><td><?php echo _MD_DEL_NS_PIC ?></td><td width="20">&nbsp;</td><td><img src="images/red.gif" border="0" width="12" height="12" align="middle" alt="" /></td><td>:</td><td><?php echo _MD_DEL_ERR_DEL ?></td></tr>
<tr><td><b>T</b></td><td>:</td><td><?php echo _MD_DEL_THUMB ?></td></tr>
<tr><td><b>C</b></td><td>:</td><td><?php echo _MD_DEL_COMMENT ?></td></tr>
<tr><td><b>D</b></td><td>:</td><td><?php echo _MD_DEL_IMGALB ?></td></tr>
</table>
</td>
</tr>
<?php
}

function delete_picture($pid)
{
        global $xoopsModuleConfig, $header_printed, $xoopsDB;
    global $del_pic, $xoopsModule;
        if (!$header_printed)
                output_table_header();
    $myts =& MyTextSanitizer::getInstance(); // MyTextSanitizer object
        $green = "<img src=\"images/green.gif\" border=\"0\" width=\"12\" height=\"12\" alt=\"\" /><br />";
        $red = "<img src=\"images/red.gif\" border=\"0\" width=\"12\" height=\"12\" alt=\"\" /><br />";

        if (USER_IS_ADMIN) {
                $query = "SELECT aid, filepath, filename FROM ".$xoopsDB->prefix("xcgal_pictures")." WHERE pid='$pid'";
                $result = $xoopsDB->query($query);
                if (!$xoopsDB->getRowsNum($result)) redirect_header('index.php',2,_MD_NON_EXIST_AP);
                $pic = $xoopsDB->fetchArray($result);
        } else {
                $query = "SELECT ".$xoopsDB->prefix("xcgal_pictures").".aid as aid, category, filepath, filename FROM ".$xoopsDB->prefix("xcgal_pictures").", ".$xoopsDB->prefix("xcgal_albums")." WHERE ".$xoopsDB->prefix("xcgal_pictures").".aid = ".$xoopsDB->prefix("xcgal_albums").".aid AND pid='$pid'";
                $result = $xoopsDB->query($query);
                if (!$xoopsDB->getRowsNum($result)) redirect_header('index.php',2,_MD_NON_EXIST_AP);
                $pic = $xoopsDB->fetchArray($result);
                if ($pic['category'] != FIRST_USER_CAT + USER_ID) redirect_header('index.php',2,_MD_PERM_DENIED);
        }

        $aid = $pic['aid'];
        $dir=$xoopsModuleConfig['fullpath'].$pic['filepath'];
        $file=$pic['filename'];

        if (!is_writable($dir)) redirect_header('index.php',2,sprintf(_MD_DIRECTORY_RO, htmlspecialchars($dir)));

        $del_pic= "<tr><td class=\"even\">".icms_core_DataFilter::htmlSpecialchars($file)."</td>";

        $files=array($dir.$file, $dir.$xoopsModuleConfig['normal_pfx'].$file, $dir.$xoopsModuleConfig['thumb_pfx'].$file);
    foreach ($files as $currFile){
                $del_pic.= "<td class=\"even\" align=\"center\">";
                if (is_file($currFile)){
                        if(@unlink($currFile))
                                $del_pic.= $green;
                        else
                                $del_pic.= $red;
                } else
                        $del_pic.= "&nbsp;";
                $del_pic.= "</td>";
        }

        $deleted=xoops_comment_delete($xoopsModule->getVar('mid'), $pid);
        $del_pic.= "<td class=\"even\" align=\"center\">";
        if($deleted)
                $del_pic.= $green;
        else
                $del_pic.= "&nbsp;";
        $del_pic.= "</td>";

        $query = "DELETE FROM ".$xoopsDB->prefix("xcgal_pictures")." WHERE pid='$pid' LIMIT 1";
        $result = $xoopsDB->queryf($query);
        $del_pic.= "<td class=\"even\" align=\"center\">";
        if($xoopsDB->getAffectedRows() > 0)
                $del_pic.= $green;
        else
                $del_pic.= $red;
        $del_pic.= "</td>";

        $del_pic.= "</tr>\n";

        return $aid;
}

function delete_album($aid)
{
        global $xoopsDB;
    global $del_message,$del_pic,$pic_del;
        $query = "SELECT title, category FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE aid ='$aid'";
        $result = $xoopsDB->query($query);
        if (!$xoopsDB->getRowsNum($result)) redirect_header('index.php',2,_MD_NON_EXIST_AP);
        $album_data = $xoopsDB->fetchArray($result);

        if (!GALLERY_ADMIN_MODE) {
                if ($album_data['category'] != FIRST_USER_CAT + USER_ID) redirect_header('index.php',2,_MD_PERM_DENIED);
        }

        $query = "SELECT pid FROM ".$xoopsDB->prefix("xcgal_pictures")." WHERE aid='$aid'";
        $result = $xoopsDB->query($query);

        // Delete all files
        $pic_del='';
        while($pic = $xoopsDB->fetchArray($result))
        {
                delete_picture($pic['pid']);
                $pic_del.=$del_pic;
                $del_pic='';
        }

        // Delete album
        $query = "DELETE from ".$xoopsDB->prefix("xcgal_albums")." WHERE aid='$aid'";
        $result = $xoopsDB->queryf($query);
        if($xoopsDB->getAffectedRows() > 0){
        $del_message=sprintf(_MD_DEL_ALB_DEL_SUC, $album_data['title']);
        }
        else $del_message ='';
}

/**************************************************************************
 * Album manager functions
 **************************************************************************/

function parse_select_option($value)
{
        global $HTML_SUBST, $myts;

        if (!preg_match("/.+?no=(\d+),album_nm='(.+?)',album_sort=(\d+),action=(\d)/", $value, $matches))
                return false;

        return array(
                'album_no'   => (int)$matches[1],
                'album_nm'   => icms_core_DataFilter::htmlSpecialchars($matches[2]),
                'album_sort' => (int)$matches[3],
                'action'     => (int)$matches[4]
        );
}

function parse_orig_sort_order($value)
{
        if (!preg_match("/(\d+)@(\d+)/", $value, $matches))
                return false;

        return array(
                'aid'   => (int)$matches[1],
                'pos'   => (int)$matches[2],
        );
}


function parse_list($value)
{
        return preg_split("/,/", $value, -1, PREG_SPLIT_NO_EMPTY);
}

/**************************************************************************
 * Main code starts here
 **************************************************************************/

if (!isset($_GET['what']) && !isset($_POST['what']) && !isset($_POST['picture'])) {
    redirect_header('index.php',2,_MD_PARAM_MISSING);
}

if (!isset($_GET['what']) && !isset($_POST['what']) && isset($_POST['picture'])) {
    $what='picture';
} else {
$what = isset($_GET['what']) ? $_GET['what'] : $_POST['what'];
}
switch ($what){
//
// Album manager (don't necessarily delete something ;-)
//
        case 'albmgr':
                if (!(GALLERY_ADMIN_MODE || USER_ADMIN_MODE)) redirect_header('index.php',2,_MD_ACCESS_DENIED);

                if(!GALLERY_ADMIN_MODE){
                        $restrict = "AND category = '".(FIRST_USER_CAT + USER_ID)."'";
                } else {
                        $restrict = '';
                }
        $out_caption= _MD_DEL_ALBMGR;
                $orig_sort_order = parse_list($_POST['sort_order']);
                foreach ($orig_sort_order as $album){
                        $op = parse_orig_sort_order($album);
                        if (count ($op) == 2){
                                $query = "UPDATE ".$xoopsDB->prefix("xcgal_albums")." SET pos='{$op['pos']}' WHERE aid='{$op['aid']}' $restrict LIMIT 1";
                                $xoopsDB->query($query);
                        } else {
                                redirect_header('index.php',2, sprintf(_MD_DEL_INVALID, $_POST['sort_order']));
                        }
                }

                $to_delete = parse_list($_POST['delete_album']);
                $data = array();
                foreach ($to_delete as $album_id){
                        delete_album((int)$album_id);
                        $data[]= array('del_message' => $del_message,'pic_del'=>$pic_del);
                }
        $create_update ='';
                if (isset($_POST['to'])) foreach ($_POST['to'] as $option_value){
                        $op = parse_select_option(stripslashes($option_value));
                        switch ($op['action']){
                                case '0':
                                        break;
                                case '1':
                                        if(GALLERY_ADMIN_MODE){
                                                $category = (int)$_POST['cat'];
                                        } else {
                                                $category = FIRST_USER_CAT + USER_ID;
                                        }
                                        $create_update = sprintf(_MD_DEL_CREATE, $op['album_nm']);
                                        $query = "INSERT INTO ".$xoopsDB->prefix("xcgal_albums")." (category, title, uploads, pos, description) VALUES ('$category', '".addslashes($op['album_nm'])."', 'NO',  '{$op['album_sort']}', '')";
                                        $xoopsDB->queryf($query);
                                        break;
                                case '2':
                                        $create_update = sprintf(_MD_DEL_UPDATE, $op['album_no'], $op['album_nm'], $op['album_sort']);
                                        $query = "UPDATE ".$xoopsDB->prefix("xcgal_albums")." SET title='".addslashes($op['album_nm'])."', pos='{$op['album_sort']}' WHERE aid='{$op['album_no']}' $restrict LIMIT 1";
                                        $xoopsDB->queryf($query);
                                        break;
                                default:
                                        redirect_header('index.php',2,_MD_DEL_INVALID);
                        }
                }
        $continueURL = 'index.php';

                break;
//
// Picture
//
        case 'picture':
                if (!(USER_IS_ADMIN || USER_ADMIN_MODE)) redirect_header('index.php',2,_MD_ACCESS_DENIED);
                $pid = (int)$_POST['id'];
        $out_caption = _MD_DEL_DELPIC;
                $aid = delete_picture($pid);
                $data = array();
                $data[]= array('del_message' => '','pic_del'=>$del_pic);
                $continueURL = "thumbnails.php?album={$aid}";
                $create_update='';
                break;
//
// Album
//
        case 'album':
                if (!(GALLERY_ADMIN_MODE || USER_ADMIN_MODE)) redirect_header('index.php',2,_MD_ACCESS_DENIED);

                $aid = (int)$_GET['id'];
        $out_caption= _MD_DEL_DELALB;
        delete_album($aid);
        $data = array();
                $data[]= array('del_message' => $del_message,'pic_del'=>$pic_del);
        $continueURL ='index.php';
        $create_update='';
                break;
//
// Unknow command
//
        default:
                redirect_header('index.php',2,_MD_PARAM_MISSING);

}
if ($out_caption){
    $xoopsOption['template_main'] = 'xcgal_delete.html';
    include ICMS_ROOT_PATH."/header.php";
    $xoopsTpl->assign('xoops_module_header', $xcgal_module_header);
    $xoopsTpl->assign('table_header', $out_caption);
    //alb pic
    foreach ($data as $dels){
                        $xoopsTpl->append('deletes', array('del_message' => $dels['del_message'],'pic_del'=>$dels['pic_del']));
                }
    if ($header_printed){
            $xoopsTpl->assign('lang_pictures', _MD_PICS);
            }
    $xoopsTpl->assign('create_update', $create_update);
    if ($need_caption){
       //output_caption();
       $xoopsTpl->assign('need_caption', 1);
       $xoopsTpl->assign('lang_caption', _MD_DEL_CAPTION);
       $xoopsTpl->assign('lang_fs_pic', _MD_DEL_FS_PIC);
       $xoopsTpl->assign('lang_del_success', _MD_DEL_DEL_SUCCESS);
       $xoopsTpl->assign('lang_ns_pic', _MD_DEL_NS_PIC);
       $xoopsTpl->assign('lang_err_del', _MD_DEL_ERR_DEL);
       $xoopsTpl->assign('lang_thumb_pic', _MD_DEL_THUMB);
       $xoopsTpl->assign('lang_comment', _MD_DEL_COMMENT);
       $xoopsTpl->assign('lang_im_in_alb', _MD_DEL_IMGALB);
       }
    $xoopsTpl->assign('continue_url', $continueURL);
        $xoopsTpl->assign('lang_continue', _MD_CONTINUE);
    user_save_profile();
    $xoopsTpl->assign('gallery', $xoopsModule->getVar('name'));
    include_once "include/theme_func.php";
    main_menu();
        //$xoopsTpl->assign('xcgal_footer', pagefooter());
        do_footer();
    include ICMS_ROOT_PATH."/footer.php";

}
?>