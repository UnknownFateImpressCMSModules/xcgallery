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
$alb_path= ICMS_ROOT_PATH."/modules/".$xcgalDir."/";
$myts =& MyTextSanitizer::getInstance(); // MyTextSanitizer object
/**************************************************************************
 * Local functions definition
 **************************************************************************/

/**
 * albumselect()
 *
 * return the HTML code for a listbox with name $id that contains the list
 * of all albums
 *
 * @param string $id the name of the listbox
 * @return the HTML code
 **/
function albumselect($id="album")
{
        global $xoopsDB, $myts;
        static $select = "";

        if ($select == ""){
                $sql = "SELECT aid, title, category ".
               "FROM ".$xoopsDB->prefix("xcgal_albums")." ".
                           "ORDER BY title";
                $result = $xoopsDB->query($sql);
        $user_handler = icms::handler('icms_member');
                while ($row = $xoopsDB->fetchArray($result)) {
                    $alb_owner =& $user_handler->getUser($row['category']-FIRST_USER_CAT);
            if (is_object ($alb_owner)) $row["title"]= "- (".$alb_owner->uname().")".$row["title"];
                        $select .= "<option value=\"" . $row["aid"] . "\">" . icms_core_DataFilter::htmlSpecialchars($row["title"]) . "</option>";
                }
                $xoopsDB->freeRecordSet($result);
        }

        return "<select name=\"$id\" class=\"listbox\">".$select."</select>";
}

/**
 * dirheader()
 *
 * return the HTML code for the row to be displayed when we start a new
 * directory
 *
 * @param $dir the directory
 * @param $dirid the name of the listbox that will list the albums
 * @return the HTML code
 **/
function dirheader($dir, $dirid)
{
        global $xoopsModuleConfig;
        $xcgalDir = basename(dirname(dirname(__FILE__)));
        $warning = '';

        if (!is_writable(ICMS_ROOT_PATH."/modules/".$xcgalDir."/".$xoopsModuleConfig['fullpath'].$dir))
                $warning ="<tr><td class=\"even\" valign=\"middle\" colspan=\"3\">\n".
                        "<div class=\"errorMsg\"><b>"._AM_SRCHNEW_WARNING."</b>: "._AM_SRCHNEW_CHG_PERM."</div></td></tr>\n";
        return "<tr><td class=\"head\" valign=\"middle\" colspan=\"3\">\n".
                        sprintf(_AM_SRCHNEW_TARGET_ALB, $dir, albumselect($dirid))."</td></tr>\n".$warning;
}

/**
 * picrow()
 *
 * return the HTML code for a row to be displayed for an image
 * the row contains a checkbox, the image name, a thumbnail
 *
 * @param $picfile the full path of the file that contains the picture
 * @param $picid the name of the check box
 * @return the HTML code
 **/
function picrow($picfile, $picid, $albid)
{
        global $xoopsModuleConfig, $expic_array;
        $xcgalDir = basename(dirname(dirname(__FILE__)));

        $encoded_picfile = base64_encode($picfile);
        $picname = ICMS_ROOT_PATH."/modules/".$xcgalDir."/".$xoopsModuleConfig['fullpath'].$picfile;
        $pic_url = urlencode($picfile);
        $picpath = ICMS_URL."/modules/".$xcgalDir."/".$xoopsModuleConfig['fullpath'].$picfile;
        $pic_fname = basename($picfile);

        $thumb_file = dirname($picname).'/'.$xoopsModuleConfig['thumb_pfx'].$pic_fname;
        if(file_exists($thumb_file)){
                $thumb_info = getimagesize($picname);
                $thumb_size = compute_img_size($thumb_info[0], $thumb_info[1], 48);
                $img ='<img src="'.$picpath.'" '.$thumb_size['geom'].' class="thumbnail" border="0">';
        } else {
                $img ='<img src="../showthumb.php?picfile='.$pic_url.'&size=48" class="thumbnail" border="0">';
        }

        if (filesize($picname) && is_readable($picname)) {
                $fullimagesize = getimagesize($picname);
                $winsizeX = ($fullimagesize[0] + 16);
                $winsizeY = ($fullimagesize[1] + 16);

                $checked = isset($expic_array[$picfile]) || !$fullimagesize ? '' : 'checked="checked"';

         if (($checked == '') && ($xoopsModuleConfig['batch_all'] == 0)) {
/*            return <<<EOT
                   <tr>
                       <td class='odd' valign='middle'>
                           <input name='pics[]' type='checkbox' value='$picid' $checked>
                           <input name='album_lb_id_$picid' type='hidden' value="$albid">
                           <input name="picfile_$picid" type="hidden" value="$encoded_picfile">
                       </td>
                       <td class="even" valign="middle" width="100%">
                           <a href="javascript:;" onClick= "MM_openBrWindow('../displayimage.php?&fullsize=1&picfile=$pic_url', 'ImageViewer', 'toolbar=yes, status=yes, resizable=yes, width=$winsizeX, height=$winsizeY')">$pic_fname</a>
                       </td>
                       <td class="odd" valign="middle" align="center">
                           <a href="javascript:;" onClick= "MM_openBrWindow('../displayimage.php?&fullsize=1&picfile=$pic_url', 'ImageViewer', 'toolbar=yes, status=yes, resizable=yes, width=$winsizeX, height=$winsizeY')">$img<br /></a>
                       </td>
                   </tr>
EOT;     */
          }
        elseif ($xoopsModuleConfig['batch_all'] == 1){
            return <<<EOT
                   <tr>
                       <td class='odd' valign='middle'>
                           <input name='pics[]' type='checkbox' value='$picid' $checked>
                           <input name='album_lb_id_$picid' type='hidden' value="$albid">
                           <input name="picfile_$picid" type="hidden" value="$encoded_picfile">
                       </td>
                       <td class="even" valign="middle" width="100%">
                           <a href="javascript:;" onClick= "MM_openBrWindow('../displayimage.php?&fullsize=1&picfile=$pic_url', 'ImageViewer', 'toolbar=yes, status=yes, resizable=yes, width=$winsizeX, height=$winsizeY')">$pic_fname</a>
                       </td>
                       <td class="odd" valign="middle" align="center">
                           <a href="javascript:;" onClick= "MM_openBrWindow('../displayimage.php?&fullsize=1&picfile=$pic_url', 'ImageViewer', 'toolbar=yes, status=yes, resizable=yes, width=$winsizeX, height=$winsizeY')">$img<br /></a>
                       </td>
                   </tr>
EOT;
        }
        } else {
                $winsizeX = (300);
                $winsizeY = (300);
                return <<<EOT
        <tr>
                <td class="odd" valign="middle">
                        &nbsp;
                </td>
                <td class="even" valign="middle" width="100%">
                        <i>$pic_fname</i>
                </td>
                <td class="odd" valign="middle" align="center">
                        <a href="javascript:;" onClick= "MM_openBrWindow('displayimage.php?&fullsize=1&picfile=$pic_url', 'ImageViewer', 'toolbar=yes, status=yes, resizable=yes, width=$winsizeX, height=$winsizeY')"><img src="showthumb.php?picfile=$pic_url&size=48" class="thumbnail" border="0"><br /></a>
                </td>
        </tr>
EOT;
        }
}

/**
 * getfoldercontent()
 *
 * return the files and directories of a folder in two arrays
 *
 * @param $folder the folder to read
 * @param $dir_array the array that will contain name of sub-dir
 * @param $pic_array the array that will contain name of picture
 * @param $expic_array  an array that contains pictures already in db
 * @return
 **/
function getfoldercontent($folder, &$dir_array, &$pic_array, &$expic_array)
{
        global $xoopsModuleConfig;
        $xcgalDir = basename(dirname(dirname(__FILE__)));

        $dir = opendir(ICMS_ROOT_PATH."/modules/".$xcgalDir."/".$xoopsModuleConfig['fullpath'].$folder);
        while($file = readdir($dir)){
                if(is_dir(ICMS_ROOT_PATH."/modules/".$xcgalDir."/".$xoopsModuleConfig['fullpath'].$folder.$file)) {
                        if ($file != "." && $file != "..")
                                $dir_array[] = $file;
                }
                if(is_file(ICMS_ROOT_PATH."/modules/".$xcgalDir."/".$xoopsModuleConfig['fullpath'].$folder.$file)) {
                        if(strncmp($file, $xoopsModuleConfig['thumb_pfx'], strlen($xoopsModuleConfig['thumb_pfx'])) != 0
                                &&  strncmp($file, $xoopsModuleConfig['normal_pfx'], strlen($xoopsModuleConfig['normal_pfx'])) != 0)
                                $pic_array[] = $file;
                }
        }
        closedir($dir);

        natcasesort($dir_array);
        natcasesort($pic_array);
}

function display_dir_tree($folder, $ident)
{
        global $xoopsModuleConfig, $PHP_SELF;
        $xcgalDir = basename(dirname(dirname(__FILE__)));
        

        $dir_path = ICMS_ROOT_PATH."/modules/".$xcgalDir."/".$xoopsModuleConfig['fullpath'].$folder;
        if (!is_readable($dir_path)) return;

        $dir = opendir($dir_path);
        while($file = readdir($dir)){
                if(is_dir(ICMS_ROOT_PATH."/modules/".$xcgalDir."/".$xoopsModuleConfig['fullpath'].$folder.$file) && $file != "." && $file != "..") {
                        $start_target = $folder.$file;
                        $dir_path = ICMS_ROOT_PATH."/modules/".$xcgalDir."/".$xoopsModuleConfig['fullpath'].$folder.$file;

                        $warnings = '';
                        if (!is_writable($dir_path)) $warnings .= _AM_SRCHNEW_DIR_RO;
                        if (!is_readable($dir_path)) $warnings .= _AM_SRCHNEW_CANT_READ;

                        if ($warnings) $warnings = '&nbsp;&nbsp;&nbsp;<b>'.$warnings.'<b>';

                        echo <<<EOT
                        <tr>
                                <td class="even">
                                        $ident<img src="../images/folder.gif" alt="">&nbsp;<a href= "$PHP_SELF?startdir=$start_target">$file</a>$warnings
                                </td>
                        </tr>
EOT;
                        display_dir_tree($folder.$file.'/', $ident.'&nbsp;&nbsp;&nbsp;&nbsp;');
                }
        }
        closedir($dir);
}

/**
 * getallpicindb()
 *
 * Fill an array where keys are the full path of all images in the picture table
 *
 * @param $pic_array the array to be filled
 * @return
 **/
function getallpicindb(&$pic_array, $startdir)
{
        global $xoopsDB;

        $sql = "SELECT filepath, filename ".
                   "FROM ".$xoopsDB->prefix("xcgal_pictures")." ".
                   "WHERE filepath LIKE '$startdir%'";
        $result = $xoopsDB->query($sql);
        while ($row = $xoopsDB->fetchArray($result)) {
                $pic_file = $row['filepath'].$row['filename'];
                $pic_array[$pic_file]=1;
        }
        $xoopsDB->freeRecordSet($result);
}

/**
 * getallalbumsindb()
 *
 * Fill an array with all albums where keys are aid of albums and values are
 * album title
 *
 * @param $album_array the array to be filled
 * @return
 **/
function getallalbumsindb(&$album_array)
{
        global $xoopsDB;

        $sql = "SELECT aid, title ".
                   "FROM ".$xoopsDB->prefix("xcgal_albums")." ".
                   "WHERE 1";
        $result = $xoopsDB->query($sql);

        while ($row = $xoopsDB->fetchArray($result)) {
                $album_array[$row['aid']]= $row['title'];
        }
        $xoopsDB->freeRecordSet($result);
}


/**
 * scandir()
 *
 * recursive function that scan a directory, create the HTML code for each
 * picture and add new pictures in an array
 *
 * @param $dir the directory to be scanned
 * @param $expic_array the array that contains pictures already in DB
 * @param $newpic_array the array that contains new pictures found
 * @return
 **/
function xcscandir($dir, &$expic_array)
{
        static $dir_id = 0;
        static $count =0;
        static $pic_id=0;

        $pic_array = array();
        $dir_array = array();

        getfoldercontent($dir, $dir_array, $pic_array, $expic_array );

        if (count($pic_array) > 0){
                $dir_id_str=sprintf("d%04d", $dir_id++);
                echo dirheader($dir, $dir_id_str);
                foreach ($pic_array as $picture) {
                        $count++;
                        $pic_id_str=sprintf("i%04d", $pic_id++);
                        echo picrow($dir.$picture, $pic_id_str, $dir_id_str );
                }
        }
        if (count($dir_array) > 0){
                foreach ($dir_array as $directory) {
                        xcscandir($dir.$directory.'/', $expic_array);
                }
        }
        return $count;
}

/**************************************************************************
 * Main code
 **************************************************************************/

$album_array = array();
getallalbumsindb($album_array);

// We need at least one album
if (!count($album_array)) redirect_header('index.php',2,_AM_SRCHNEW_NEED_ONE_ALB);

if (isset($_POST['insert'])){

        if(!isset($_POST['pics'])) redirect_header('index.php',2,_AM_SRCHNEW_NO_PIC_ADD);
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

        echo "<table border='0' cellpadding='0' cellspacing='1' width='100%' class='outer'>";
        echo "<tr><th colspan='4'>"._AM_SRCHNEW_TITLE."</th></tr>";
        echo "<tr><td colspan='4' class='head'>"._AM_SRCHNEW_INSERT."</td></tr>";
        echo "<tr>";
        echo "<td class='head' valign='middle' align='center'><b>"._AM_SRCHNEW_FOLDER."</b></td>";
        echo "<td class='head' valign='middle' align='center'><b>"._AM_SRCHNEW_IMAGE."</b></td>";
    echo "<td class='head' valign='middle' align='center'><b>"._AM_SRCHNEW_ALB."</b></td>";
    echo "<td class='head' valign='middle' align='center'><b>"._AM_SRCHNEW_RESULT."</b></td>";
        echo "</tr>";

        $count=0;
        foreach ($_POST['pics'] as $pic_id){
                $album_lb_id = $_POST['album_lb_id_'.$pic_id];
                $album_id    = $_POST[$album_lb_id];
                $album_name  = $album_array[$album_id];
                $pic_file    = base64_decode($_POST['picfile_'.$pic_id]);
                $dir_name    = dirname($pic_file)."/";
                $file_name   = basename($pic_file);

                // To avoid problems with PHP scripts max execution time limit, each picture is
                // added individually using a separate script that returns an image
                $status = "<a href=\"../addpic.php?aid=$album_id&pic_file=".($_POST['picfile_'.$pic_id])."&reload=".uniqid('')."\"><img src=\"../addpic.php?aid=$album_id&pic_file=".($_POST['picfile_'.$pic_id])."&reload=".uniqid('')."\" class=\"thumbnail\" border=\"0\" width=\"24\" height=\"24\" /><br /></a>";

                echo "<tr>\n";
                echo "<td class=\"even\" valign=\"middle\" align=\"left\">$dir_name</td>\n";
                echo "<td class=\"odd\" valign=\"middle\" align=\"left\">$file_name</td>\n";
                echo "<td class=\"even\" valign=\"middle\" align=\"left\">$album_name</td>\n";
                echo "<td class=\"odd\" valign=\"middle\" align=\"center\">$status</td>\n";
                echo "</tr>\n";
                $count++;
        }
        echo "<tr><td class='foot' colspan='4'><b>"._AM_SRCHNEW_PATIENT."</b></td></tr>";
        echo "<tr><td class='foot' colspan='4'>"._AM_SRCHNEW_NOTES."</td></tr>";
} elseif(isset($_GET['startdir'])){
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
        
        echo "<table border='0' cellpadding='0' cellspacing='1' width='100%' class='outer'>";
        echo "<tr><th colspan='3'>"._AM_SRCHNEW_TITLE."</th></tr>";
        echo "<form method=\"post\" action=\"{$PHP_SELF}?insert=1\">";
        echo "<tr><td colspan='3' class='head'>"._AM_SRCHNEW_LIST_NEW."</td></tr>";
        $expic_array = array();
        getallpicindb($expic_array, $_GET['startdir']);
        if (xcscandir($_GET['startdir'].'/', $expic_array)){
            echo "<tr><td colspan='3' align='center' class='foot'>";
            echo "<input type=\"submit\" class=\"button\" name=\"insert\" value=\""._AM_SRCHNEW_INS_SEL."\" />";
                echo "</td></tr></form>";
        } else {
            echo "<tr><td colspan='3' align='center' class='even'>";
            echo "<div class='errorMsg'><br /><br />";
            echo "<b>"._AM_SRCHNEW_NO_PIC."</b>";
            echo "<br /><br /><br /></div></td></tr></form>";
        }
} else {
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
        
        echo "<table border='0' cellpadding='0' cellspacing='1' width='100%' class='outer'>";
        echo "<tr><th>"._AM_SRCHNEW_TITLE."</th></tr>";
        display_dir_tree('','');
        echo "<tr><td class='foot'><b>"._AM_SRCHNEW_SEL_DIR_MSG."</b></td></tr>";
}
echo "</table><script type=\"text/javascript\" src=\"../scripts.js\"></script>";
icms_cp_footer();
?>