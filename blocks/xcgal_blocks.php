<?php
// $Id$
//  ------------------------------------------------------------------------ //
//                    xcGal 2.0 - XOOPS Gallery Modul                        //
//  ------------------------------------------------------------------------ //
//  Based on      xcGallery 1.1 RC1 - XOOPS Gallery Modul                    //
//                    Copyright (c) 2003 Derya Kiran                         //
//  ------------------------------------------------------------------------ //
//  Based on Coppermine Photo Gallery 1.10 http://coppermine.sourceforge.net///
//                      developed by Gr�gory DEMAR                           //
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

define("BLOCK_FIRST_USER_CAT", 10000);
define('RANDPOS_MAX_PIC_BLOCK', 200);
$xcgalDir = basename(dirname(dirname(__FILE__)));

function xcgal_block_func($options) {
    global $xcgalModule;
    $xcgalDir = basename(dirname(dirname(__FILE__)));
    $thumb_list= array();
    $block= array();
    block_album_set();
        $pic_datas = pic_data_block($options[1], $options[5], $options[4]);

        $i = 0;
        $piclist=array();
        switch ($options[1]){
    case '1':
        $album = "random";
        break;
    case '2':
        $album = "lastup";
        break;
    case '3':
        $album = "topn";
        break;
    case '4':
        $album = "toprated";
        break;
    case '5':
        $album = "lastcom";
        break;
    case '6':
        $album = "mostsend";
        break;
    case '7':
        $album = "lasthits";
        break;

    }

    $module_handler= & xoops_gethandler('module');
    $xcgalModule = $module_handler->getByDirname($xcgalDir);
    $config_handler =& xoops_gethandler('config');
        $xcgalConfig =& $config_handler->getConfigsByCat(0, $xcgalModule->mid());
        $block = array();
        if (count($pic_datas) > 0) {
                foreach ($pic_datas as $key => $row) {
                        $i++;

                        //$image_size = compute_img_size($row['pwidth'], $row['pheight'], $xoopsModuleConfig['thumb_width']);

                        $thumb_list[$i]['pos'] = $key < 0 ? $key : $i - 1 - $options[5];
                        $thumb_list[$i]['image'] = "<img src=\"".XOOPS_URL ."/modules/".$xcgalDir."/".$xcgalConfig['fullpath'].str_replace("%2F","/",rawurlencode($row['filepath'].$xcgalConfig['thumb_pfx'].$row['filename']))."\" class=\"image\" border=\"0\" alt=\"{$row['filename']}\" />";
                        $thumb_list[$i]['caption'] = $row['caption_text'];
                        $thumb_list[$i]['pid'] = $row['pid'];
                        $thumb_list[$i]['link_tgt']=XOOPS_URL ."/modules/".$xcgalDir."/displayimage.php?pid={$row['pid']}&amp;album={$album}&amp;pos={$key}&amp;cat=";
                    $thumb_list[$i]['i']= $i;

                }

                //$xoopsTpl->assign('no_img',0);
                //theme_display_thumbnails($thumb_list, $thumb_count, $album_name, $album, $cat, $page, $total_pages, is_numeric($album), $display_tabs);
                $block['pics'] = $thumb_list;
                $block['position'] = $options[2];
                $block['wh'] = $options[3];
        }
        return $block;
}
function xcgal_block_filmstrip_func($options) {
    global $xcgalModule;
    $xcgalDir = basename(dirname(dirname(__FILE__)));
    $thumb_list= array();
    $block= array();
    block_album_set();
        $pic_datas = pic_data_block($options[1], $options[5], $options[4]);

        $i = 0;
        $piclist=array();
        switch ($options[1]){
    case '1':
        $album = "random";
        break;
    case '2':
        $album = "lastup";
        break;
    case '3':
        $album = "topn";
        break;
    case '4':
        $album = "toprated";
        break;
    case '5':
        $album = "lastcom";
        break;
    case '6':
        $album = "mostsend";
        break;
    case '7':
        $album = "lasthits";
        break;

    }

    $module_handler= & xoops_gethandler('module');
    $xcgalModule = $module_handler->getByDirname($xcgalDir);
    $config_handler =& xoops_gethandler('config');
        $xcgalConfig =& $config_handler->getConfigsByCat(0, $xcgalModule->mid());
        $block = array();
        if (count($pic_datas) > 0) {
                foreach ($pic_datas as $key => $row) {
                        $i++;

                        //$image_size = compute_img_size($row['pwidth'], $row['pheight'], $xoopsModuleConfig['thumb_width']);

                        $thumb_list[$i]['pos'] = $key < 0 ? $key : $i - 1 - $options[5];
                        $thumb_list[$i]['image'] = XOOPS_URL ."/modules/".$xcgalDir."/".$xcgalConfig['fullpath'].str_replace("%2F","/",rawurlencode($row['filepath'].$xcgalConfig['normal_pfx'].$row['filename']));
                        $thumb_list[$i]['caption'] = $row['caption_text'];
                        $thumb_list[$i]['pid'] = $row['pid'];
                        $thumb_list[$i]['link_tgt']=XOOPS_URL ."/modules/".$xcgalDir."/displayimage.php?pid={$row['pid']}&amp;album={$album}&amp;pos={$key}&amp;cat=";
                    $thumb_list[$i]['i']= $i;

                }

                //$xoopsTpl->assign('no_img',0);
                //theme_display_thumbnails($thumb_list, $thumb_count, $album_name, $album, $cat, $page, $total_pages, is_numeric($album), $display_tabs);
                $block['pics'] = $thumb_list;
                $block['position'] = $options[2];
                $block['wh'] = $options[3];
        }
        return $block;
}

function xcgal_block_edit($options) {
    $form = "<input type='hidden' name='options[]' value='".intval($options[0])."' />";
    $form.= _MB_XCGAL_TYPE."&nbsp;<select name='options[]'>";
    $sel= array();
    for ( $i = 1; $i <= 6; $i++) {
                if ($i == intval($options[1])) $sel[$i] = "selected='selected'";
                else $sel[$i] = "";
        }
        $form.= "<option value='1' $sel[1]>"._MB_XCGAL_RANDOM."</option>";
        $form.= "<option value='2' $sel[2]>"._MB_XCGAL_NEWST."</option>";
        $form.= "<option value='3' $sel[3]>"._MB_XCGAL_VIEW."</option>";
        $form.= "<option value='4' $sel[4]>"._MB_XCGAL_TOP."</option>";
        $form.= "<option value='5' $sel[5]>"._MB_XCGAL_COMMENTS."</option>";
        $form.= "<option value='6' $sel[6]>"._MB_XCGAL_MOSTSENT."</option>";
        $form.= "<option value='7' $sel[7]>"._MB_XCGAL_LASTHITS."</option>";
        $form.= "</select>";
    if (intval($options[0]) == 1) {
        $form.= "<input type='hidden' name='options[]' value='".intval($options[2])."' />";
        $form.= "<br />"._MB_XCGAL_WIDTH."&nbsp;<input type='text' name='options[]' value='".intval($options[3])."' />";

    } else{
        $form.= "<br />"._MB_XCGAL_DISPLAY."&nbsp;<select name='options[]'><option value='1'";
        if ($options[2] == 1) $form .= " selected='selected'";
        $form.= ">"._MB_XCGAL_HORIZONTALLY."</option><option value='2'";
        if ($options[2] == 2) $form .= " selected='selected'";
        $form.= ">"._MB_XCGAL_VERTICALLY."</option></select><input type='hidden' name='options[]' value='".intval($options[3])."' />";
    }
    $form .= "<br />"._MB_XCGAL_CAPTION."&nbsp;<input type='radio' id='options[]' name='options[]' value='1'";
        if ( intval($options[4]) == 1 ) {
                $form .= " checked='checked'";
        }
        $form .= " />&nbsp;"._YES."&nbsp;<input type='radio' id='options[]' name='options[]' value='0'";
        if ( intval($options[4]) == 0 ) {
                $form .= " checked='checked'";
        }
        $form .= " />&nbsp;"._NO."";
        $form.= "<br />"._MB_XCGAL_COUNT."&nbsp;<input type='text' name='options[]' value='".intval($options[5])."' />";
    return $form;
}
function xcgal_catmenu_block_func(){
    global $CAT_LIST_BLOCK;
    $xcgalDir = basename(dirname(dirname(__FILE)));

    get_subcat_data_block(0,'&nbsp;&nbsp;&nbsp;');
    $block = array();
    $module_handler= & xoops_gethandler('module');
    $xcgalModule = $module_handler->getByDirname($xcgalDir);
    $block['xcgal'] = $xcgalModule->getVar('name');
    $block['cat_list'] = $CAT_LIST_BLOCK;

    return $block;
}

function xcgal_block_meta_func($options){

    $elements = preg_split("|/|", $options[0], -1, PREG_SPLIT_NO_EMPTY);
    $display= array();
    $display[0]= 0;
    $display[2]= 0;
    $display[3]= 0;
    $display[5]= $options[1];
    $display[4]= $options[2];
    $block = array();
    $block['count']= $options[1];
    $column_width = ceil(100/$options[1]);
        $block['width']=  $column_width;
    foreach ($elements as $element){
           if (preg_match("/(\w+),*(\d+)*/", $element, $matches)){
               switch($matches[1]){
            case 'random':
               $display[1]= 1;
               $block['metas'][$element]['title'] = _MB_XCGAL_RANDOM;
                break;

                case 'lastup':
                  $display[1]= 2;
                  $block['metas'][$element]['title'] = _MB_XCGAL_NEWST;
        break;

                case 'topn':
                  $display[1]= 3;
                  $block['metas'][$element]['title'] = _MB_XCGAL_VIEW;
        break;

                case 'toprated':
                  $display[1]= 4;
                  $block['metas'][$element]['title'] = _MB_XCGAL_TOP;
        break;

                case 'lastcom':
                  $display[1]= 5;
                  $block['metas'][$element]['title'] = _MB_XCGAL_COMMENTS;
        break;
                case 'mostsend':
                  $display[1]= 6;
                  $block['metas'][$element]['title'] = _MB_XCGAL_MOSTSENT;
                break;
                case 'lasthits':
                  $display[1]= 7;
                  $block['metas'][$element]['title'] = _MB_XCGAL_LASTHITS;
        break;
           }
           $display[5]= $options[1] * max(1,$matches[2]);
           $pics = xcgal_block_func($display);
           $block['metas'][$element]['pics']= $pics['pics'];
           $count = count($pics['pics']);
           if (count($pics['pics'])< $display[5] && $count % $options[1] != 0) {
            $block['metas'][$element]['empty']= 1;
            while ($count++ % $options[1] != 0) {
                      $block['metas'][$element]['empties'][]= "";
                }
       } else $block['metas'][$element]['empty']= 0;
        }
    }
    return $block;
}

function xcgal_block_meta_edit($options){
    $form = _MB_XCGAL_METAALBS."<input type='text' name='options[]' value='".($options[0])."' />";
    $form.= "<br />"._MB_XCGAL_PICSPERROW."<input type='text' name='options[]' value='".($options[1])."' />";
    $form.= "<br />"._MB_XCGAL_CAPTION."&nbsp;<input type='radio' id='options[]' name='options[]' value='1'";
        if ( $options[2] == 1 ) {
                $form .= " checked='checked'";
        }
        $form .= " />&nbsp;"._YES."&nbsp;<input type='radio' id='options[]' name='options[]' value='0'";
        if ( $options[2] == 0 ) {
                $form .= " checked='checked'";
        }
        $form .= " />&nbsp;"._NO."";
    return $form;
}
function get_subcat_data_block($parent, $ident='')
{
    global $CAT_LIST_BLOCK, $xoopsDB, $myts;
    $myts =& MyTextSanitizer::getInstance();
        $sql = "SELECT cid, name, description ".
                   "FROM ".$xoopsDB->prefix("xcgal_categories")." ".
                   "WHERE parent = '$parent' ".
                   "ORDER BY pos";
        $result = $xoopsDB->query($sql);

        if (($cat_count = $xoopsDB->getRowsNum($result)) > 0){
                $rowset = fetch_rowset_block($result);
                $pos=0;
                foreach ($rowset as $subcat){
                        if($pos>0){
                                $CAT_LIST_BLOCK[]=array(
                                        'cid' => $subcat['cid'],
                                        'parent' => $parent,
                                        'pos' => $pos++,
                                        'prev' => $prev_cid,
                                        'cat_count' => $cat_count,
                                        'name' => $ident.icms_core_DataFilter::htmlSpecialchars($subcat['name']));
                                $CAT_LIST_BLOCK[$last_index]['next'] = $subcat['cid'];
                        } else {
                                $CAT_LIST_BLOCK[]=array(
                                        'cid' => $subcat['cid'],
                                        'parent' => $parent,
                                        'pos' => $pos++,
                                        'cat_count' => $cat_count,
                                        'name' => $ident.icms_core_DataFilter::htmlSpecialchars($subcat['name']));
                        }
                        $prev_cid = $subcat['cid'];
                        $last_index = count($CAT_LIST_BLOCK) -1;
                        get_subcat_data_block($subcat['cid'], $ident.'&nbsp;&nbsp;&nbsp;');
                }
        }
}
function pic_data_block($album, $count, $set_caption)
{
        global $ALBUM_SET_BLOCK, $GLOBALS;
        global $xoopsDB, $xcgalModule;
        $xcgalDir = basename(dirname(dirname(__FILE__)));


    $select_columns = 'pid, filepath, filename, url_prefix, filesize, pwidth, pheight, ctime, title, caption';


        // Meta albums
        switch($album){
        case '5': // Last comments
                $select_columns = $select_columns.', com_id, com_uid,com_itemid,com_rootid, com_exparams, com_created, com_title';
                $member_handler = icms::handler('icms_member');
                $module_handler= & xoops_gethandler('module');
                $xcgalModule = $module_handler->getByDirname($xcgalDir);
                include_once XOOPS_ROOT_PATH."/include/comment_constants.php";
                $result = $xoopsDB->query("SELECT $select_columns FROM ".$xoopsDB->prefix("xoopscomments").", ".$xoopsDB->prefix("xcgal_pictures")." WHERE com_modid = ".$xcgalModule->mid()." AND approved = 'YES' AND pid = com_itemid AND com_status=".XOOPS_COMMENT_ACTIVE." $ALBUM_SET_BLOCK ORDER by com_id DESC LIMIT $count");
                $rowset = fetch_rowset_block($result);
                $xoopsDB->freeRecordSet($result);

                $comment_config = $xcgalModule->getInfo('comments');
                if ($set_caption) foreach ($rowset as $key => $row){
                        if ($row['com_uid'] > 0){
                            $poster =& $member_handler->getUser($row['com_uid']);
                            if (is_object($poster)) {
                                                $posters = '<a href="'.XOOPS_URL.'/userinfo.php?uid='.$row['com_uid'].'">'.$poster->getVar('uname').'</a>';
                                        } else {
                                                 $posters = $GLOBALS['xoopsConfig']['anonymous'];
                            }}
                        else $posters = $GLOBALS['xoopsConfig']['anonymous'];
                        $comtitle='<a href="'.XOOPS_URL.'/modules/".$xcgalDir."/'.$comment_config['pageName'].'?'.$comment_config['itemName'].'='.$row['com_itemid'].'&amp;com_id='.$row['com_id'].'&amp;com_rootid='.$row['com_rootid'].'&amp;com_mode=flat&amp;'.$row['com_exparams'].'#comment'.$row['com_id'].'">'.$row['com_title'].'</a>';
                        $caption = "<span style=\" font-size: 10px;        padding: 1px; display : block;\">".$posters.'</span>'."<span style=\" font-weight : bold; font-size: 10px; padding: 2px; display : block;\">".formatTimestamp($row['com_created'],'m').'</span>'."<span style=\" font-weight : bold; font-size: 10px; padding: 2px; display : block;\">".$comtitle.'</span>';
                        $rowset[$key]['caption_text'] = $caption;
                }
               return $rowset;
                break;

        case '2': // Last uploads
                $select_columns .= ', owner_id';

                $result = $xoopsDB->query("SELECT $select_columns FROM ".$xoopsDB->prefix("xcgal_pictures")." WHERE approved = 'YES' $ALBUM_SET_BLOCK ORDER BY pid DESC LIMIT $count");
                $rowset = fetch_rowset_block($result);
                $xoopsDB->freeRecordSet($result);
                if ($set_caption) foreach ($rowset as $key => $row){
                        $user_handler = icms::handler('icms_member');
                    $pic_owner =& $user_handler->getUser($row['owner_id']);
            if (is_object ($pic_owner)){
                            $user_link = '<br /><a href ="'.XOOPS_URL.'/userinfo.php?uid='.$pic_owner->uid().'">'.$pic_owner->uname().'</a>';
                        } else {
                                $user_link = '';
                        }
                        $caption = "<span style=\" font-weight : bold; font-size: 10px; padding: 2px; display : block;\">".formatTimestamp($row['ctime'],'m').$user_link.'</span>';
                        $rowset[$key]['caption_text'] = $caption;
                }

                return $rowset;
                break;

        case '3': // Most viewed pictures
                $select_columns .= ', hits';

                $result = $xoopsDB->query("SELECT $select_columns FROM ".$xoopsDB->prefix("xcgal_pictures")." WHERE approved = 'YES'AND hits > 0 $ALBUM_SET_BLOCK ORDER BY hits DESC LIMIT $count");
                $rowset = fetch_rowset_block($result);
                $xoopsDB->freeRecordSet($result);

                if ($set_caption) foreach ($rowset as $key => $row){
                        $caption = "<span style=\" font-weight : bold; font-size: 10px; padding: 2px; display : block;\">".sprintf(_MB_FUNC_VIEW, $row['hits']).'</span>';
                        $rowset[$key]['caption_text'] = $caption;
                }

                return $rowset;
                break;

        case '4': // Top rated pictures
                $select_columns .= ', pic_rating, votes';

                $result = $xoopsDB->query("SELECT $select_columns FROM ".$xoopsDB->prefix("xcgal_pictures")." WHERE approved = 'YES' AND votes > 0 $ALBUM_SET_BLOCK ORDER BY ROUND((pic_rating+1)/2000) DESC, votes DESC LIMIT $count");
                $rowset = fetch_rowset_block($result);
                $xoopsDB->freeRecordSet($result);

                if ($set_caption) foreach ($rowset as $key => $row){
                        $caption = "<span style=\" font-weight : bold; font-size: 10px; padding: 2px; display : block;\">".'<img src="'.XOOPS_URL.'/modules/".$xcgalDir."/images/rating'.round($row['pic_rating']/2000).'.gif" align="absmiddle"/>'.'<br />'.sprintf(_MB_FUNC_VOTE, $row['votes']).'</span>';
                        $rowset[$key]['caption_text'] = $caption;
                }
                return $rowset;
                break;

        case '1': // Random pictures
            $result = $xoopsDB->query("SELECT count(*) from ".$xoopsDB->prefix("xcgal_pictures")." WHERE approved = 'YES' $ALBUM_SET_BLOCK");
                $nbEnr = $xoopsDB->fetchArray($result);
                $pic_count = $nbEnr['count(*)'];
                $xoopsDB->freeRecordSet($result);

                // if we have more than 1000 pictures, we limit the number of picture returned
                // by the SELECT statement as ORDER BY RAND() is time consuming
                if ($pic_count > 1000) {
                    $result = $xoopsDB->query("SELECT count(*) from ".$xoopsDB->prefix("xcgal_pictures")." WHERE approved = 'YES'");
                        $nbEnr = $xoopsDB->fetchArray($result);
                        $total_count = $nbEnr['count(*)'];
                        $xoopsDB->freeRecordSet($result);

                        $granularity = floor($total_count / RANDPOS_MAX_PIC_BLOCK);
                        $cor_gran = ceil($total_count / $pic_count);
                        srand(time());
                        for ($i=1; $i<= $cor_gran; $i++) $random_num_set =rand(0, $granularity).', ';
                        $random_num_set = substr($random_num_set,0, -2);
                        $result = $xoopsDB->query("SELECT $select_columns FROM ".$xoopsDB->prefix("xcgal_pictures")." WHERE  randpos IN ($random_num_set) AND approved = 'YES' $ALBUM_SET_BLOCK ORDER BY RAND() LIMIT $count");
                } else {
                        $result = $xoopsDB->query("SELECT $select_columns FROM ".$xoopsDB->prefix("xcgal_pictures")." WHERE approved = 'YES' $ALBUM_SET_BLOCK ORDER BY RAND() LIMIT $count");
                }

                $rowset = array();
                while($row = $xoopsDB->fetchArray($result)){
                        $row['caption_text'] = '';
                        $rowset[-$row['pid']] = $row;
                }
                $xoopsDB->freeRecordSet($result);

                return $rowset;
                break;
        case '6': // Top sent ecards
                $select_columns .= ', sent_card';

                $result = $xoopsDB->query("SELECT $select_columns FROM ".$xoopsDB->prefix("xcgal_pictures")." WHERE approved = 'YES' AND sent_card >0 $ALBUM_SET_BLOCK ORDER BY sent_card DESC LIMIT $count");
                $rowset = fetch_rowset_block($result);
                $xoopsDB->freeRecordSet($result);

                if ($set_caption) foreach ($rowset as $key => $row){
                        $caption = "<span style=\" font-weight : bold; font-size: 10px; padding: 2px; display : block;\">".sprintf(_MB_FUNC_CARD, $row['sent_card']).'</span>';
                        $rowset[$key]['caption_text'] = $caption;
                }
                return $rowset;
        case '7': // Last hits
                $select_columns .= ', mtime';

                $result = $xoopsDB->query("SELECT $select_columns FROM ".$xoopsDB->prefix("xcgal_pictures")." WHERE approved = 'YES' $ALBUM_SET_BLOCK ORDER BY mtime DESC LIMIT $count");
                $rowset = fetch_rowset_block($result);
                $xoopsDB->freeRecordSet($result);

                if ($set_caption) foreach ($rowset as $key => $row){
                        $caption = "<span style=\" font-weight : bold; font-size: 10px; padding: 2px; display : block;\">".formatTimestamp($row['mtime'],'m').'</span>';
                        $rowset[$key]['caption_text'] = $caption;
                }
                return $rowset;
                break;
        }
}

function fetch_rowset_block($result)
{   global $xoopsDB;
        $rowset = array();

        while ($row = $xoopsDB->fetchArray($result)) $rowset[] = $row;

        return $rowset;
}
function block_album_set()
{
        global $ALBUM_SET_BLOCK, $xoopsDB,$xoopsUser;
        $xcgalDir = basename(dirname(dirname(__FILE__)));
        if (is_object ($xoopsUser)){
        $usergroups= $xoopsUser->getgroups();
            $usergroup= implode(",",$usergroups);
        $buid= $xoopsUser->uid();
    } else {
        $usergroup= XOOPS_GROUP_ANONYMOUS;
        $buid = 0;
    }
    $module_handler= & xoops_gethandler('module');
    $xcgalModule = $module_handler->getByDirname($xcgalDir);
    if(is_object($xoopsUser) && ($xoopsUser->isAdmin($xcgalModule->mid()))) $ALBUM_SET_BLOCK= "";
    else {
        $result = $xoopsDB->query("SELECT aid FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE visibility NOT IN ($usergroup, 0,".(BLOCK_FIRST_USER_CAT + $buid).")");
        if (($xoopsDB->getRowsNum($result))) {
                $set ='';
            while($album=$xoopsDB->fetchArray($result)){
                    $set .= $album['aid'].',';
            } // while
                $ALBUM_SET_BLOCK .= 'AND aid NOT IN ('.substr($set, 0, -1).') ';
        }
        $xoopsDB->freeRecordSet($result);
        }
}
?>
