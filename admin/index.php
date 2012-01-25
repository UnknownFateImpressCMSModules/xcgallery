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
$result=$xoopsDB->query("SELECT count(*) FROM ".$xoopsDB->prefix("xcgal_pictures")." WHERE approved = 'NO'");
$nbEnr = $xoopsDB->fetchArray($result);
$pic_count = $nbEnr['count(*)'];
if ($pic_count > 0) $pics= "<span style='color: #ff0000; font-weight: bold'>$pic_count</span>";
else $pics= "<span style='font-weight: bold'>$pic_count</span>";

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

echo "<h4>"._AM_CONFIG."</h4>";
echo"<table width='100%' border='0' cellspacing='1' class='outer'><tr><td class=\"odd\">";
echo " - <b><a href='".ICMS_URL.'/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod='.$xoopsModule->getVar('mid')."'>"._AM_GENERALCONF."</a></b><br /><br />\n";
echo " - <b><a href='catmgr.php'>"._AM_CATMNGR."</a></b>";
echo "<br /><br />\n";
echo " - <b><a href='usermgr.php'>"._AM_USERMNGR."</a></b>\n";
echo "<br /><br />\n";
echo " - <b><a href='groupmgr.php'>"._AM_GROUPMNGR."</a></b>\n";
echo "<br /><br />\n";
echo " - <b><a href='searchnew.php'>"._AM_BATCHADD."</a></b>\n";
echo "<br /><br />\n";
echo " - <b><a href='ecardmgr.php'>"._AM_ECARDMNGR."</a></b>\n";
echo "<br /><br />\n";
echo " - <b><a href='".ICMS_URL."/modules/".$xoopsModule->getVar('dirname')."/editpics.php?mode=upload_approval'>"._AM_PICAPP." ({$pics})</a></b>\n";
echo"</td></tr></table>";
icms_cp_footer();



?>