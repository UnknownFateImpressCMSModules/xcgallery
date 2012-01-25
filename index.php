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

function html_albummenu($id)
{
        $amenu['conf_del'] = _MD_INDEX_CONF_DEL;
        $amenu['delete']= _MD_INDEX_DEL;
        $amenu['modify']= _MD_INDEX_MOD;
        $amenu['edit_pics']= _MD_INDEX_EDIT;


        return $amenu;
}

function get_subcat_data($parent, &$cat_data, &$album_set_array, $level, $ident='')
{
    global $xoopsModuleConfig, $HIDE_USER_CAT, $xoopsDB, $myts;

        $sql = $xoopsDB->query("SELECT cid, name, description FROM ".$xoopsDB->prefix("xcgal_categories")." WHERE parent = '$parent'  ORDER BY pos");

        if ($xoopsDB->getRowsNum($sql) > 0){
                //$rowset = db_fetch_rowset($result);
                while($subcat = $xoopsDB->fetchArray($sql)) {

                        if ($subcat['cid'] == USER_GAL_CAT) {
                                $result= $xoopsDB->query("SELECT aid FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE category >= ".FIRST_USER_CAT);
                                $album_count = $xoopsDB->getRowsNum($result);
                                while($row = $xoopsDB->fetchArray($result)){
                                        $album_set_array[] = $row['aid'];
                                } // while
                                $xoopsDB->freeRecordSet($result);

                                $result= $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xcgal_pictures").", ".$xoopsDB->prefix("xcgal_albums")." WHERE ".$xoopsDB->prefix("xcgal_pictures").".aid = ".$xoopsDB->prefix("xcgal_albums").".aid AND category >= ".FIRST_USER_CAT);
                                $pic_count = $xoopsDB->getRowsNum($result);
                $xoopsDB->freeRecordSet($result);
                                $subcat['description'] = preg_replace("/<br \/>/i",'<br />'.$ident , $myts->makeTareaData4Show($subcat['description']));
                                $link = $ident."<a href=index.php?cat={$subcat['cid']}>".icms_core_DataFilter::htmlSpecialchars($subcat['name'])."</a>";
                                if($album_count){
                                        $cat_data[]=array($link, $ident.$subcat['description'], $album_count, $pic_count);
                                        $HIDE_USER_CAT = 0;
                                } else {
                                        $HIDE_USER_CAT = 1;
                                }

                        } else {
                                $result= $xoopsDB->query("SELECT aid FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE category = {$subcat['cid']}");
                                $album_count = $xoopsDB->getRowsNum($result);
                                while($row = $xoopsDB->fetchArray($result)){
                                        $album_set_array[] = $row['aid'];
                                } // while
                                $xoopsDB->freeRecordSet($result);

                                $result= $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xcgal_pictures").", ".$xoopsDB->prefix("xcgal_albums")." WHERE ".$xoopsDB->prefix("xcgal_pictures").".approved='YES' AND ".$xoopsDB->prefix("xcgal_pictures").".aid = ".$xoopsDB->prefix("xcgal_albums").".aid AND category = {$subcat['cid']}");
                                $pic_count = $xoopsDB->getRowsNum($result);
                $xoopsDB->freeRecordSet($result);

                                $subcat['name'] = icms_core_DataFilter::htmlSpecialchars($subcat['name']);
                                $subcat['description'] = preg_replace("/<br \/>/i",'<br />'.$ident ,$myts->makeTareaData4Show($subcat['description']));
                                $link = $ident."<a href=\"index.php?cat={$subcat['cid']}\">{$subcat['name']}</a>";
                                if($pic_count == 0 && $album_count ==0 ){
                                        $cat_data[]=array($link, $ident.$subcat['description']);
                                } else {
                                        $cat_data[]=array($link, $ident.$subcat['description'], $album_count, $pic_count);
                                }
                        }

                        if ($level > 1) get_subcat_data($subcat['cid'], $cat_data, $album_set_array, $level -1, $ident."<img src=\"images/spacer.gif\" width=\"20\" height=\"1\" alt=\"\" />");
                }
        }
}

// List all categories
function get_cat_list(&$breadcrumb, &$cat_data, &$statistics)
{
        global $_GET, $xoopsModuleConfig, $ALBUM_SET, $CURRENT_CAT_NAME, $BREADCRUMB_TEXT, $STATS_IN_ALB_LIST;
        global $HIDE_USER_CAT;
        global $xoopsDB, $xoopsModule;

        $cat = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;

        // Build the breadcrumb
        breadcrumb($cat, $breadcrumb, $BREADCRUMB_TEXT);

        // Build the category list
        $cat_data = array();
        $album_set_array = array();
        get_subcat_data($cat, $cat_data, $album_set_array, $xoopsModuleConfig['subcat_level']);

        // Treat the album set
        if ($cat) {
                if ($cat == USER_GAL_CAT) {
                        $result=$xoopsDB->query("SELECT aid FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE category >= ".FIRST_USER_CAT);
                } else {
                        $result=$xoopsDB->query("SELECT aid FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE category = '$cat'");
                }
                while($row = $xoopsDB->fetchArray($result)){
                        $album_set_array[] = $row['aid'];
                } // while
                $xoopsDB->freeRecordSet($result);
        }
        if (count($album_set_array) && $cat) {
                $set ='';
            foreach ($album_set_array as $album) $set .= $album.',';
                $set = substr($set, 0, -1);
                $current_album_set = "AND aid IN ($set) ";
                $ALBUM_SET .= $current_album_set;
        } elseif ($cat) {
                $current_album_set = "AND aid IN (-1) ";
                $ALBUM_SET .= $current_album_set;
        }

        // Gather gallery statistics
        if ($cat == 0) {
                $result=$xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE 1");
                $album_count = $xoopsDB->getRowsNum($result);
                $xoopsDB->freeRecordSet($result);

                $result=$xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xcgal_pictures")." WHERE 1 AND approved='YES'");
                $picture_count = $xoopsDB->getRowsNum($result);
                $xoopsDB->freeRecordSet($result);

                $comment_count = xoops_comment_count($xoopsModule->mid());

                $result=$xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xcgal_categories")." WHERE 1");
                $cat_count = $xoopsDB->getRowsNum($result) - $HIDE_USER_CAT;
                $xoopsDB->freeRecordSet($result);

                $result=$xoopsDB->query("SELECT sum(hits) FROM ".$xoopsDB->prefix("xcgal_pictures")." WHERE 1");
                $nbEnr = $xoopsDB->fetchArray($result);
                $hit_count = (int)$nbEnr['sum(hits)'];
                $xoopsDB->freeRecordSet($result);

                if (count($cat_data)) {
                        $statistics = strtr(_MD_INDEX_STAT1, array(
                                '[pictures]' => $picture_count,
                                '[albums]' => $album_count,
                                '[cat]' => $cat_count,
                                '[comments]' => $comment_count,
                                '[views]' => $hit_count));
                } else {
                        $STATS_IN_ALB_LIST = true;
                        $statistics = strtr(_MD_INDEX_STAT3, array(
                                '[pictures]' => $picture_count,
                                '[albums]' => $album_count,
                                '[comments]' => $comment_count,
                                '[views]' => $hit_count));
                }

        } elseif ($cat >= FIRST_USER_CAT && $ALBUM_SET) {
                $result=$xoopsDB->query("SELECT count(*) FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE 1 $current_album_set");
                $nbEnr = $xoopsDB->fetchArray($result);
                $album_count = $nbEnr['count(*)'];
                $xoopsDB->freeRecordSet($result);

                $result=$xoopsDB->query("SELECT count(*) FROM ".$xoopsDB->prefix("xcgal_pictures")." WHERE 1 $current_album_set");
                $nbEnr = $xoopsDB->fetchArray($result);
                $picture_count = $nbEnr['count(*)'];
                $xoopsDB->freeRecordSet($result);

                $result=$xoopsDB->query("SELECT sum(hits) FROM ".$xoopsDB->prefix("xcgal_pictures")." WHERE 1 $current_album_set");
                $nbEnr = $xoopsDB->fetchArray($result);
                $hit_count = (int)$nbEnr['sum(hits)'];
                $xoopsDB->freeRecordSet($result);

                $statistics = strtr(_MD_INDEX_STAT2, array(
                                '[pictures]' => $picture_count,
                                '[albums]' => $album_count,
                                '[views]' => $hit_count));

        } else {
                $statistics = '';
        }

}

function list_users()
{
        global $xoopsModuleConfig, $PAGE, $FORBIDDEN_SET, $xoopsDB, $myts;


        $sql =  "SELECT category, COUNT(DISTINCT a.aid) as alb_count,".
                        "                COUNT(DISTINCT pid) as pic_count,".
                        "                MAX(pid) as thumb_pid ".
                        "FROM ".$xoopsDB->prefix("xcgal_albums")." AS a, ".
                        "".$xoopsDB->prefix("xcgal_pictures")." AS p WHERE category > ".FIRST_USER_CAT." AND p.aid = a.aid ".
                        "AND approved = 'YES' ".
                        "$FORBIDDEN_SET ".
                        "GROUP BY category ".
                        "ORDER BY category ";
        $result=$xoopsDB->query($sql);

        $user_count = $xoopsDB->getRowsNum($result);
        if (!$user_count) {
                   redirect_header('index.php',2,_MD_INDEX_NO_UGAL);

        return;
        }
        $rowset = db_fetch_rowset($result);
        $xoopsDB->freeRecordSet($result);

        $user_per_page = $xoopsModuleConfig['thumbcols'] * $xoopsModuleConfig['thumbrows'];
        $totalPages = ceil($user_count / $user_per_page);
        if ($PAGE > $totalPages) $PAGE = 1;
        $lower_limit = ($PAGE-1) * $user_per_page;
        $upper_limit = min($user_count, $PAGE * $user_per_page);
        $limit = "LIMIT ". $lower_limit . "," . ($upper_limit-$lower_limit);

        $user_list = array();
        for ($i = $lower_limit; $i < $upper_limit; $i++){
                $user = &$rowset[$i];
                $user_thumb = '<img src="images/nopic.jpg" class="image" border="0" alt=""/>';
                $user_pic_count   = $user['pic_count'];
                $user_thumb_pid   = $user['thumb_pid'];
                $user_album_count = $user['alb_count'];

                if ($user_pic_count) {
                        $sql =  "SELECT filepath, filename, url_prefix, pwidth, pheight ".
                                        "FROM ".$xoopsDB->prefix("xcgal_pictures")." ".
                                        "WHERE pid='$user_thumb_pid'";
                        $result = $xoopsDB->query($sql);
                        if ($xoopsDB->getRowsNum($result)) {
                                $picture = $xoopsDB->fetchArray($result);
                                $xoopsDB->freeRecordSet($result);

                                $image_size = compute_img_size($picture['pwidth'], $picture['pheight'], $xoopsModuleConfig['thumb_width']);
                                $user_thumb = "<img src=\"" .get_pic_url($picture, 'thumb')."\" {$image_size['geom']} alt=\"\" border=\"0\" class=\"image\" />";
                         }
                }

                $albums_txt = sprintf(_MD_INDEX_NALBS, $user_album_count);
                $pictures_txt = sprintf(_MD_NPICS, $user_pic_count);
                $user_handler = icms::handler('icms_member');
                $alb_owner =& $user_handler->getUser($user['category']-FIRST_USER_CAT);
        if (is_object ($alb_owner)){
        $caption['u_name']= $alb_owner->uname();
        $caption['u_id']= $alb_owner->uid();
        $caption['albums']=$albums_txt;
        $caption['pictures']=$pictures_txt;
                $user_list[]=array(
                        'cat' => $user['category'],
                        'image' => $user_thumb,
                        'caption' => $caption,
                );
        }   }
        theme_display_thumbnails($user_list, $user_count, '', '', 1, $PAGE, $totalPages, false, true, 'user');
}


// List all albums
function list_albums()
{
        global $xoopsModuleConfig, $USER, $PAGE, $lastup_date_fmt, $_GET, $USER_DATA;
        global $xoopsDB;
    $myts =& MyTextSanitizer::getInstance(); // MyTextSanitizer object
        $cat = isset($_GET['cat']) ? $_GET['cat'] : 0;

        $alb_per_page = $xoopsModuleConfig['albums_per_page'];
        $maxTab = $xoopsModuleConfig['max_tabs'];

        $result = $xoopsDB->query("SELECT count(*) FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE category = '$cat'");
        $nbEnr = $xoopsDB->fetchArray($result);
        $nbAlb = $nbEnr['count(*)'];
        $xoopsDB->freeRecordSet($result);

        if ($nbAlb == 0) {
                return;
        }

        $totalPages = ceil($nbAlb / $alb_per_page);

        if ($PAGE > $totalPages) $PAGE = 1;
        $lower_limit = ($PAGE-1) * $alb_per_page;
        $upper_limit = min($nbAlb, $PAGE * $alb_per_page);
        $limit = "LIMIT ". $lower_limit . "," . ($upper_limit-$lower_limit);

        $sql =  "SELECT a.aid, a.title, description, visibility, filepath, ".
                        "                filename, url_prefix, pwidth, pheight ".
                        "FROM ".$xoopsDB->prefix("xcgal_albums")." as a ".
                        "LEFT JOIN ".$xoopsDB->prefix("xcgal_pictures")." as p ON thumb=pid ".
                        "WHERE category = '$cat' ORDER BY pos ".
                        "$limit";
        $alb_thumbs_q = $xoopsDB->query($sql);
        $alb_thumbs = db_fetch_rowset($alb_thumbs_q);
        $xoopsDB->freeRecordSet($alb_thumbs_q);

        $disp_album_count = count($alb_thumbs);
        $album_set = '';
        foreach($alb_thumbs as $value){
                $album_set .= $value['aid'].', ';
        }
        $album_set = '('.substr($album_set,0, -2).')';

        $sql =  "SELECT aid, count(pid) as pic_count, max(pid) as last_pid, max(ctime) as last_upload ".
                        "FROM ".$xoopsDB->prefix("xcgal_pictures")." ".
                        "WHERE aid IN $album_set AND approved = 'YES' ".
                        "GROUP BY aid";
        $alb_stats_q = $xoopsDB->query($sql);
        $alb_stats = db_fetch_rowset($alb_stats_q);
        $xoopsDB->freeRecordSet($alb_stats_q);

        foreach($alb_stats as $key => $value){
                $cross_ref[$value['aid']] = &$alb_stats[$key];
        }


        for ($alb_idx=0; $alb_idx < $disp_album_count; $alb_idx++) {

                $alb_thumb = &$alb_thumbs[$alb_idx];
                $aid = $alb_thumb['aid'];

                if (isset($cross_ref[$aid])) {
                        $alb_stat = $cross_ref[$aid];
                        $count = $alb_stat['pic_count'];
                } else {
                        $alb_stat = array();
                        $count = 0;
                }

                // Inserts a thumbnail if the album contains 1 or more images
                if ($count > 0) {
                        $visibility = $alb_thumb['visibility'];
                        if ($visibility == '0' || $visibility == (FIRST_USER_CAT + USER_ID) || (is_array($USER_DATA['group_id']) && in_array($visibility, $USER_DATA['group_id']))) {
                                if ($alb_thumb['filename']) {
                                    $picture = &$alb_thumb;
                                } else {
                                        $sql =  "SELECT filepath, filename, url_prefix, pwidth, pheight ".
                                                        "FROM ".$xoopsDB->prefix("xcgal_pictures")." ".
                                                        "WHERE pid='{$alb_stat['last_pid']}'";
                                        $result = $xoopsDB->query($sql);
                                        $picture = $xoopsDB->fetchArray($result);
                                        $xoopsDB->freeRecordSet($result);
                                }
                                $image_size = compute_img_size($picture['pwidth'], $picture['pheight'], $xoopsModuleConfig['alb_list_thumb_size']);
                                $alb_list[$alb_idx]['thumb_pic'] = "<img src=\"" . get_pic_url($picture, 'thumb') ."\" {$image_size['geom']} alt=\"\" border=\"0\" class=\"image\" />";
                        } else {
                                $image_size = compute_img_size(100, 75, $xoopsModuleConfig['alb_list_thumb_size']);
                                $alb_list[$alb_idx]['thumb_pic'] = "<img src=\"images/private.jpg\" {$image_size['geom']} alt=\"\" border=\"0\" class=\"image\" />";
                        }
                } else {
                        $image_size = compute_img_size(100, 75, $xoopsModuleConfig['alb_list_thumb_size']);
                        $alb_list[$alb_idx]['thumb_pic'] = "<img src=\"images/nopic.jpg\" {$image_size['geom']} alt=\"\" border=\"0\" class=\"image\" />";
                }

                // Prepare everything
                $last_upload_date = $count ? formatTimestamp($alb_stat['last_upload'],'m') : '';
                $alb_list[$alb_idx]['aid']            = $alb_thumb['aid'];
                $alb_list[$alb_idx]['album_title']    = icms_core_DataFilter::htmlSpecialchars($alb_thumb['title']);
                $alb_list[$alb_idx]['album_desc']     = $myts->makeTareaData4Show($alb_thumb['description'],1);
                $alb_list[$alb_idx]['pic_count']      = $count;
                $alb_list[$alb_idx]['last_upl']       = $last_upload_date;
#                $alb_list[$alb_idx]['album_info']     = sprintf(_MD_NPICS, $count).($count ? sprintf(_MD_INDEX_LASTADD, $last_upload_date) : "" );
                $alb_list[$alb_idx]['album_info']     = sprintf(_MD_NPICS, $count);

                $alb_list[$alb_idx]['album_adm_menu'] = (GALLERY_ADMIN_MODE || (USER_ADMIN_MODE && $cat == USER_ID + FIRST_USER_CAT)) ? html_albummenu($alb_thumb['aid']) : '';
        }

    theme_display_album_list($alb_list, $nbAlb, $cat, $PAGE, $totalPages);
}

/**************************************************************************
 * Main code
 **************************************************************************/

if (isset($_GET['page'])){
        $PAGE = max((int)$_GET['page'], 1);
        $USER['lap'] = $PAGE;
} elseif (isset($USER['lap'])){
        $PAGE = max((int)$USER['lap'],1);
} else {
        $PAGE = 1;
}

if (isset($_GET['cat'])) {
    $cat = (int)$_GET['cat'];
}
else $cat='';

// Gather data for categories
if (!GALLERY_ADMIN_MODE && $xoopsModuleConfig['allow_private_albums']) get_private_album_set();
$breadcrumb = '';
$cat_data = array();
$statistics = '';
$STATS_IN_ALB_LIST = false;
get_cat_list($breadcrumb, $cat_data, $statistics );
user_save_profile();
include_once "include/theme_func.php";
$xoopsOption['template_main'] = 'xcgal_index.html';
include ICMS_ROOT_PATH."/header.php";
$xoopsTpl->assign('xoops_module_header', $xcgal_module_header);
$xoopsTpl->assign('display_alb_list','');

$elements = preg_split("|/|", $xoopsModuleConfig['main_page_layout'], -1, PREG_SPLIT_NO_EMPTY);
foreach ($elements as $element){
        if (preg_match("/(\w+),*(\d+)*/", $element, $matches))        switch($matches[1]){
                case 'catlist':
                if ($breadcrumb != '' || count($cat_data) > 0) theme_display_cat_list($breadcrumb, $cat_data, $statistics);
                if (isset($cat) && $cat == USER_GAL_CAT) list_users();
                break;

                case 'alblist':
                   list_albums();
                break;

                case 'random':
                   display_thumbnails('random', $cat, 1, $xoopsModuleConfig['thumbcols'], max(1,$matches[2]), false);
                break;
        case 'lasthits':
                   display_thumbnails('lasthits', $cat, 1, $xoopsModuleConfig['thumbcols'], max(1,$matches[2]), false);
                break;

                case 'lastup':
                   display_thumbnails('lastup', $cat, 1, $xoopsModuleConfig['thumbcols'], max(1,$matches[2]), false);
                break;

                case 'topn':
                   display_thumbnails('topn', $cat, 1, $xoopsModuleConfig['thumbcols'], max(1,$matches[2]), false);
                break;

                case 'toprated':
                   display_thumbnails('toprated', $cat, 1, $xoopsModuleConfig['thumbcols'], max(1,$matches[2]), false);
                break;

                case 'lastcom':
                   display_thumbnails('lastcom', $cat, 1, $xoopsModuleConfig['thumbcols'], max(1,$matches[2]), false);
                break;
                case 'mostsend':
                   display_thumbnails('mostsend', $cat, 1, $xoopsModuleConfig['thumbcols'], max(1,$matches[2]), false);
                break;
        }
}

//$xoopsTpl->assign('xcgal_main', $temp);
$xoopsTpl->assign('gallery', $xoopsModule->getVar('name'));
main_menu();
        //$xoopsTpl->assign('xcgal_footer', pagefooter());
do_footer();
if ($cat <> 0){
$xoopsTpl->assign('xoops_pagetitle', icms_core_DataFilter::htmlSpecialchars($CURRENT_CAT_NAME) . ' : ' .icms_core_DataFilter::htmlSpecialchars($xoopsModule->name()));
} else {
$xoopsTpl->assign('xoops_pagetitle', icms_core_DataFilter::htmlSpecialchars($xoopsModule->name()));
}
include_once "../../footer.php";
// Speed-up the random image query by 'keying' the image table
$result=$xoopsDB->query("SELECT count(*) FROM ".$xoopsDB->prefix("xcgal_pictures")." WHERE 1");
$nbEnr = $xoopsDB->fetchArray($result);
$xoopsDB->freeRecordSet($result);
$pic_count = $nbEnr['count(*)'];
$granularity = floor($pic_count / RANDPOS_MAX_PIC);
if ($granularity != RANDPOS_INTERVAL && $pic_count > RANDPOS_MAX_PIC) {
    $result=$xoopsDB->queryf("UPDATE ".$xoopsDB->prefix("xcgal_pictures")." SET randpos = ROUND(RAND()*$granularity) WHERE 1");
    $result=$xoopsDB->queryf("UPDATE ".$xoopsDB->prefix("xcgal_config")." SET value = '$granularity' WHERE name = 'randpos_interval'");
}

?>
