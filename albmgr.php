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

if (!(GALLERY_ADMIN_MODE || USER_ADMIN_MODE)) redirect_header('index.php',2,_MD_ACCESS_DENIED);

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
$xoopsOption['template_main'] = 'xcgal_albmgr.html';
include ICMS_ROOT_PATH."/header.php";
$xoopsTpl->assign('xoops_module_header', $xcgal_module_header);
$xoopsTpl->assign('alb_need_name', _MD_ALBMGR_NEED_NAME);
$xoopsTpl->assign('confirm_modifs', _MD_ALBMGR_CONF_MOD);
$xoopsTpl->assign('no_change', _MD_ALBMGR_NO_CHANGE);
$xoopsTpl->assign('new_album', _MD_ALBMGR_NEW_ALB);
$xoopsTpl->assign('confirm_delete1', _MD_ALBMGR_CONF_DEL1);
$xoopsTpl->assign('confirm_delete2', _MD_ALBMGR_CONF_DEL2);
$xoopsTpl->assign('select_first', _MD_ALBMGR_SELECT_FIRST);
$xoopsTpl->assign('alb_mrg', _MD_ALBMGR_ALB_MGR);

        $cat = isset($_GET['cat']) ? ($_GET['cat']) : 0;
        if ($cat == 1) $cat = 0;

        if (GALLERY_ADMIN_MODE) {
                $result = $xoopsDB->query("SELECT aid, title FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE category = $cat ORDER BY pos ASC");
        } elseif (USER_ADMIN_MODE) {
                $result = $xoopsDB->query("SELECT aid, title FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE category = ".(USER_ID + FIRST_USER_CAT)." ORDER BY pos ASC");
        } else redirect_header('index.php',2, _MD_PERM_DENIED);
        $rowset = db_fetch_rowset($result);
        $i=100;
        $sort_order = '';
        if (count ($rowset) > 0) foreach ($rowset as $album){
                $sort_order .= $album['aid'].'@'.($i++).',';
        }
        $xoopsTpl->assign('sort_order', $sort_order);

if (GALLERY_ADMIN_MODE) {

$CAT_LIST = array();
$CAT_LIST[] = array(FIRST_USER_CAT + USER_ID, _MD_ALBMGR_MY_GAL);
$CAT_LIST[] = array(0, _MD_ALBMGR_NO_CAT);
get_subcat_data(0,'');

$xoopsTpl->assign('select_category', _MD_ALBMGR_SELECT);
$options='';
foreach($CAT_LIST as $category){
        $options.= '<option value="'.$category[0].'"'.($cat == $category[0] ? ' selected="selected"': '').">".$category[1]." </option>\n";
}
$xoopsTpl->assign('options', $options);
$xoopsTpl->assign('admin_mo', 1);
} else $xoopsTpl->assign('admin_mo', 0);
$xoopsTpl->assign('size', min(max(count ($rowset)+3,15), 40));

        $i=100;
        $lb = '';
        if (count ($rowset) > 0) foreach ($rowset as $album){
                        $lb .= '                                        <option value="album_no=' . $album['aid'] .',album_nm=\'' . $album['title'] .'\',album_sort=' .($i++). ',action=0">' . stripslashes($album['title']) . "</option>\n";
        }
$xoopsTpl->assign('lb', $lb);
$xoopsTpl->assign('delete', _MD_ALBMGR_DEL);
$xoopsTpl->assign('new', _MD_ALBMGR_NEW);
$xoopsTpl->assign('apply_modifs', _MD_ALBMGR_APPLY);
user_save_profile();
$xoopsTpl->assign('gallery', $xoopsModule->getVar('name'));
include_once "include/theme_func.php";
main_menu();
//$xoopsTpl->assign('xcgal_footer', pagefooter());
do_footer();
include_once "../../footer.php";

?>
