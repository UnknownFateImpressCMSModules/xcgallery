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
$myts =& MyTextSanitizer::getInstance(); // MyTextSanitizer object
// Fix categories that have an invalid parent
function fix_cat_table()
{
    global $xoopsDB;

        $result = $xoopsDB->query("SELECT cid FROM ".$xoopsDB->prefix("xcgal_categories")." WHERE 1");
        if ($xoopsDB->getRowsNum($result) > 0){
                $set = '';
                while($row = $xoopsDB->fetchArray($result)) $set .= $row['cid'] . ',';
                $set = '('.substr($set, 0, -1).')';
                $sql = "UPDATE ".$xoopsDB->prefix("xcgal_categories")." ".
                           "SET parent = '0' ".
                           "WHERE parent=cid OR parent NOT IN $set";
                $result = $xoopsDB->queryf($sql);
        }
}

function get_subcat_data($parent, $ident='')
{
    global $CAT_LIST, $xoopsDB, $myts;

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
                                        'name' => $ident.icms_core_DataFilter::htmlSpecialchars($subcat['name']));
                                $CAT_LIST[$last_index]['next'] = $subcat['cid'];
                        } else {
                                $CAT_LIST[]=array(
                                        'cid' => $subcat['cid'],
                                        'parent' => $parent,
                                        'pos' => $pos++,
                                        'cat_count' => $cat_count,
                                        'name' => $ident.icms_core_DataFilter::htmlSpecialchars($subcat['name']));
                        }
                        $prev_cid = $subcat['cid'];
                        $last_index = count($CAT_LIST)        -1;
                        get_subcat_data($subcat['cid'], $ident.'&nbsp;&nbsp;&nbsp;');
                }
        }
}

function update_cat_order()
{
        global $CAT_LIST, $xoopsDB;

        foreach ($CAT_LIST as $category)
                $xoopsDB->queryf("UPDATE ".$xoopsDB->prefix("xcgal_categories")." SET pos='{$category['pos']}' WHERE cid = '{$category['cid']}' LIMIT 1");
}

function cat_list_box($highlight=0, $curr_cat, $on_change_refresh = true)
{
        global $CAT_LIST, $PHP_SELF, $myts;

        if($on_change_refresh){
                $lb = <<< EOT
                        <select onChange="if(this.options[this.selectedIndex].value) window.location.href='$PHP_SELF?op=setparent&cid=$curr_cat&parent='+this.options[this.selectedIndex].value;"  name="parent" class="listbox">

EOT;
        } else {
                $lb = <<< EOT
                        <select name="parent" class="listbox">

EOT;
        }
        $lb .= '                        <option value="0"'.($highlight == 0 ? ' selected="selected"': '').">"._AM_CAT_NOCAT."</option>\n";
        foreach($CAT_LIST as $category) if ($category['cid'] != 1 && $category['cid'] != $curr_cat) {
                $lb .= '                        <option value="'.$category['cid'].'"'.($highlight == $category['cid'] ? ' selected="selected"': '').">".$category['name']."</option>\n";
        } elseif ($category['cid'] != 1 && $category['cid'] == $curr_cat){
                $lb .= '                        <option value="'.$category['parent'].'"'.($highlight == $category['cid'] ? ' selected="selected"': '').">".$category['name']."</option>\n";
        }

        $lb .= <<<EOT
                        </select>

EOT;

        return $lb;
}

function display_cat_list()
{
        global $CAT_LIST, $PHP_SELF, $myts;

        $CAT_LIST3 = $CAT_LIST;

        foreach ($CAT_LIST3 as $key => $category){
                echo "        <tr>\n";
                echo '                <td class="even" width="80%"><b>'.$category['name'].'</b></td>'."\n";

                if ($category['pos']>0) {
                        echo '                <td class="odd" width="4%"><a href="'.$PHP_SELF.'?op=move&cid1='.$category['cid'].'&pos1='.($category['pos']-1).'&cid2='.$category['prev'].'&pos2='.($category['pos']).'">'.'<img src="../images/up.gif"  border="0">'.'</a></td>'."\n";
                } else {
                        echo '                <td class="odd" width="4%">'.'&nbsp;'.'</td>'."\n";
                }

                if ($category['pos'] < $category['cat_count']-1) {
                        echo '                <td class="odd" width="4%"><a href="'.$PHP_SELF.'?op=move&cid1='.$category['cid'].'&pos1='.($category['pos']+1).'&cid2='.$category['next'].'&pos2='.($category['pos']).'">'.'<img src="../images/down.gif"  border="0">'.'</a></td>'."\n";
                } else {
                        echo '                <td class="odd" width="4%">'.'&nbsp;'.'</td>'."\n";
                }

                if ($category['cid'] != 1) {
                        echo '                <td class="odd" width="4%"><a href="'.$PHP_SELF.'?op=deletecat&cid='.$category['cid'].'" onClick="return confirmDel(\''.str_replace('&nbsp;','', icms_core_DataFilter::htmlSpecialchars($category['name'])).'\')">'.'<img src="../images/delete.gif"  border="0">'.'</a></td>'."\n";
                } else {
                        echo '                <td class="odd" width="4%">'.'&nbsp;'.'</td>'."\n";
                }

                echo '                <td class="odd" width="4%">'.'<a href="'.$PHP_SELF.'?op=editcat&cid='.$category['cid'].'">'.'<img src="../images/edit.gif" border="0">'.'</a></td>'."\n";
                echo '                <td class="odd" width="4%">'."\n".cat_list_box($category['parent'], $category['cid'])."\n".'</td>'."\n";
                echo "        </tr>\n";
        }
}

$op = isset($_GET['op']) ? $_GET['op'] : '';
$current_category = array('cid' => '0', 'name'=>'', 'parent' => '0', 'description'=>'');

switch($op){
        case 'move':
        if (!isset($_GET['cid1']) || !isset($_GET['cid2']) || !isset($_GET['pos1']) || !isset($_GET['pos2'])) redirect_header('index.php',2,sprintf(_AM_CAT_MISS_PARAM, 'move'));

        $cid1 = (int)$_GET['cid1'];
        $cid2 = (int)$_GET['cid2'];
        $pos1 = (int)$_GET['pos1'];
        $pos2 = (int)$_GET['pos2'];

        $xoopsDB->queryf("UPDATE ".$xoopsDB->prefix("xcgal_categories")." SET pos='$pos1' WHERE cid = '$cid1' LIMIT 1");
        $xoopsDB->queryf("UPDATE ".$xoopsDB->prefix("xcgal_categories")." SET pos='$pos2' WHERE cid = '$cid2' LIMIT 1");
        break;

        case 'setparent':
        if (!isset($_GET['cid']) || !isset($_GET['parent'])) redirect_header('index.php',2,sprintf(_AM_CAT_MISS_PARAM, 'setparent'));

        $cid    = (int)$_GET['cid'];
        $parent = (int)$_GET['parent'];

        $xoopsDB->queryf("UPDATE ".$xoopsDB->prefix("xcgal_categories")." SET parent='$parent', pos='-1' WHERE cid = '$cid' LIMIT 1");
        break;

        case 'editcat':
        if (!isset($_GET['cid'])) redirect_header('index.php',2,sprintf(_AM_CAT_MISS_PARAM, 'editcat'));

        $cid    = (int)$_GET['cid'];
        $result = $xoopsDB->query("SELECT cid, name, parent, description FROM ".$xoopsDB->prefix("xcgal_categories")." WHERE cid = '$cid' LIMIT 1");

        if(!$xoopsDB->getRowsNum($result)) redirect_header('index.php',2,_AM_CAT_UNKOWN);
        $current_category = $xoopsDB->fetchArray($result);
        break;

        case 'updatecat':
        if (!isset($_POST['cid']) || !isset($_POST['parent']) || !isset($_POST['name']) || !isset($_POST['description'])) redirect_header('index.php',2,_AM_CAT_MISS_PARAM, 'updatecat');

        $cid    = (int)$_POST['cid'];
        $parent = (int)$_POST['parent'];
        $name = trim($_POST['name']) ? $myts->addSlashes($_POST['name']) : '&lt;???&gt;';
        $description = $myts->makeTareaData4Save($_POST['description']);

        $xoopsDB->queryf("UPDATE ".$xoopsDB->prefix("xcgal_categories")." SET parent='$parent', name='$name', description='$description' WHERE cid = '$cid' LIMIT 1");
        break;

        case 'createcat':
        if (!isset($_POST['parent']) || !isset($_POST['name']) || !isset($_POST['description'])) redirect_header('index.php',2,_AM_CAT_MISS_PARAM, 'createcat');

        $parent = (int)$_POST['parent'];
        $name = trim($_POST['name']) ? $myts->addSlashes($_POST['name']) : '&lt;???&gt;';
        $description = $myts->makeTareaData4Save($_POST['description']);

        $xoopsDB->queryF("INSERT INTO ".$xoopsDB->prefix("xcgal_categories")." (pos, parent, name, description) VALUES ('10000', '$parent', '$name', '$description')");
        break;

        case 'deletecat':
        if (!isset($_GET['cid'])) redirect_header('index.php',2,sprintf(_AM_CAT_MISS_PARAM, 'deletecat'));

        $cid    = (int)$_GET['cid'];

        $result = $xoopsDB->query("SELECT parent FROM ".$xoopsDB->prefix("xcgal_categories")." WHERE cid = '$cid' LIMIT 1");
        if($cid == 1) redirect_header('index.php',2,_AM_CAT_UGAL_CAT_RO);
        if(!$xoopsDB->getRowsNum($result)) redirect_header('index.php',2, _AM_CAT_UNKOWN);
        $del_category = $xoopsDB->fetchArray($result);
        $parent = $del_category['parent'];
        $result = $xoopsDB->queryf("UPDATE ".$xoopsDB->prefix("xcgal_categories")." SET parent='$parent' WHERE parent = '$cid'");
        $result = $xoopsDB->queryf("UPDATE ".$xoopsDB->prefix("xcgal_albums")." SET category='$parent' WHERE category = '$cid'");
        $result = $xoopsDB->queryf("DELETE FROM ".$xoopsDB->prefix("xcgal_categories")." WHERE cid='$cid' LIMIT 1");
        break;
}

fix_cat_table();
get_subcat_data(0);
update_cat_order();
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

echo "

<script language=\"javascript\">
function confirmDel(catName)
{
    return confirm(\""._AM_CAT_CONF_DEL." (\" + catName + \") ?\");
}
</script>";


//starttable('100%');

echo "
        <table border='0' cellpadding='0' cellspacing='1' width='100%' class='outer'>
    <tr><th colspan='6'>"._AM_CAT_MNGCAT."</th></tr>
    <tr>
                <td class='head'><b><span class='statlink'>"._AM_CAT_CAT."&nbsp;</span></b></td>
                <td colspan='4' class='head' align='center'><b><span class='statlink'>"._AM_CAT_OP."&nbsp;</span></b></td>
                <td class='head' align='center'><b><span class='statlink'>"._AM_CAT_MOVE."&nbsp;</span></b></td>
        </tr>
        <form method='get' action='$PHP_SELF'>";


display_cat_list();
echo "</form>";
echo "</table><br />\n";

$lb = cat_list_box($current_category['parent'], $current_category['cid'], false);
$op = $current_category['cid'] ? 'updatecat' : 'createcat';
$current_category['name']= icms_core_DataFilter::htmlSpecialchars($current_category['name']);
$current_category['description']= icms_core_DataFilter::htmlSpecialchars($current_category['description']);
echo <<<EOT
    <table border='0' cellpadding='0' cellspacing='1' width='100%' class='outer'>
    <tr><th colspan="2">
EOT;
echo _AM_CAT_UPCR;
echo <<<EOT
</th></tr>
        <form method="post" action="$PHP_SELF?op=$op">
        <input type="hidden" name="cid" value ="{$current_category['cid']}">
        <tr>
            <td width="40%" class="even">
EOT;
echo _AM_CAT_PARENT;
echo <<<EOT
        </td>
        <td width="60%" class="odd" valign="top">
                $lb
                </td>
        </tr>
        <tr>
            <td width="40%" class="even">
EOT;
echo _AM_CAT_TITLE;
echo <<<EOT
        </td>
        <td width="60%" class="odd" valign="top">
                <input type="text" style="width: 100%" name="name" value="{$current_category['name']}" class="textinput">
                </td>
        </tr>
        <tr>
                <td class="even" valign="top">
EOT;
echo _AM_CAT_DESC;
echo <<<EOT
                </td>
                <td class="odd" valign="top">
                        <textarea name="description" ROWS="5" COLS="40" SIZE="9"  WRAP="virtual" STYLE="WIDTH: 100%;" class="textinput">{$current_category['description']}</textarea>
                </td>
        </tr>
        <tr>
                <td colspan="2" align="center" class="foot">
EOT;
echo "<input type=\"submit\" value=\""._AM_CAT_UPCR."\" class=\"button\">";
echo "</td></form></tr></table>";

//endtable();
//pagefooter();
//ob_end_flush();
icms_cp_footer();
?>