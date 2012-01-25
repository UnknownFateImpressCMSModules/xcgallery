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
include '../../mainfile.php';
define('IN_XCGALLERY', true);
require('include/init.inc.php');
$com_itemid = isset($_GET['com_itemid']) ? intval($_GET['com_itemid']) : (isset($_POST['com_itemid']) ? intval($_POST['com_itemid']) : 0);

if ($com_itemid > 0) {
    $sql="SELECT a.comments FROM ".$xoopsDB->prefix("xcgal_albums")." as a, ".$xoopsDB->prefix("xcgal_pictures")." as p WHERE a.aid=p.aid AND p.pid=".$com_itemid."";
        $result = $xoopsDB->query($sql);

    $CURRENT_ALBUM_DATA = $xoopsDB->fetchArray($result);
}
if (USER_CAN_POST_COMMENTS && $CURRENT_ALBUM_DATA['comments'] == 'YES') include ICMS_ROOT_PATH.'/include/comment_post.php';
else redirect_header('index.php',2,"You aren't allowed to post comments for this pic.");

?>
