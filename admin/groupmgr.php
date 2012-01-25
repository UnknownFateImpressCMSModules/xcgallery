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

//$member_handler = icms::handler('icms_member');
$member_handler = icms::handler('icms_member');
$groups =&$member_handler->getGroups();
function synchronize(){
        global $xoopsDB, $groups,$member_handler;
            $group_exist2=array();
        foreach ($groups as $gr){
            $group_exist2[$gr->getVar('groupid')] = FALSE;
        }
        $result = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xcgal_usergroups")." WHERE 1 ORDER BY group_id");

      while($myrow = $xoopsDB->fetchArray($result)) {
            $group_exist1 = FALSE;
            //var_dump($myrow['xgroupid']);
            foreach ($groups as $group){
                if ($myrow['xgroupid'] == $group->getVar('groupid')){
                    if ($myrow['group_name']!= $group->getVar('name')){
                        $xoopsDB->query("UPDATE ".$xoopsDB->prefix("xcgal_usergroups")." SET group_name = '".$group->getVar('name')."' WHERE xgroupid = '".(int)$myrow['xgroupid']."'");
                    }
                    $group_exist1 = TRUE;
                    $group_exist2[$group->getVar('groupid')] = TRUE;
                }
                
            }
            if (!$group_exist1){
                $xoopsDB->query("DELETE FROM ".$xoopsDB->prefix("xcgal_usergroups")." WHERE xgroupid = '".(int)$myrow['xgroupid']."'");
                }
      }
     // for ($i = 0; $i < count($group_exist2); $i++) {
        foreach($group_exist2 as $key=>$value){
          if (!$value){
              $group = &$member_handler->getGroup($key);
              $result=$xoopsDB->queryF("INSERT INTO ".$xoopsDB->prefix("xcgal_usergroups")." VALUES (".$key.", '".$group->getVar('name')."', 1024, 0, 1, 1, 1, 1,1,1, 1, ".$key.")");
              }
              
      }
      

}
function display_group_list()
{
        global $xoopsDB, $tdstyle,$buffer,$groups;
        
        $count = count($groups);
        $result = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xcgal_usergroups")." WHERE 1 ORDER BY group_id");
        if (!$xoopsDB->getRowsNum($result)) {
            
            //print_r($groups);
            for ($i = 0; $i < $count; $i++) {
                $id = $groups[$i]->getVar('groupid');
                if (XOOPS_GROUP_ADMIN == $id ){
                $result=$xoopsDB->queryF("INSERT INTO ".$xoopsDB->prefix("xcgal_usergroups")." VALUES (1, '".$groups[$i]->getVar('name')."', 0, 1, 1, 1, 1, 1, 1, 0, 0,1)");
            }
            elseif (XOOPS_GROUP_USERS == $id){
                $result=$xoopsDB->queryF("INSERT INTO ".$xoopsDB->prefix("xcgal_usergroups")." VALUES (2, '".$groups[$i]->getVar('name')."', 1024, 0, 1, 1, 1, 1, 1,1,0, 2)");

            }
            elseif (XOOPS_GROUP_ANONYMOUS == $id) {
                $result=$xoopsDB->queryF("INSERT INTO ".$xoopsDB->prefix("xcgal_usergroups")." VALUES (3, '".$groups[$i]->getVar('name')."', 0, 0, 0, 0, 1, 0, 0 ,1,1,3)");

            }
            else {
                $result=$xoopsDB->queryF("INSERT INTO ".$xoopsDB->prefix("xcgal_usergroups")." VALUES (".$id.", '".$groups[$i]->getVar('name')."', 1024, 0, 1, 1, 1, 1,1,1, 1, ".$id.")");

            }
            
        }

            redirect_header('groupmgr.php',2,_AM_GRPMGR_EMPTY);
           exit;
        }

        $field_list = array('can_rate_pictures', 'can_send_ecards', 'can_post_comments', 'can_upload_pictures', 'pub_upl_need_approval', 'can_create_albums', 'priv_upl_need_approval');
    $tdstyle ="even";
    echo $buffer;
        while($group=$xoopsDB->fetchArray($result)){
                $group['group_name'] = $group['group_name'];
        if ($tdstyle== "even") $tdstyle = "odd";
        else $tdstyle = "even";
                        echo <<< EOT
        <tr>


                <td class="{$tdstyle}">
                        <input type="hidden" name="group_id[]" value="{$group['group_id']}">
                        {$group['group_name']}
                </td>
                <td class="{$tdstyle}" style="white-space: nowrap;">
                        <input type="text" name="group_quota_{$group['group_id']}" value="{$group['group_quota']}" size="10" class="textinput">
EOT;
echo _AM_GRPMGR_KB."</td>";


                foreach ($field_list as $field_name){
                        $value = $group[$field_name];
                        $yes_selected = ($value == 1) ? 'selected="selected"' : '';
                        $no_selected  = ($value == 0) ? 'selected="selected"' : '';
                        echo <<< EOT
                <td class="{$tdstyle}" align="center">
                        <select name="{$field_name}_{$group['group_id']}" class="listbox">
EOT;
            echo "<option value='1' {$yes_selected}>"._YES."</option>";
            echo "<option value='0' {$no_selected}>"._NO."</option></select></td>";
                }
                echo "</tr>";
        } // while
        $xoopsDB->freeRecordSet($result);
}

function get_post_var($var)
{
        global $_POST;

        if(!isset($_POST[$var])) redirect_header('index.php',2,_AM_PARAM_MISSING." ($var)");
        return $_POST[$var];
}

function process_post_data()
{
        global $_POST, $xoopsDB;

        $field_list = array('group_quota', 'can_rate_pictures', 'can_send_ecards', 'can_post_comments', 'can_upload_pictures', 'pub_upl_need_approval', 'can_create_albums', 'priv_upl_need_approval');

        $group_id_array = get_post_var('group_id');
        foreach ($group_id_array as $key => $group_id){
                $set_statment = '';
                foreach ($field_list as $field){
                        if (!isset($_POST[$field.'_'.$group_id])) redirect_header('groupmgr.php',2,_AM_PARAM_MISSING." ({$field}_{$group_id})");
                        if ($field == 'group_name') {
                            $set_statment .= $field ."='".addslashes($_POST[$field.'_'.$group_id])."',";
                        } else {
                                $set_statment .= $field ."='".(int)$_POST[$field.'_'.$group_id]."',";
                        }
                }
                $set_statment = substr($set_statment, 0, -1);
                $xoopsDB->query("UPDATE ".$xoopsDB->prefix("xcgal_usergroups")." SET $set_statment WHERE group_id = '$group_id' LIMIT 1");
        }
}

if (isset($_POST) && count($_POST)) {
    if (isset($_POST['synchronize'])) {
           synchronize();
        } elseif (isset($_POST['apply_modifs'])) {
                process_post_data();
        }
}
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

ob_start();
echo "<table border='0' cellpadding='0' cellspacing='1' width='100%' class='outer'>
    <tr><th colspan='9'>"._AM_GRPMGR_MANAGE."</th></tr>
    <tr>
                <td class='head'>"._AM_GRPMGR_NAME."</td>
                <td class='head'>"._AM_GRPMGR_QUOTA."</td>
                <td class='head' align='center'>"._AM_GRPMGR_RATE."</td>
                <td class='head' align='center'>"._AM_GRPMGR_SENDCARD."</td>
                <td class='head' align='center'>"._AM_GRPMGR_COM."</td>
                <td class='head' align='center'>"._AM_GRPMGR_UPLOAD."</td>
                <td class='head' align='center'>"._AM_GRPMGR_PUB_APPR."</td>
                <td class='head' align='center'>"._AM_GRPMGR_PRIVATE."</td>
                <td class='head' align='center'>"._AM_GRPMGR_PRIV_APPR."</td>
        </tr>
        <form method=\"post\" action=\"{$PHP_SELF}\">";


$buffer = ob_get_contents();
ob_end_clean();

display_group_list();
if ($tdstyle == "even") $tdstyle = "odd";
else $tdsytle = "even";
echo "<tr><td colspan='9' class='{$tdstyle}'>";
echo "<b>"._AM_GRPMGR_NOTES."</b></td></tr>";
echo "<tr><td colspan='9' class='{$tdstyle}'>"._AM_GRPMGR_PUB_NOTE."</td></tr>";
echo "<tr><td colspan='9' class='{$tdstyle}'>"._AM_GRPMGR_PRIV_NOTE."</td></tr>";
echo "<tr><td colspan='9' class='{$tdstyle}'>"._AM_GRPMGR_SYN_NOTE."</td></tr>";
echo "<tr><td colspan='9' align='center' class='foot'>";
echo "<input type=\"submit\" name=\"apply_modifs\" value=\""._AM_GRPMGR_APPLY."\" class=\"button\" />&nbsp;&nbsp;&nbsp;";
echo "<input type=\"submit\" name=\"synchronize\" value=\""._AM_GRPMGR_SYN."\" class=\"button\" />&nbsp;&nbsp;&nbsp;";
echo "</td></form></tr></table>";
icms_cp_footer();

?>