<?php
// $Id$

/**************************************************************************
   Function for managing cookie saved user profile
 **************************************************************************/

// Decode the user profile contained in a cookie
function user_get_profile()
{
        global $xoopsModuleConfig, $USER, $_COOKIE, $xoopsUser;

        if (isset($_COOKIE[$xoopsModuleConfig['cookie_name'].'_data'])) {
                $USER = @unserialize(@base64_decode($_COOKIE[$xoopsModuleConfig['cookie_name'].'_data']));
        }

        if (!isset($USER['ID']) || strlen($USER['ID']) != 32) {
                list($usec, $sec) = explode(' ', microtime());
                $seed = (float) $sec + ((float) $usec * 100000);
                srand($seed);
                $USER=array('ID' => md5(uniqid(rand(),1)));
        } else {
                $USER['ID'] = addslashes($USER['ID']);
        }
        if (!isset($USER['am'])) $USER['am'] = 1;
}

// Save the user profile in a cookie
function user_save_profile()
{
        global $xoopsModuleConfig, $USER, $_SERVER;
        static $profile_saved = 0;

        if (!$profile_saved) {
                $data = base64_encode(serialize($USER));
                setcookie($xoopsModuleConfig['cookie_name'].'_data', $data, time()+86400*30, $xoopsModuleConfig['cookie_path']);
                $profile_saved=1;
        }
}

/**************************************************************************
   Database functions
 **************************************************************************/


// Fetch all rows in an array
function db_fetch_rowset($result)
{   global $xoopsDB;
        $rowset = array();

        while ($row = $xoopsDB->fetchArray($result)) $rowset[] = $row;

        return $rowset;
}

/**************************************************************************
   Utilities functions
 **************************************************************************/

// Function to create correct URLs for image name with space or exotic characters
function path2url($path)
{
        return str_replace("%2F","/",rawurlencode($path));
}

// create tabs for multi-page navigation
// $template = array(
//        'left_text' => ,
//        'tab_header' => ,
//        'tab_trailer' => ,
//  'active_tab' => ,
//        'inactive_tab' => );
function create_tabs($items, $curr_page, $total_pages, $template)
{
        global $xoopsModuleConfig;

        if (function_exists('theme_create_tabs')) {
            theme_create_tabs($items, $curr_page, $total_pages, $template);
                return;
        }

        $maxTab = $xoopsModuleConfig['max_tabs'];

        $tabs = sprintf($template['left_text'], $items, $total_pages, 1);
        
        if (($total_pages == 1)) return $tabs;

        $tabs .= $template['tab_header'];
        if ($curr_page == 1) {
                $tabs .= sprintf($template['active_tab'], 1);
        } else {
                $tabs .= sprintf($template['inactive_tab'], 1, 1);
        }
        if ($total_pages > $maxTab){
                $start = max(2, $curr_page - floor(($maxTab -2)/2));
                $start = min($start, $total_pages - $maxTab +2);
                $end = $start + $maxTab -3;
        } else {
                $start = 2;
                $end = $total_pages-1;
        }
        for ($page = $start ; $page <= $end; $page++) {
                if ($page == $curr_page) {
                        $tabs .= sprintf($template['active_tab'], $page);
                } else {
                        $tabs .= sprintf($template['inactive_tab'], $page, $page);
                }
        }
        if ($total_pages > 1){
                if ($curr_page == $total_pages) {
                        $tabs .= sprintf($template['active_tab'], $total_pages);
                } else {
                        $tabs .= sprintf($template['inactive_tab'], $total_pages, $total_pages);
                }
        }
        return $tabs.$template['tab_trailer'];
}

/**************************************************************************
   Functions for album/picture management
 **************************************************************************/

// Get the list of albums that the current user can't see
function get_private_album_set()
{
        global $ALBUM_SET, $USER_DATA, $FORBIDDEN_SET, $xoopsDB,$xoopsUser,$suid;
        if (is_object ($xoopsUser)){
        $usergroups= $xoopsUser->getgroups();
            $usergroup=implode(",",$usergroups);
        }
    else $usergroup= XOOPS_GROUP_ANONYMOUS;

        $result = $xoopsDB->query("SELECT aid FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE visibility NOT IN ($usergroup, 0,".(FIRST_USER_CAT + USER_ID).")");
        if (($xoopsDB->getRowsNum($result))) {
                $set ='';
            while($album=$xoopsDB->fetchArray($result)){
                    $set .= $album['aid'].',';
            } // while
                $FORBIDDEN_SET = "AND p.aid NOT IN (".substr($set, 0, -1).') ';
                $ALBUM_SET .= 'AND aid NOT IN ('.substr($set, 0, -1).') ';
        }
        $xoopsDB->freeRecordSet($result);
}

// Retrieve the data for a picture or a set of picture
function get_pic_data($album, &$count, &$album_name, $limit1=-1, $limit2=-1, $set_caption = true)
{
        global $USER, $xoopsModuleConfig, $ALBUM_SET, $CURRENT_CAT_NAME, $HTML_SUBST, $THEME_DIR;
        global $GLOBALS;
        global $xoopsDB, $xoopsModule, $xoopsConfig;
    $myts =& MyTextSanitizer::getInstance(); // MyTextSanitizer object
        $sort_array = array('na' => 'filename ASC', 'nd' => 'filename DESC', 'da' => 'pid ASC', 'dd' => 'pid DESC');
    $sort_code = isset($USER['sort'])? $USER['sort'] : $xoopsModuleConfig['default_sort_order'];
        $sort_order = isset($sort_array[$sort_code]) ? $sort_array[$sort_code] : $sort_array[$xoopsModuleConfig['default_sort_order']];
        $limit = ($limit1 != -1) ? ' LIMIT '. $limit1 : '';
        $limit .= ($limit2 != -1) ? ' ,'. $limit2 : '';

        if ($limit2 == 1) {
            $select_columns = '*';
        } else {
            $select_columns = 'pid, filepath, filename, url_prefix, filesize, pwidth, pheight, ctime';
        }

        // Regular albums
        if ((is_numeric($album))) {
            $album_name = get_album_name($album);

                $approved = GALLERY_ADMIN_MODE ? '' : 'AND approved=\'YES\'';

                $result = $xoopsDB->query("SELECT count(*) from ".$xoopsDB->prefix("xcgal_pictures")." WHERE aid='$album' $approved $ALBUM_SET");
                $nbEnr = $xoopsDB->fetchArray($result);
                $count = $nbEnr['count(*)'];
                $xoopsDB->freeRecordSet($result);

                if($select_columns != '*') $select_columns .= ', title, caption, owner_id';

                $result = $xoopsDB->query("SELECT $select_columns from ".$xoopsDB->prefix("xcgal_pictures")." WHERE aid='$album' $approved $ALBUM_SET ORDER BY $sort_order $limit");
                $rowset = db_fetch_rowset($result);
                $xoopsDB->freeRecordSet($result);

                // Set picture caption
                if ($set_caption) foreach ($rowset as $key => $row){
                        $caption = $rowset[$key]['title'] ? "<span class=\"thumb_title\">".$rowset[$key]['title']."</span>" : '';
                        if ($xoopsModuleConfig['caption_in_thumbview']){
                           $caption .= $rowset[$key]['caption'] ? "<span class=\"thumb_caption\">".$myts->makeTareaData4Show($rowset[$key]['caption'],0)."</span>" : '';
                        }
                        if ($xoopsModuleConfig['display_comment_count']) {
                                $comments_nr =  xoops_comment_count($xoopsModule->mid(),$row['pid'] );
                                if ($comments_nr > 0) $caption .= "<span class=\"thumb_num_comments\">".sprintf(_MD_FUNC_COM, $comments_nr )."</span>";
                        }
                        $rowset[$key]['caption_text'] = $caption;
                }

                return $rowset;
        }


        // Meta albums
        switch($album){
        case 'lastcom': // Last comments
                if ($ALBUM_SET && $CURRENT_CAT_NAME) {
                        $album_name = $album_name = _MD_LASTCOM.' - '. $CURRENT_CAT_NAME;
                } else {
                        $album_name = _MD_LASTCOM;
                }
                $result = $xoopsDB->query("SELECT count(*) from ".$xoopsDB->prefix("xoopscomments").", ".$xoopsDB->prefix("xcgal_pictures")." WHERE com_modid = ".$xoopsModule->mid()." AND approved='YES' AND com_itemid = pid $ALBUM_SET");
                $nbEnr = $xoopsDB->fetchArray($result);
                        $count = $nbEnr['count(*)'];
                        $xoopsDB->freeRecordSet($result);

                if($select_columns != '*'){
                   $select_columns = $select_columns.', com_id, com_uid,com_itemid,com_rootid, com_exparams, com_created, com_title';
                }
                include_once ICMS_ROOT_PATH."/include/comment_constants.php";
                $result = $xoopsDB->query("SELECT $select_columns FROM ".$xoopsDB->prefix("xoopscomments").", ".$xoopsDB->prefix("xcgal_pictures")." WHERE com_modid = ".$xoopsModule->mid()." AND approved = 'YES' AND pid = com_itemid AND com_status=".XOOPS_COMMENT_ACTIVE." $ALBUM_SET ORDER by com_id DESC $limit");
                $rowset = db_fetch_rowset($result);
                $xoopsDB->freeRecordSet($result);
                $member_handler = icms::handler('icms_member');
                $comment_config = $xoopsModule->getInfo('comments');
                if ($set_caption) foreach ($rowset as $key => $row){
                        if ($row['com_uid'] > 0){
                            $poster =& $member_handler->getUser($row['com_uid']);
                            if (is_object($poster)) {
                                                $posters = '<a href="'.ICMS_URL.'/userinfo.php?uid='.$row['com_uid'].'">'.$poster->getVar('uname').'</a>';
                                        } else {
                                                 $posters = $GLOBALS['xoopsConfig']['anonymous'];
                            }}
                        else $posters = $GLOBALS['xoopsConfig']['anonymous'];
                        $comtitle='<a href="'.ICMS_URL.'/modules/'.$xoopsModule->getVar('dirname').'/'.$comment_config['pageName'].'?'.$comment_config['itemName'].'='.$row['com_itemid'].'&amp;com_id='.$row['com_id'].'&amp;com_rootid='.$row['com_rootid'].'&amp;com_mode=flat&amp;'.$row['com_exparams'].'#comment'.$row['com_id'].'">'.$row['com_title'].'</a>';
                        $caption = "<span class=\"thumb_title\">".$posters.'</span>'."<span class=\"thumb_caption\">".formatTimestamp($row['com_created'],'m').'</span>'."<span class=\"thumb_caption\">".$comtitle.'</span>';
                        $rowset[$key]['caption_text'] = $caption;
                }
               return $rowset;
                break;

        case 'lastup': // Last uploads
                if ($ALBUM_SET && $CURRENT_CAT_NAME) {
                        $album_name = _MD_LASTUP.' - '. $CURRENT_CAT_NAME;
                } else {
                        $album_name = _MD_LASTUP;
                }
                $result = $xoopsDB->query("SELECT count(*) from ".$xoopsDB->prefix("xcgal_pictures")." WHERE approved = 'YES' $ALBUM_SET");
                $nbEnr = $xoopsDB->fetchArray($result);
                $count = $nbEnr['count(*)'];
                $xoopsDB->freeRecordSet($result);

                if($select_columns != '*') $select_columns .= ', owner_id';

                $result = $xoopsDB->query("SELECT $select_columns FROM ".$xoopsDB->prefix("xcgal_pictures")." WHERE approved = 'YES' $ALBUM_SET ORDER BY pid DESC $limit");
                $rowset = db_fetch_rowset($result);
                $xoopsDB->freeRecordSet($result);
                if ($set_caption) foreach ($rowset as $key => $row){
                    $user_handler = icms::handler('icms_member');
                    $pic_owner =& $user_handler->getUser($row['owner_id']);
            if (is_object ($pic_owner)){
                            $user_link = '<br /><a href ="'.ICMS_URL.'/userinfo.php?uid='.$pic_owner->uid().'">'.$pic_owner->uname().'</a>';
                        } else {
                                $user_link = '';
                        }
                        $caption = "<span class=\"thumb_caption\">".formatTimestamp($row['ctime'],'m').$user_link.'</span>';
                        $rowset[$key]['caption_text'] = $caption;
                }

                return $rowset;
                break;

        case 'topn': // Most viewed pictures
                if ($ALBUM_SET && $CURRENT_CAT_NAME) {
                        $album_name = _MD_TOPN.' - '. $CURRENT_CAT_NAME;
                } else {
                        $album_name = _MD_TOPN;
                }
                $result = $xoopsDB->query("SELECT count(*) from ".$xoopsDB->prefix("xcgal_pictures")." WHERE approved = 'YES' AND hits > 0  $ALBUM_SET");
                $nbEnr = $xoopsDB->fetchArray($result);
                $count = $nbEnr['count(*)'];
                $xoopsDB->freeRecordSet($result);

                if($select_columns != '*') $select_columns .= ', hits';

                $result = $xoopsDB->query("SELECT $select_columns FROM ".$xoopsDB->prefix("xcgal_pictures")." WHERE approved = 'YES' AND hits > 0 $ALBUM_SET ORDER BY hits DESC, ctime, mtime $limit");
                $rowset = db_fetch_rowset($result);
                $xoopsDB->freeRecordSet($result);

                if ($set_caption) foreach ($rowset as $key => $row){
                        $caption = "<span class=\"thumb_caption\">".sprintf(_MD_FUNC_VIEW, $row['hits']).'</span>';
                        $rowset[$key]['caption_text'] = $caption;
                }

                return $rowset;
                break;

        case 'toprated': // Top rated pictures
                if ($ALBUM_SET && $CURRENT_CAT_NAME) {
                        $album_name = _MD_TOPRATED.' - '. $CURRENT_CAT_NAME;
                } else {
                        $album_name = _MD_TOPRATED;
                }
                $result = $xoopsDB->query("SELECT count(*) from ".$xoopsDB->prefix("xcgal_pictures")." WHERE approved = 'YES' AND votes >= '{$xoopsModuleConfig['min_votes_for_rating']}' $ALBUM_SET");
                $nbEnr = $xoopsDB->fetchArray($result);
                $count = $nbEnr['count(*)'];
                $xoopsDB->freeRecordSet($result);

                if($select_columns != '*') $select_columns .= ', pic_rating, votes';

                $result = $xoopsDB->query("SELECT $select_columns FROM ".$xoopsDB->prefix("xcgal_pictures")." WHERE approved = 'YES' AND votes >= '{$xoopsModuleConfig['min_votes_for_rating']}' $ALBUM_SET ORDER BY ROUND((pic_rating+1)/2000) DESC, votes DESC $limit");
                $rowset = db_fetch_rowset($result);
                $xoopsDB->freeRecordSet($result);

                if ($set_caption) foreach ($rowset as $key => $row){
                        if (defined('THEME_HAS_RATING_GRAPHICS')) {
                            $prefix= $THEME_DIR;
                        } else {
                            $prefix= '';
                        }
                        $caption = "<span class=\"thumb_caption\">".'<img src="'.$prefix.'images/rating'.round($row['pic_rating']/2000).'.gif" align="middle" alt=""/>'.'<br />'.sprintf(_MD_FUNC_VOTE, $row['votes']).'</span>';
                        $rowset[$key]['caption_text'] = $caption;
                }
                return $rowset;
                break;

        case 'lasthits': // Last viewed pictures
                if ($ALBUM_SET && $CURRENT_CAT_NAME) {
                        $album_name = _MD_LASTHITS.' - '. $CURRENT_CAT_NAME;
                } else {
                        $album_name = _MD_LASTHITS;
                }
                $result = $xoopsDB->query("SELECT count(*) from ".$xoopsDB->prefix("xcgal_pictures")." WHERE approved = 'YES' $ALBUM_SET");
                $nbEnr = $xoopsDB->fetchArray($result);
                $count = $nbEnr['count(*)'];
                $xoopsDB->freeRecordSet($result);

                if($select_columns != '*') $select_columns .= ', mtime';

                $result = $xoopsDB->query("SELECT $select_columns FROM ".$xoopsDB->prefix("xcgal_pictures")." WHERE approved = 'YES' $ALBUM_SET ORDER BY mtime DESC $limit");
                $rowset = db_fetch_rowset($result);
                $xoopsDB->freeRecordSet($result);

                if ($set_caption) foreach ($rowset as $key => $row){
                        $caption = "<span class=\"thumb_caption\">".formatTimestamp($row['mtime'],'m').'</span>';
                        $rowset[$key]['caption_text'] = $caption;
                }
                return $rowset;
                break;

        case 'random': // Random pictures
                if ($ALBUM_SET && $CURRENT_CAT_NAME) {
                        $album_name = _MD_RANDOM.' - '. $CURRENT_CAT_NAME;
                } else {
                        $album_name = _MD_RANDOM;
                }
                $result = $xoopsDB->query("SELECT count(*) from ".$xoopsDB->prefix("xcgal_pictures")." WHERE approved = 'YES' $ALBUM_SET");
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

                        $granularity = floor($total_count / RANDPOS_MAX_PIC);
                        $cor_gran = ceil($total_count / $pic_count);
                        srand(time());
                        for ($i=1; $i<= $cor_gran; $i++) $random_num_set =rand(0, $granularity).', ';
                        $random_num_set = substr($random_num_set,0, -2);
                        $result = $xoopsDB->query("SELECT $select_columns FROM ".$xoopsDB->prefix("xcgal_pictures")." WHERE  randpos IN ($random_num_set) AND approved = 'YES' $ALBUM_SET ORDER BY RAND() LIMIT $limit2");
                } else {
                        $result = $xoopsDB->query("SELECT $select_columns FROM ".$xoopsDB->prefix("xcgal_pictures")." WHERE approved = 'YES' $ALBUM_SET ORDER BY RAND() LIMIT $limit2");
                }

                $rowset = array();
                while($row = $xoopsDB->fetchArray($result)){
                        $row['caption_text'] = '';
                        $rowset[-$row['pid']] = $row;
                }
                $xoopsDB->freeRecordSet($result);

                return $rowset;
                break;

        case 'search': // Search results
                if (isset($USER['search'])) {
                        $search_string = ($USER['search']);
                } else {
                        $search_string = '';
                }

                if (substr($search_string, 0, 3) == '###') {
                    $query_all = 1;
                        $search_string = substr($search_string, 3);
                } else {
                    $query_all = 0;
                }

                if ($ALBUM_SET && $CURRENT_CAT_NAME) {
                        $album_name = _MD_SEARCH.' - '. $CURRENT_CAT_NAME;
                } else {
                        $album_name = _MD_SEARCH.' - "'.$search_string. '"';
                }
                //var_dump(htmlspecialchars($search_string));
        //$search_string = utf8Encode($search_string);
                include 'include/search.inc.php';

                return $rowset;
                break;
        case 'usearch': // User pics search results
            if (isset($USER['suid']) && $USER['suid'] > 0){
                    $owner = new XoopsUser($USER['suid']);
            $album_name = _MD_USEARCH.$owner->uname();
        }
        else $album_name = 'Pics submitted by '.$xoopsConfig['anonymous'];
        $result = $xoopsDB->query("SELECT count(*) from ".$xoopsDB->prefix("xcgal_pictures")." WHERE approved = 'YES' AND owner_id = '{$USER['suid']}' $ALBUM_SET");
                $nbEnr = $xoopsDB->fetchArray($result);
                $count = $nbEnr['count(*)'];
                $xoopsDB->freeRecordSet($result);

                if($select_columns != '*') $select_columns .= ', pic_rating, votes';

                $result = $xoopsDB->query("SELECT $select_columns FROM ".$xoopsDB->prefix("xcgal_pictures")." WHERE approved = 'YES' AND owner_id = '{$USER['suid']}' $ALBUM_SET ORDER BY ctime DESC $limit");
                $rowset = db_fetch_rowset($result);
                $xoopsDB->freeRecordSet($result);

                if ($set_caption) foreach ($rowset as $key => $row){
                        $caption = "<span class=\"thumb_caption\">".formatTimestamp($row['ctime'],'m')."</span>";
                        $rowset[$key]['caption_text'] = $caption;
                }
                return $rowset;
                break;
        case 'mostsend': // Top rated pictures
                if ($ALBUM_SET && $CURRENT_CAT_NAME) {
                        $album_name = _MD_MOST_SENT.' - '. $CURRENT_CAT_NAME;
                } else {
                        $album_name = _MD_MOST_SENT;
                }
                $result = $xoopsDB->query("SELECT count(*) from ".$xoopsDB->prefix("xcgal_pictures")." WHERE approved = 'YES' AND sent_card > 0 $ALBUM_SET");
                $nbEnr = $xoopsDB->fetchArray($result);
                $count = $nbEnr['count(*)'];
                $xoopsDB->freeRecordSet($result);

                if($select_columns != '*') $select_columns .= ', sent_card';

                $result = $xoopsDB->query("SELECT $select_columns FROM ".$xoopsDB->prefix("xcgal_pictures")." WHERE approved = 'YES' AND sent_card >0 $ALBUM_SET ORDER BY sent_card DESC $limit");
                $rowset = db_fetch_rowset($result);
                $xoopsDB->freeRecordSet($result);

                if ($set_caption) foreach ($rowset as $key => $row){
                        $caption = "<span class=\"thumb_caption\">".sprintf(_MD_FUNC_SEND, $row['sent_card']).'</span>';
                        $rowset[$key]['caption_text'] = $caption;
                }
                return $rowset;
                break;


        default : // Invalid meta album
                redirect_header('index.php',2, _MD_NON_EXIST_AP);
        }
} // End of get_pic_data


// Get the name of an album
function get_album_name($aid)
{
        global $xoopsDB;

        $result = $xoopsDB->query("SELECT title from ".$xoopsDB->prefix("xcgal_albums")." WHERE aid='$aid'");
        $count = $xoopsDB->getRowsNum($result);
        if ($count > 0) {
                $row = $xoopsDB->fetchArray($result);
                return $row['title'];
        } else {
                redirect_header('index.php',2,_MD_NON_EXIST_AP);
        }
}

// Add 1 everytime a picture is viewed.
function add_hit($pid)
{
        global $xoopsDB;
        $xoopsDB->queryf("UPDATE ".$xoopsDB->prefix("xcgal_pictures")." SET hits=hits+1, mtime=".time()." WHERE pid='$pid'");

}

// Build the breadcrumb
function breadcrumb($cat, &$breadcrumb, &$BREADCRUMB_TEXT)
{
        global $xoopsModule;
        global $CURRENT_CAT_NAME, $xoopsDB;

    $myts =& MyTextSanitizer::getInstance(); // MyTextSanitizer object
        $breadcrumb = '';
        if ($cat != 0) {
                $breadcrumb_array = array();
                if ($cat >= FIRST_USER_CAT) {
                    $row= array();
                    $user_handler = icms::handler('icms_member');
                    $alb_owner =& $user_handler->getUser($cat-FIRST_USER_CAT);
            if (!is_object($alb_owner) && !USER_IS_ADMIN) redirect_header('index.php',2,_MD_NO_EXIST_CAT);
            elseif (!is_object($alb_owner)) $row['uname'] = _MD_FUNC_DELUSER." uid=".($cat - FIRST_USER_CAT);
                        else $row['uname'] = $alb_owner->uname();
                        $breadcrumb_array[] = array($cat, $row['uname']);
                        $CURRENT_CAT_NAME = sprintf(_MD_INDEX_USERS_GAL, $row['uname']);
                        $row['parent'] = 1;
                        if (isset($result)) $xoopsDB->freeRecordSet($result);
                } else {
                    $result = $xoopsDB->query("SELECT name, parent FROM ".$xoopsDB->prefix("xcgal_categories")." WHERE cid = '$cat'");
                        if ($xoopsDB->getRowsNum($result) == 0) redirect_header('index.php',2,_MD_NO_EXIST_CAT);
                        $row = $xoopsDB->fetchArray($result);
            $row['name']=icms_core_DataFilter::htmlSpecialchars($row['name']);
                        $breadcrumb_array[] = array($cat, $row['name']);
                        $CURRENT_CAT_NAME = $row['name'];
                        $xoopsDB->freeRecordSet($result);
                }
                while($row['parent'] != 0){
                    $result = $xoopsDB->query("SELECT cid, name, parent FROM ".$xoopsDB->prefix("xcgal_categories")." WHERE cid = '{$row['parent']}'");
                        if ($xoopsDB->getRowsNum($result) == 0) redirect_header('index.php',2,_MD_ORPHAN_CAT);
                        $row = $xoopsDB->fetchArray($result);
            $row['name']=icms_core_DataFilter::htmlSpecialchars($row['name']);
                        $breadcrumb_array[] = array($row['cid'], $row['name']);
                        $xoopsDB->freeRecordSet($result);
                } // while

                $breadcrumb_array = array_reverse($breadcrumb_array);
                $breadcrumb = '<a href=index.php>'.$xoopsModule->getVar('name').'</a>';
                $BREADCRUMB_TEXT = $xoopsModule->getVar('name');
                foreach ($breadcrumb_array as $category){
                        $link = "<a href=index.php?cat={$category[0]}>{$category[1]}</a>";
                        $breadcrumb .= ' > ' . $link;
                        $BREADCRUMB_TEXT .= ' > ' . $category[1];
                }

        }
}

/**************************************************************************

 **************************************************************************/

// Compute image geometry based on max width / height
function compute_img_size($width, $height, $max)
{
        $ratio = max($width, $height) / $max;
        if ($ratio > 1.0) {
                $image_size['reduced'] = true;
        }
        $ratio = max($ratio, 1.0);
        $image_size['width'] = ceil($width / $ratio);
        $image_size['height'] = ceil($height / $ratio);
        $image_size['geom'] = 'width="'.$image_size['width'].'" height="'.$image_size['height'].'"';

        return $image_size;
}

// Prints thumbnails of pictures in an album
function display_thumbnails($album, $cat, $page, $thumbcols, $thumbrows, $display_tabs)
{
        global $xoopsModuleConfig, $xoopsTpl;
        $myts =& MyTextSanitizer::getInstance(); // MyTextSanitizer object

        $thumb_per_page = $thumbcols * $thumbrows;
        $lower_limit = ($page-1) * $thumb_per_page;

        $pic_data = get_pic_data($album, $thumb_count, $album_name, $lower_limit, $thumb_per_page);
        $total_pages = ceil($thumb_count / $thumb_per_page);

        $i = 0;
        if (count($pic_data) > 0) {
                foreach ($pic_data as $key => $row) {
                        $i++;

                        $image_size = compute_img_size($row['pwidth'], $row['pheight'], $xoopsModuleConfig['thumb_width']);

                        $pic_title =_MD_FUNC_FNAME.icms_core_DataFilter::htmlSpecialchars($row['filename'])."\n".
                                _MD_FUNC_FSIZE.($row['filesize'] >> 10)._MD_KB."\n".
                                _MD_FUNC_DIM.$row['pwidth']."x".$row['pheight']."\n".
                                _MD_FUNC_DATE.formatTimestamp($row['ctime'],'m');

                        $thumb_list[$i]['pos'] = $key < 0 ? $key : $i - 1 + $lower_limit;
                        $thumb_list[$i]['image'] = "<img src=\"" . get_pic_url($row, 'thumb') . "\" class=\"image\" {$image_size['geom']} border=\"0\" alt=\"{$row['filename']}\" title=\"$pic_title\" />";     # deleted </a> at end mcleines
                        $thumb_list[$i]['caption'] = ($row['caption_text']);
                        $thumb_list[$i]['admin_menu'] = '';
                        $thumb_list[$i]['pid'] = $row['pid'];

                }
                $xoopsTpl->assign('no_img',0);
                theme_display_thumbnails($thumb_list, $thumb_count, $album_name, $album, $cat, $page, $total_pages, is_numeric($album), $display_tabs);
        } else {
            $xoopsTpl->assign('no_img',1);
            $xoopsTpl->assign('lang_no_img',_MD_NO_IMG_TO_DISPLAY);
            $xoopsTpl->assign('album_name',icms_core_DataFilter::htmlSpecialchars($album_name));
        }
}

// Return the url for a picture, allows to have pictures spreaded over multiple servers
function get_pic_url(&$pic_row, $mode)
{
        global $xoopsModuleConfig, $xoopsModule;

        static $pic_prefix = array();
        static $url_prefix = array();

        if (!count($pic_prefix)) {
                $pic_prefix = array(
                        'thumb' => $xoopsModuleConfig['thumb_pfx'],
                        'normal' => $xoopsModuleConfig['normal_pfx'],
                        'fullsize' => ''
                );

                $url_prefix = array(
                        0 => $xoopsModuleConfig['fullpath'],
                );
        }

        // watermarking for JPG
        $ext = strrchr($pic_row['filename'], ".");
        if ((strtolower($ext) == ".jpg" or strtolower($ext) == ".jpeg") and $xoopsModuleConfig['watermarking']) {
                return ICMS_URL.'/modules/'.$xoopsModule->getVar('dirname').'/watermark.php?picturename='.$url_prefix[$pic_row['url_prefix']]. path2url($pic_row['filepath']. $pic_prefix[$mode]. $pic_row['filename']);
        }
        else {
             return $url_prefix[$pic_row['url_prefix']]. path2url($pic_row['filepath']. $pic_prefix[$mode]. $pic_row['filename']);
        }
}

function clean_words(&$entry)
{
        //global $charset, $multibyte_charset;

        static $drop_char_match =   array('^', '$', '(', ')', '<', '>', '`', '\'', '"', '|', ',', '@', '_', '?', '%', '~', '.', '[', ']', '{', '}', ':', '\\', '/', '=', '\'', '!');
        static $drop_char_replace = array(' ', ' ', ' ', ' ', ' ', ' ', ' ', '',  '',   ' ', ' ', ' ', ' ', '',  ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ' , ' ', ' ', ' ', ' ',  ' ', ' ');

        $entry = ' ' . strtolower($entry) . ' ';

        // Replace line endings by a space
        $entry = preg_replace('/[\n\r]/is', ' ', $entry);

        // + and - becomes and & not
        $entry = str_replace(' +', ' and ', $entry);
        $entry = str_replace(' -', ' not ', $entry);

        //
        // Filter out strange characters like ^, $, &, change "it's" to "its"
        //
        if(XOOPS_USE_MULTIBYTES == 0) for($i = 0; $i < count($drop_char_match); $i++)
        {
                $entry =  str_replace($drop_char_match[$i], $drop_char_replace[$i], $entry);
        }

        return $entry;
}

// Multi-media Beta functions
function file_type_test($file,$types)
{
        if (!is_array($file))
                $file = explode('.',$file);     // explode the filename
        $EOA = count($file)-1;                  // use the last element as the extension (END OF ARRAY)
        if ($EOA>0)                             // If not EOA (extension exists) test extension
                foreach ($types as $extension)
                {
                        if (strcasecmp($file[$EOA],$extension)==0)
                                return strtolower($extension);
                }
        return false;
}

function is_image(&$file)
{
        global $IMG_TYPES;
        return file_type_test($file,$IMG_TYPES);
}

function is_movie(&$file)
{
        global $MOV_TYPES;
        return file_type_test($file,$MOV_TYPES);
}

function is_audio(&$file)
{
        global $SND_TYPES;
        return file_type_test($file,$SND_TYPES);
}

function is_document(&$file)
{
        global $DOC_TYPES;
        return file_type_test($file,$DOC_TYPES);
}

function is_known_filetype(&$file) {
        return is_image($file) || is_movie($file) || is_audio($file) || is_document($file);
}

function get_media_type(&$file)
{
        foreach(array('image','movie','audio','document') as $type)
        {
                eval("\$file_type = is_$type(\$file);");
                if ($file_type) return $type;
        }
        return null;
}

function get_real_path()
{
        global $xoopsModuleConfig;

        if (empty($xoopsModuleConfig['realpath']))
                return $xoopsModuleConfig['fullpath'];
        else
                return $xoopsModuleConfig['realpath'];
}

function get_full_real_path()
{
        global $xoopsModuleConfig;
        $xcgalDir = basename(dirname(dirname(__FILE__)));

        if (empty($xoopsModuleConfig['realpath']))
                return ICMS_ROOT_PATH."/modules/".$xcgalDir."/".$xoopsModuleConfig['fullpath'];
        else
                return $xoopsModuleConfig['realpath'];
}

?>