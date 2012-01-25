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
include "header.php";


//if (!GALLERY_ADMIN_MODE) redirect_header('index.php',2,$lang_errors['access_denied']);

function list_users()
{
        global $PHP_SELF, $_GET, $xoopsDB,$xoopsConfig;
        

        $sort = (!isset($_GET['sort']) || !isset($sort_codes[$_GET['sort']])) ? 'name_a' : $_GET['sort'];
        $tab_tmpl = array(
                        'left_text' => '<td width="100%%" align="left" valign="middle" class="tableh1_compact" style="white-space: nowrap"><b>'._AM_USERMGR_UONPAGE.'</b></td>'."\n",
                        'tab_header' => '',
                        'tab_trailer' => '',
                        'active_tab' => '<td><img src="../images/spacer.gif" width="1" height="1"></td>'."\n".'<td align="center" valign="middle" class="tableb_compact"><b>%d</b></td>',
                        'inactive_tab' => '<td><img src="../images/spacer.gif" width="1" height="1"></td>'."\n".'<td align="center" valign="middle" class="navmenu"><a href="'.$PHP_SELF.'?page=%d&op=showuser"<b>%d</b></a></td>'."\n"
        );

        $result=$xoopsDB->query("SELECT COUNT(DISTINCT owner_id) as owners FROM ".$xoopsDB->prefix("xcgal_pictures")." ");
        $nbEnr = $xoopsDB->fetchArray($result);
        $user_count = $nbEnr['owners'];
        $xoopsDB->freeRecordSet($result);
    $byte = _AM_GRPMGR_KB;
        if (!$user_count) redirect_header('index.php',2,_AM_USERMGR_NOUSER);

        $user_per_page = 25;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $lower_limit = ($page-1) * $user_per_page;
        $total_pages = ceil($user_count / $user_per_page);

        $sql =  "SELECT owner_id, COUNT(pid) as pic_count, ".
            "ROUND(SUM(total_filesize)/1024) as disk_usage ".
                        "FROM ".
                        //"LEFT JOIN ".$xoopsDB->prefix("xcgal_albums")." AS a ON category = ".FIRST_USER_CAT." + uid ".
                        "".$xoopsDB->prefix("xcgal_pictures")." ".
                        "GROUP BY owner_id ".
                        "LIMIT $lower_limit, $user_per_page";
        $sql2= "SELECT category, COUNT(aid) as alb FROM ".
               "".$xoopsDB->prefix("xcgal_albums")." WHERE category > ".FIRST_USER_CAT." ".
           "GROUP BY category ";

        $result = $xoopsDB->query($sql);
    $result2 = $xoopsDB->query($sql2);
    $albs= db_fetch_rowset($result2);
        $tabs = create_tabs($user_count, $page, $total_pages, $tab_tmpl);

        //starttable('100%');
        
        
    echo "<table border='0' cellpadding='0' cellspacing='1' width='100%' class='outer'><tr><th colspan='4'>"._AM_USERMGR_ULIST."</th></tr>";
        echo "<tr><td width=\"40%\" align=\"center\" class=\"head\">"._AM_USERMGR_USER."</td><td width=\"20%\" align=\"center\" class=\"head\">"._AM_USERMGR_ALBUMS."</td><td class=\"head\" align=\"center\" width=\"20%\">"._AM_USERMGR_PICS."</td><td class=\"head\" align=\"right\" width=\"20%\">"._AM_USERMGR_QUOTA."</td></tr>";

    $tdstyle ="even";
    $user_handler = icms::handler('icms_member');
        while($user = $xoopsDB->fetchArray($result)){
                $pic_owner =& $user_handler->getUser($user['owner_id']);
        if ($user['pic_count'] && is_object ($pic_owner)) {
                    $usr_link_start = '<a href="../index.php?cat='.($user['owner_id']+FIRST_USER_CAT).'" target="_blank">';
                        $usr_link_end = '</a>';
                        $ulink = "<a href=\"".ICMS_URL."/userinfo.php?uid={$user['owner_id']}\" target=\"_blank\">";
                        $ulink_end = "</a>";
                        $user['uname']= $pic_owner->uname();
                
                } else {
                        $usr_link_start = '';
                        $usr_link_end = '';
                        $ulink="";
                        $ulink_end="";
                        $user['uname']= $xoopsConfig['anonymous'];
                }
        if ($tdstyle== "even") $tdstyle = "odd";
        else $tdstyle = "even";
                echo <<< EOT
        <tr>
                <td class="{$tdstyle}" align="center">{$ulink}{$user['uname']}{$ulink_end}</td>
                <td class="{$tdstyle}" align="center">
EOT;
        foreach($albs as $alb){
            if ($user['owner_id'] == $alb['category']-FIRST_USER_CAT) echo $usr_link_start.$alb['alb'].$usr_link_end;
            }
        
        
        echo <<< EOT
        </td>
                <td class="{$tdstyle}" align="center"><a href="../thumbnails.php?album=usearch&suid={$user['owner_id']}" target="_blank">{$user['pic_count']}</a></td>
                <td class="{$tdstyle}" align="right">{$user['disk_usage']} {$byte}</td>
        </tr>

EOT;
        } // while
        $xoopsDB->freeRecordSet($result);
        echo <<<EOT
        <tr>
                <td colspan="8" class="foot">
                        <table width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                        $tabs
                                </tr>
                        </table>
                </td>
        </tr></table>

EOT;

        //endtable();
}
function list_deleted_users(){
    global $PHP_SELF, $_GET, $xoopsDB;
        global $CAT_LIST,$member_handler;
        
        get_subcat_data(0);
        $box= cat_list_box();
    $sql= "SELECT aid, title, category, pic_count ".
               "FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE category > ".FIRST_USER_CAT."";
           //"GROUP BY category";
        $result = $xoopsDB->query($sql);
        if (!$result) return;
        echo "<form method=\"post\" name=\"deluseralb\" action=\"{$PHP_SELF}\">";
        echo "<table border='0' cellpadding='0' cellspacing='1' width='100%' class='outer'><tr><th colspan='6'>"._AM_USERMGR_ULIST."</th></tr>";
        echo "<tr><td class=\"head\">"._AM_USERMGR_ALB."</td><td class=\"head\">"._AM_USERMGR_DELUID."</td><td class=\"head\" colspan=\"4\">"._AM_USERMGR_OPT."</td></tr>";

    $tdstyle ="even";
        while($deluser = $xoopsDB->fetchArray($result)){
            $deleted =& $member_handler->getUser(($deluser['category'] - FIRST_USER_CAT));
        if (!is_object($deleted)){
            if ($tdstyle== "even") $tdstyle = "odd";
            else $tdstyle = "even";
            $delid=$deluser['category'] - FIRST_USER_CAT;
            echo "<tr><td class=\"{$tdstyle}\"><a href=\"../thumbnails.php?album={$deluser['aid']}\">{$deluser['title']}</a></td><td class=\"{$tdstyle}\">{$delid}</td>";
            echo "<td class=\"{$tdstyle}\"><a href=\"../delete.php?id={$deluser['aid']}&what=album\">"._AM_USERMGR_DEL."</a></td>";
                    echo "<td class=\"{$tdstyle}\"><a href=\"../modifyalb.php?album={$deluser['aid']}\">"._AM_USERMGR_PROPS."</a></td>";
                    echo "<td class=\"{$tdstyle}\"><a href=\"../editpics.php?album={$deluser['aid']}\">"._AM_USERMGR_EDITP."</a></td>";
            echo "<td class=\"{$tdstyle}\"><input type=\"hidden\" name=\"album[]\" value=\"{$deluser['aid']}\" />{$box}</td></tr>";
           }
    }
    echo "<tr><td class=\"foot\" colspan=\"5\"></td><td class=\"foot\"><input type=\"hidden\" name=\"op\" value=\"movealb\" /><input type=\"submit\"></td></tr>";
    echo "</table></form>";
    
}
function movealb(){
    global $_POST, $xoopsDB;
    if (!isset($_POST['album']) || !is_array($_POST['album'])) return;
    $album_array = &$_POST['album'];
    foreach($album_array as $key => $value){
        $album_array[$key] = array();
        $album_array[$key][] = $value;
        $album_array[$key][] = $_POST['move'][$key];
    }
    foreach($album_array as $alb){
        if($alb[1] != -1) {
            $query = "UPDATE ".$xoopsDB->prefix("xcgal_albums")." SET category='".$alb[1]."' WHERE aid='".$alb[0]."' LIMIT 1";
                $update = $xoopsDB->query($query);
                
        }

    }

}
function get_subcat_data($parent, $ident='')
{
    global $CAT_LIST, $xoopsDB;

        $sql = "SELECT cid, name, description ".
                   "FROM ".$xoopsDB->prefix("xcgal_categories")." ".
                   "WHERE parent = '$parent' ".
                   "ORDER BY pos";
        $result = $xoopsDB->query($sql);

        if (($cat_count = $xoopsDB->getRowsNum($result)) > 0){
                $rowset = db_fetch_rowset($result);
                $pos=0;
                foreach ($rowset as $subcat){
                        if($pos>0){
                                $CAT_LIST[]=array(
                                        'cid' => $subcat['cid'],
                                        'parent' => $parent,
                                        'pos' => $pos++,
                                        'prev' => $prev_cid,
                                        'cat_count' => $cat_count,
                                        'name' => $ident.$subcat['name']);
                                $CAT_LIST[$last_index]['next'] = $subcat['cid'];
                        } else {
                                $CAT_LIST[]=array(
                                        'cid' => $subcat['cid'],
                                        'parent' => $parent,
                                        'pos' => $pos++,
                                        'cat_count' => $cat_count,
                                        'name' => $ident.$subcat['name']);
                        }
                        $prev_cid = $subcat['cid'];
                        $last_index = count($CAT_LIST)        -1;
                        get_subcat_data($subcat['cid'], $ident.'&nbsp;&nbsp;&nbsp;');
                }
        }
}
function cat_list_box()
{
        global $CAT_LIST, $PHP_SELF;
        $lb = <<< EOT
                        <select name="move[]" class="listbox">
EOT;
        $lb .= "<option value=\"-1\">"._AM_USERMGR_NOTMOVE."</option><option value=\"0\">"._AM_CAT_NOCAT."</option>\n";
        foreach($CAT_LIST as $category) if ($category['cid'] != 1 ) {
                $lb .= '                        <option value="'.$category['cid'].'">'.$category['name']."</option>\n";
        } 
        

        $lb .= <<<EOT
                        </select>

EOT;

        return $lb;
}
$op = isset($_GET['op']) ? $_GET['op'] : (isset($_POST['op']) ? $_POST['op'] : '');
icms_cp_header();

echo "<table><tr><td width='100px'><b><a href='index.php'>INDEX</a></b></td>
<td align='center'>
<b><a href='catmgr.php'>"._AM_CATMNGR."</a></b>&nbsp;::&nbsp;
<b><a href='usermgr.php'>"._AM_USERMNGR."</a></b>&nbsp;::&nbsp;
<b><a href='groupmgr.php'>"._AM_GROUPMNGR."</a></b><br />
<b><a href='searchnew.php'>"._AM_BATCHADD."</a></b>&nbsp;::&nbsp;
<b><a href='ecardmgr.php'>"._AM_ECARDMNGR."</a></b>&nbsp;::&nbsp;
<b><a href='../editpics.php?mode=upload_approval'>"._AM_PICAPP."</a></b>
</td></tr> </table>
<br /><hr />";

echo "<h4>"._AM_USERMGR_TITLE."</h4>";
echo"<table width='100%' border='0' cellspacing='1' class='outer'><tr><td class=\"odd\">";
echo " - <b><a href='usermgr.php?op=showuser'>"._AM_USERMGR_USHOW."</a></b><br /><br />\n";
echo " - <b><a href='usermgr.php?op=showdeluser'>"._AM_USERMGR_USHOWDEL."</a></b>";
echo "</td></tr></table>";
echo "<br /><br />\n";
switch($op){
case "showuser":
        list_users();
        break;
case "showdeluser":
                list_deleted_users();
        break;
case "movealb":
                movealb();
                list_deleted_users();
        break;

}
icms_cp_footer();
?>