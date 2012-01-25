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
$xcgalDir = basename(dirname(dirname(__FILE__)));
function delete_cards($e_ids){
global $xoopsDB;
   
    foreach($e_ids as $e_id){
        $xoopsDB->query("DELETE FROM ".$xoopsDB->prefix("xcgal_ecard")." WHERE e_id = '".$e_id."'");
    }

}

$card_per_page= 25;
$delete_time = time() - ($xoopsModuleConfig['ecards_saved_db'] * 86400);
$xoopsDB->queryf("DELETE from ".$xoopsDB->prefix("xcgal_ecard")." WHERE s_time < ".$delete_time."");


if(isset($_POST['card_action'])){
    if ($_POST['card_action'] == 1){
        if (isset($_POST['ecard']) && is_array($_POST['ecard'])){
            $ecard_array = &$_POST['ecard'];
            delete_cards($ecard_array);
        }
    } elseif ($_POST['card_action'] == 2){
        $xoopsDB->query("DELETE FROM ".$xoopsDB->prefix("xcgal_ecard")."");
    } elseif ($_POST['card_action'] == 3){
        $xoopsDB->query("DELETE FROM ".$xoopsDB->prefix("xcgal_ecard")." WHERE picked=1");
    } elseif ($_POST['card_action'] == 4){
        $xoopsDB->query("DELETE FROM ".$xoopsDB->prefix("xcgal_ecard")." WHERE picked=0");
    }
}
$tab_tmpl = array(
        'left_text' => '<td width="100%%" align="left" valign="middle" class="tableh1_compact" style="white-space: nowrap"><b>'._AM_CARDMGR_CONPAGE.'</b></td>'."\n",
        'tab_header' => '',
        'tab_trailer' => '',
        'active_tab' => '<td><img src="../images/spacer.gif" width="1" height="1"></td>'."\n".'<td align="center" valign="middle" class="tableb_compact"><b>%d</b></td>',
        'inactive_tab' => '<td><img src="../images/spacer.gif" width="1" height="1"></td>'."\n".'<td align="center" valign="middle" class="navmenu"><a href="'.$PHP_SELF.'?page=%d"><b>%d</b></a></td>'."\n"
        );
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$lower_limit = ($page-1) * $card_per_page;
$result=$xoopsDB->query("SELECT count(*) FROM ".$xoopsDB->prefix("xcgal_ecard")." WHERE 1");
$nbEnr = $xoopsDB->fetchArray($result);
$card_count = $nbEnr['count(*)'];
$xoopsDB->freeRecordSet($result);
$total_pages = ceil($card_count / $card_per_page);
$tabs = create_tabs($card_count, $page, $total_pages, $tab_tmpl);
$result=$xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xcgal_ecard")." ORDER BY s_time DESC LIMIT $lower_limit, $card_per_page");

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

echo "<form method=\"post\" name=\"ecard\" action=\"{$PHP_SELF}\">";
echo "<table border='0' cellpadding='0' cellspacing='1' width='100%' class='outer'><tr><th colspan='8'>"._AM_CARDMGR_TITLE."</th></tr>";
echo "<tr><td class=\"head\"><input name='allbox' id='allbox' onclick='xoopsCheckAll(\"ecard\", \"allbox\");' type='checkbox' value='Check All' /></td><td class=\"head\">"._AM_CARDMGR_TIME."</td><td class=\"head\">"._AM_CARDMGR_SUNAME."</td><td class=\"head\">"._AM_CARDMGR_SEMAIL."</td><td class=\"head\">"._AM_CARDMGR_SIP."</td><td class=\"head\">"._AM_CARDMGR_PID."</td><td class=\"head\">"._AM_CARDMGR_STATUS."</td></tr>";
$tdstyle ="even";
$user_handler = icms::handler('icms_member');
while($row = $xoopsDB->fetchArray($result)){
    if ($tdstyle== "even") $tdstyle = "odd";
    else $tdstyle = "even";
    echo "<tr><td class=\"$tdstyle\"><input type='checkbox' id='ecard[]' name='ecard[]' value='".$row['e_id']."' /></td><td class=\"$tdstyle\">".formatTimestamp($row['s_time'],'m')."</td>";
    if ($row['sender_uid']> 0 ){
        $sender =& $user_handler->getUser($row['sender_uid']);
        echo "<td class=\"$tdstyle\"><a href=\"".ICMS_URL."/userinfo.php?uid=".$row['sender_uid']."\" target=\"_blank\">".$sender->uname()."</a></td>";
    } else echo "<td class=\"$tdstyle\">".$xoopsConfig['anonymous']."</td>";
    echo "<td class=\"$tdstyle\">".$row['sender_email']."</td><td class=\"$tdstyle\">".$row['sender_ip']."</td>";
    echo "<td class=\"$tdstyle\"><a href=\"".ICMS_URL."/modules/".$xcgalDir."/displayimage.php?pid=".$row['pid']."\" target=\"_blank\">".$row['pid']."</a></td>";
    if ($row['picked'] == 0) $picked = _NO;
    else $picked = _YES;
    echo "<td class=\"$tdstyle\">$picked</td></tr>";

}
echo "<tr><td colspan=\"7\" class=\"foot\"><table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\"><tr>$tabs</tr></table></td></tr>";
echo "</table>";
echo "<table align=\"center\">";
echo "<tr><td><select name=\"card_action\">";
echo "<option value=\"1\">"._AM_CARDMGR_DEL_SELECTED."</option>";
echo "<option value=\"2\">"._AM_CARDMGR_DEL_ALL."</option>";
echo "<option value=\"3\">"._AM_CARDMGR_DEL_PICKED."</option>";
echo "<option value=\"4\">"._AM_CARDMGR_DEL_UNPICKED."</option>";
echo "</select>";
echo "</td><td><input type=\"submit\" /></td></tr>";
echo "</table>";
echo "</form>";

icms_cp_footer();



?>