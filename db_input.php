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
require('include/picmgmt.inc.php');

if (!isset($_GET['event']) && !isset($_POST['event'])) {
    redirect_header('index.php',2,_MD_PARAM_MISSING);
}
$myts =& MyTextSanitizer::getInstance();// MyTextSanitizer object
$event = isset($_POST['event']) ? $_POST['event'] : $_GET['event'];
switch ($event){

//
// Update album
//
        case 'album_update':
        if (!(USER_ADMIN_MODE || GALLERY_ADMIN_MODE)) redirect_header('index.php',2,_MD_PERM_DENIED);

        $aid         = (int)$_POST['aid'];
        $title       = $myts->addSlashes(trim($_POST['title']));
        $category    = (int)$_POST['category'];
        $description = $myts->makeTareaData4Save(trim($_POST['description']),0);
        $thumb       = (int)$_POST['thumb'];
        $visibility  = (int)$_POST['visibility'];
        $uploads     = $_POST['uploads'] == 'YES' ? 'YES' : 'NO';
        $comments    = $_POST['comments'] == 'YES' ? 'YES' : 'NO';
        $votes       = $_POST['votes'] == 'YES' ? 'YES' : 'NO';

        if (!$title) redirect_header('index.php',2,_MD_DB_ALB_NEED_TITLE);

        if (GALLERY_ADMIN_MODE) {
            $query = "UPDATE ".$xoopsDB->prefix("xcgal_albums")." SET title='$title', description='$description', category='$category', thumb='$thumb', uploads='$uploads', comments='$comments', votes='$votes', visibility='$visibility' WHERE aid='$aid' LIMIT 1";
        } else {
                $category = FIRST_USER_CAT + USER_ID;
                if ($visibility != $category && (is_array($USER_DATA['group_id']) && in_array($visibility, $USER_DATA['group_id']))) $visibility = 0;
            $query = "UPDATE ".$xoopsDB->prefix("xcgal_albums")." SET title='$title', description='$description', thumb='$thumb',  comments='$comments', votes='$votes', visibility='$visibility' WHERE aid='$aid' AND category='$category' LIMIT 1";
        }
        $update = $xoopsDB->query($query);
        if (!$xoopsDB->getAffectedRows()) redirect_header("modifyalb.php?album=$aid",2,_MD_DB_NO_NEED);
        redirect_header("modifyalb.php?album=$aid",2, _MD_DB_ALB_UPDATED);
        exit;
        break;
//
// Picture upload
//
        case 'picture':
        if (!USER_CAN_UPLOAD_PICTURES) redirect_header('index.php',2,_MD_PERM_DENIED);

        $album    = (int)$_POST['album'];
        $title    = $myts->addSlashes($_POST['title']);
        if (trim($title)=='') {
           $title = substr($myts->addSlashes($_FILES['userpicture']['name']), 0, (strlen($_FILES['userpicture']['name']) -4));
        }
        $caption  = $myts->makeTareaData4Save($_POST['caption'],0);
        $keywords = $myts->addSlashes($_POST['keywords']);
        $user1    = $myts->addSlashes($_POST['user1']);
        $user2    = $myts->addSlashes($_POST['user2']);
        $user3    = $myts->addSlashes($_POST['user3']);
        $user4    = $myts->addSlashes($_POST['user4']);

        // Check if the album id provided is valid
        if (!GALLERY_ADMIN_MODE){
                $result = $xoopsDB->query("SELECT category FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE aid='$album' and (uploads = 'YES' OR category = '".(USER_ID + FIRST_USER_CAT)."')");
                if ($xoopsDB->getRowsNum($result) == 0 )redirect_header('index.php',2,_MD_DB_UNKOWN);
                $row = $xoopsDB->fetchArray($result);
                $xoopsDB->freeRecordSet($result);
                $category = $row['category'];
        } else {
                $result = $xoopsDB->query("SELECT category FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE aid='$album'");
                if ($xoopsDB->getRowsNum($result) == 0 )redirect_header('index.php',2,_MD_DB_UNKOWN);
                $row = $xoopsDB->fetchArray($result);
                $xoopsDB->freeRecordSet($result);
                $category = $row['category'];
        }

        // Test if the filename of the temporary uploaded picture is empty
        if($_FILES['userpicture']['tmp_name'] == '') redirect_header('index.php',2,_MD_DB_NO_PICUP);

        // Pictures are moved in a directory named 10000 + USER_ID
        if (USER_ID && !defined('SILLY_SAFE_MODE')) {
                $filepath = $xoopsModuleConfig['userpics'].(USER_ID+FIRST_USER_CAT);
            $dest_dir = $xoopsModuleConfig['fullpath'].$filepath;
                if (!is_dir($dest_dir)) {
                    mkdir($dest_dir, octdec($xoopsModuleConfig['default_dir_mode']),true);
                    chmod($dest_dir, octdec($xoopsModuleConfig['default_dir_mode']));
                        if (!is_dir($dest_dir)) redirect_header('index.php',2,sprintf(_MD_DB_ERR_MKDIR, $dest_dir));
                        $fp = fopen($dest_dir.'/index.html', 'w');
                        fwrite($fp, ' ');
                        fclose($fp);
                }
                $dest_dir .= '/';
                $filepath .= '/';
        } else {
                $filepath = $xoopsModuleConfig['userpics'];
            $dest_dir = $xoopsModuleConfig['fullpath'].$filepath;
        }

        // Check that target dir is writable
        if (!is_writable($dest_dir)) redirect_header('index.php',2,sprintf(_MD_DB_DEST_DIR_RO, $dest_dir));

        // Replace forbidden chars with underscores
        $matches = array();
        $forbidden_chars = strtr($xoopsModuleConfig['forbidden_fname_char'], array('&amp;' => '&', '&quot;' => '"', '&lt;' => '<', '&gt;' => '>'));

        // Check that the file uploaded has a valid extension
        $_FILES['userpicture']['name'] = $myts->addSlashes($_FILES['userpicture']['name']);
        $picture_name = strtr($_FILES['userpicture']['name'], $forbidden_chars, str_repeat('_', strlen($xoopsModuleConfig['forbidden_fname_char'])));
        if (!preg_match("/(.+)\.(.*?)\Z/", $picture_name, $matches)){
                $matches[1] = 'invalid_fname';
                $matches[2] = 'xxx';
        }
        if ($matches[2]=='' || !stristr($xoopsModuleConfig['allowed_file_extensions'], $matches[2])) {
            redirect_header('index.php',2,sprintf(_MD_DB_ERR_FEXT, $xoopsModuleConfig['allowed_file_extensions']));
        }

        // Create a unique name for the uploaded file
        $nr = 0;
        $picture_name = $matches[1] . '.' . $matches[2];
        while(file_exists($dest_dir . $picture_name)){
                $picture_name = $matches[1] . '~'. $nr++ .'.' . $matches[2];
        }
        $uploaded_pic = $dest_dir.$picture_name;

        // Move the picture into its final location
        if(!move_uploaded_file($_FILES['userpicture']['tmp_name'], $uploaded_pic ))
                redirect_header('index.php',2, sprintf(_MD_DB_ERR_MOVE, $picture_name, $dest_dir));

        // Change file permission
        chmod($uploaded_pic, octdec($xoopsModuleConfig['default_file_mode']));

        // Get picture information
        $imginfo = getimagesize($uploaded_pic);

        //media
        if(!$imginfo && is_movie($uploaded_pic))
        {
                   $imginfo[0]=320;
                   $imginfo[1]=240;
            $movie_picture=true;
        }
        elseif(!$imginfo && is_audio($uploaded_pic))
        {
                   $imginfo[0]=320;
                   $imginfo[1]=240;
            $movie_picture=true;
        }
        elseif(!$imginfo && is_document($uploaded_pic))
        {
                   $imginfo[0]=320;
                   $imginfo[1]=240;
            $movie_picture=true;
        }
        else $movie_picture=false;


        // Check that picture size (in pixels) is lower than the maximum allowed
        if (max($imginfo[0], $imginfo[1]) > $xoopsModuleConfig['max_upl_width_height']) {
                @unlink($uploaded_pic);
            redirect_header('index.php',2,sprintf(_MD_DB_ERR_PIC_SIZE, $xoopsModuleConfig['max_upl_width_height'], $xoopsModuleConfig['max_upl_width_height']));

        // Check that picture file size is lower than the maximum allowed
        } elseif (filesize($uploaded_pic) > ($xoopsModuleConfig['max_upl_size'] << 10)) {
                @unlink($uploaded_pic);
                redirect_header('index.php',2,sprintf(_MD_DB_ERR_FSIZE, $xoopsModuleConfig['max_upl_size']));

        // getimagesize does not recognize the file as a picture
        } elseif ($imginfo == null) {
                @unlink($uploaded_pic);
                redirect_header('index.php',2,_MD_DB_ERR_IMG_INVALID);

        // JPEG and PNG only are allowed with GD
        } elseif (!$movie_picture &&
                  ($imginfo[2] != GIS_GIF && $imginfo[2] != GIS_JPG && $imginfo[2] != GIS_PNG && ($xoopsModuleConfig['thumb_method'] == 'gd1' || $xoopsModuleConfig['thumb_method'] == 'gd2'))){
                @unlink($uploaded_pic);
                redirect_header('index.php',2,_MD_GD_FILE_TYPE_ERR);

        // Check image type is among those allowed for ImageMagick
        } elseif (!$movie_picture &&
                  (!stristr($xoopsModuleConfig['allowed_img_types'], $IMG_TYPES[$imginfo[2]]) && $xoopsModuleConfig['thumb_method'] == 'im')){
                @unlink($uploaded_pic);
                redirect_header('index.php',2,sprintf(_MD_DB_IMG_ALLOWED, $xoopsModuleConfig['allowed_img_types']));
        } else {

                // Create thumbnail and internediate image and add the image into the DB
                $result = add_picture($album, $filepath, $picture_name, $title, $caption, $keywords, $user1, $user2, $user3, $user4, $category);
                if(!$result){
                        @unlink($uploaded_pic);
                        redirect_header('index.php',2,sprintf(_MD_DB_ERR_INSERT, $uploaded_pic).'<br /><br />'.$ERROR);
                } elseif ($PIC_NEED_APPROVAL) {
                    redirect_header('index.php',2,_MD_DB_UPLOAD_SUCC);
                } else {
                        $header_location = ( @preg_match('/Microsoft|WebSTAR|Xitami/', getenv('SERVER_SOFTWARE')) ) ? 'Refresh: 0; URL=' : 'Location: ';
                        $redirect = "displayimage.php?pid=".$picinID."&amp;pos=".-$picinID;
                           redirect_header($redirect,2,_MD_DB_UPL_SUCC);
                        exit;
                }
        }
        break;




//
// Picture upload
//
        case 'more_pictures':
        if (!USER_CAN_UPLOAD_PICTURES) redirect_header('index.php',2,_MD_PERM_DENIED);

        $album    = (int)$_POST['album'];


#first pic:
if($_FILES['userpicture1']['tmp_name'] != "") {

        $title    = $myts->addSlashes($_POST['title1']);
        if (trim($title)=='') {
           $title = $myts->addSlashes($_FILES['userpicture1']['name']);
        }

        // Check if the album id provided is valid
        if (!GALLERY_ADMIN_MODE){
                $result = $xoopsDB->query("SELECT category FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE aid='$album' and (uploads = 'YES' OR category = '".(USER_ID + FIRST_USER_CAT)."')");
                if ($xoopsDB->getRowsNum($result) == 0 )redirect_header('index.php',2,_MD_DB_UNKOWN);
                $row = $xoopsDB->fetchArray($result);
                $xoopsDB->freeRecordSet($result);
                $category = $row['category'];
        } else {
                $result = $xoopsDB->query("SELECT category FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE aid='$album'");
                if ($xoopsDB->getRowsNum($result) == 0 )redirect_header('index.php',2,_MD_DB_UNKOWN);
                $row = $xoopsDB->fetchArray($result);
                $xoopsDB->freeRecordSet($result);
                $category = $row['category'];
        }

        // Test if the filename of the temporary uploaded picture is empty
        if($_FILES['userpicture1']['tmp_name'] == '') redirect_header('index.php',2,_MD_DB_NO_PICUP);

        // Pictures are moved in a directory named 10000 + USER_ID
        if (USER_ID && !defined('SILLY_SAFE_MODE')) {
                $filepath = $xoopsModuleConfig['userpics'].(USER_ID+FIRST_USER_CAT);
            $dest_dir = $xoopsModuleConfig['fullpath'].$filepath;
                if (!is_dir($dest_dir)) {
                    mkdir($dest_dir, octdec($xoopsModuleConfig['default_dir_mode']),true);
                    chmod($dest_dir, octdec($xoopsModuleConfig['default_dir_mode']));
                        if (!is_dir($dest_dir)) redirect_header('index.php',2,sprintf(_MD_DB_ERR_MKDIR, $dest_dir));
                        $fp = fopen($dest_dir.'/index.html', 'w');
                        fwrite($fp, ' ');
                        fclose($fp);
                }
                $dest_dir .= '/';
                $filepath .= '/';
        } else {
                $filepath = $xoopsModuleConfig['userpics'];
            $dest_dir = $xoopsModuleConfig['fullpath'].$filepath;
        }

        // Check that target dir is writable
        if (!is_writable($dest_dir)) redirect_header('index.php',2,sprintf(_MD_DB_DEST_DIR_RO, $dest_dir));

        // Replace forbidden chars with underscores
        $matches = array();
        $forbidden_chars = strtr($xoopsModuleConfig['forbidden_fname_char'], array('&amp;' => '&', '&quot;' => '"', '&lt;' => '<', '&gt;' => '>'));

        // Check that the file uploaded has a valid extension
        $_FILES['userpicture1']['name'] = $myts->addSlashes($_FILES['userpicture1']['name']);
        $picture_name = strtr($_FILES['userpicture1']['name'], $forbidden_chars, str_repeat('_', strlen($xoopsModuleConfig['forbidden_fname_char'])));
        if (!preg_match("/(.+)\.(.*?)\Z/", $picture_name, $matches)){
                $matches[1] = 'invalid_fname';
                $matches[2] = 'xxx';
        }
        if ($matches[2]=='' || !stristr($xoopsModuleConfig['allowed_file_extensions'], $matches[2])) {
            redirect_header('index.php',2,sprintf(_MD_DB_ERR_FEXT, $xoopsModuleConfig['allowed_file_extensions']));
        }

        // Create a unique name for the uploaded file
        $nr = 0;
        $picture_name = $matches[1] . '.' . $matches[2];
        while(file_exists($dest_dir . $picture_name)){
                $picture_name = $matches[1] . '~'. $nr++ .'.' . $matches[2];
        }
        $uploaded_pic = $dest_dir.$picture_name;

        // Move the picture into its final location
        if(!move_uploaded_file($_FILES['userpicture1']['tmp_name'], $uploaded_pic ))
                redirect_header('index.php',2, sprintf(_MD_DB_ERR_MOVE, $picture_name, $dest_dir));

        // Change file permission
        chmod($uploaded_pic, octdec($xoopsModuleConfig['default_file_mode']));

        // Get picture information
        $imginfo = getimagesize($uploaded_pic);

        // Check that picture size (in pixels) is lower than the maximum allowed
        if (max($imginfo[0], $imginfo[1]) > $xoopsModuleConfig['max_upl_width_height']) {
                @unlink($uploaded_pic);
            redirect_header('index.php',2,sprintf(_MD_DB_ERR_PIC_SIZE, $xoopsModuleConfig['max_upl_width_height'], $xoopsModuleConfig['max_upl_width_height']));

        // Check that picture file size is lower than the maximum allowed
        } elseif (filesize($uploaded_pic) > ($xoopsModuleConfig['max_upl_size'] << 10)) {
                @unlink($uploaded_pic);
                redirect_header('index.php',2,sprintf(_MD_DB_ERR_FSIZE, $xoopsModuleConfig['max_upl_size']));

        // getimagesize does not recognize the file as a picture
        } elseif ($imginfo == null) {
                @unlink($uploaded_pic);
                redirect_header('index.php',2,_MD_DB_ERR_IMG_INVALID);

        // JPEG and PNG only are allowed with GD
        } elseif ($imginfo[2] != GIS_GIF && $imginfo[2] != GIS_JPG && $imginfo[2] != GIS_PNG && ($xoopsModuleConfig['thumb_method'] == 'gd1' || $xoopsModuleConfig['thumb_method'] == 'gd2')){
                @unlink($uploaded_pic);
                redirect_header('index.php',2,_MD_GD_FILE_TYPE_ERR);

        // Check image type is among those allowed for ImageMagick
        } elseif (!stristr($xoopsModuleConfig['allowed_img_types'], $IMG_TYPES[$imginfo[2]]) && $xoopsModuleConfig['thumb_method'] == 'im'){
                @unlink($uploaded_pic);
                redirect_header('index.php',2,sprintf(_MD_DB_IMG_ALLOWED, $xoopsModuleConfig['allowed_img_types']));
        } else {

                // Create thumbnail and internediate image and add the image into the DB
                $result = add_picture($album, $filepath, $picture_name, $title, '', '', '', '', '', '', $category);
                if(!$result){
                        @unlink($uploaded_pic);
                        redirect_header('index.php',2,sprintf(_MD_DB_ERR_INSERT, $uploaded_pic).'<br /><br />'.$ERROR);
                } elseif ($PIC_NEED_APPROVAL) {
                    redirect_header('index.php',2,_MD_DB_UPLOAD_SUCC);
                } else {
                        $header_location = ( @preg_match('/Microsoft|WebSTAR|Xitami/', getenv('SERVER_SOFTWARE')) ) ? 'Refresh: 0; URL=' : 'Location: ';
#                        $redirect = "displayimage.php?pid=".$picinID."&amp;pos=".-$picinID;
#                           redirect_header($redirect,2,_MD_DB_UPL_SUCC);
#                        exit;
                }
        }
#       break;
}

#second pic:
if($_FILES['userpicture2']['tmp_name'] != "") {
        $title    = $myts->addSlashes($_POST['title2']);
        if (trim($title)=='') {
           $title = $myts->addSlashes($_FILES['userpicture2']['name']);
        }

        // Check if the album id provided is valid
        if (!GALLERY_ADMIN_MODE){
                $result = $xoopsDB->query("SELECT category FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE aid='$album' and (uploads = 'YES' OR category = '".(USER_ID + FIRST_USER_CAT)."')");
                if ($xoopsDB->getRowsNum($result) == 0 )redirect_header('index.php',2,_MD_DB_UNKOWN);
                $row = $xoopsDB->fetchArray($result);
                $xoopsDB->freeRecordSet($result);
                $category = $row['category'];
        } else {
                $result = $xoopsDB->query("SELECT category FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE aid='$album'");
                if ($xoopsDB->getRowsNum($result) == 0 )redirect_header('index.php',2,_MD_DB_UNKOWN);
                $row = $xoopsDB->fetchArray($result);
                $xoopsDB->freeRecordSet($result);
                $category = $row['category'];
        }

        // Test if the filename of the temporary uploaded picture is empty
        if($_FILES['userpicture2']['tmp_name'] == '') redirect_header('index.php',2,_MD_DB_NO_PICUP);

        // Pictures are moved in a directory named 10000 + USER_ID
        if (USER_ID && !defined('SILLY_SAFE_MODE')) {
                $filepath = $xoopsModuleConfig['userpics'].(USER_ID+FIRST_USER_CAT);
            $dest_dir = $xoopsModuleConfig['fullpath'].$filepath;
                if (!is_dir($dest_dir)) {
                    mkdir($dest_dir, octdec($xoopsModuleConfig['default_dir_mode']),true);
                    chmod($dest_dir, octdec($xoopsModuleConfig['default_dir_mode']));
                        if (!is_dir($dest_dir)) redirect_header('index.php',2,sprintf(_MD_DB_ERR_MKDIR, $dest_dir));
                        $fp = fopen($dest_dir.'/index.html', 'w');
                        fwrite($fp, ' ');
                        fclose($fp);
                }
                $dest_dir .= '/';
                $filepath .= '/';
        } else {
                $filepath = $xoopsModuleConfig['userpics'];
            $dest_dir = $xoopsModuleConfig['fullpath'].$filepath;
        }

        // Check that target dir is writable
        if (!is_writable($dest_dir)) redirect_header('index.php',2,sprintf(_MD_DB_DEST_DIR_RO, $dest_dir));

        // Replace forbidden chars with underscores
        $matches = array();
        $forbidden_chars = strtr($xoopsModuleConfig['forbidden_fname_char'], array('&amp;' => '&', '&quot;' => '"', '&lt;' => '<', '&gt;' => '>'));

        // Check that the file uploaded has a valid extension
        $_FILES['userpicture2']['name'] = $myts->addSlashes($_FILES['userpicture2']['name']);
        $picture_name = strtr($_FILES['userpicture2']['name'], $forbidden_chars, str_repeat('_', strlen($xoopsModuleConfig['forbidden_fname_char'])));
        if (!preg_match("/(.+)\.(.*?)\Z/", $picture_name, $matches)){
                $matches[1] = 'invalid_fname';
                $matches[2] = 'xxx';
        }
        if ($matches[2]=='' || !stristr($xoopsModuleConfig['allowed_file_extensions'], $matches[2])) {
            redirect_header('index.php',2,sprintf(_MD_DB_ERR_FEXT, $xoopsModuleConfig['allowed_file_extensions']));
        }

        // Create a unique name for the uploaded file
        $nr = 0;
        $picture_name = $matches[1] . '.' . $matches[2];
        while(file_exists($dest_dir . $picture_name)){
                $picture_name = $matches[1] . '~'. $nr++ .'.' . $matches[2];
        }
        $uploaded_pic = $dest_dir.$picture_name;

        // Move the picture into its final location
        if(!move_uploaded_file($_FILES['userpicture2']['tmp_name'], $uploaded_pic ))
                redirect_header('index.php',2, sprintf(_MD_DB_ERR_MOVE, $picture_name, $dest_dir));

        // Change file permission
        chmod($uploaded_pic, octdec($xoopsModuleConfig['default_file_mode']));

        // Get picture information
        $imginfo = getimagesize($uploaded_pic);

        // Check that picture size (in pixels) is lower than the maximum allowed
        if (max($imginfo[0], $imginfo[1]) > $xoopsModuleConfig['max_upl_width_height']) {
                @unlink($uploaded_pic);
            redirect_header('index.php',2,sprintf(_MD_DB_ERR_PIC_SIZE, $xoopsModuleConfig['max_upl_width_height'], $xoopsModuleConfig['max_upl_width_height']));

        // Check that picture file size is lower than the maximum allowed
        } elseif (filesize($uploaded_pic) > ($xoopsModuleConfig['max_upl_size'] << 10)) {
                @unlink($uploaded_pic);
                redirect_header('index.php',2,sprintf(_MD_DB_ERR_FSIZE, $xoopsModuleConfig['max_upl_size']));

        // getimagesize does not recognize the file as a picture
        } elseif ($imginfo == null) {
                @unlink($uploaded_pic);
                redirect_header('index.php',2,_MD_DB_ERR_IMG_INVALID);

        // JPEG and PNG only are allowed with GD
        } elseif ($imginfo[2] != GIS_GIF && $imginfo[2] != GIS_JPG && $imginfo[2] != GIS_PNG && ($xoopsModuleConfig['thumb_method'] == 'gd1' || $xoopsModuleConfig['thumb_method'] == 'gd2')){
                @unlink($uploaded_pic);
                redirect_header('index.php',2,_MD_GD_FILE_TYPE_ERR);

        // Check image type is among those allowed for ImageMagick
        } elseif (!stristr($xoopsModuleConfig['allowed_img_types'], $IMG_TYPES[$imginfo[2]]) && $xoopsModuleConfig['thumb_method'] == 'im'){
                @unlink($uploaded_pic);
                redirect_header('index.php',2,sprintf(_MD_DB_IMG_ALLOWED, $xoopsModuleConfig['allowed_img_types']));
        } else {

                // Create thumbnail and internediate image and add the image into the DB
                $result = add_picture($album, $filepath, $picture_name, $title, '', '', '', '', '', '', $category);
                if(!$result){
                        @unlink($uploaded_pic);
                        redirect_header('index.php',2,sprintf(_MD_DB_ERR_INSERT, $uploaded_pic).'<br /><br />'.$ERROR);
                } elseif ($PIC_NEED_APPROVAL) {
                    redirect_header('index.php',2,_MD_DB_UPLOAD_SUCC);
                } else {
                        $header_location = ( @preg_match('/Microsoft|WebSTAR|Xitami/', getenv('SERVER_SOFTWARE')) ) ? 'Refresh: 0; URL=' : 'Location: ';
#                        $redirect = "displayimage.php?pid=".$picinID."&amp;pos=".-$picinID;
#                           redirect_header($redirect,2,_MD_DB_UPL_SUCC);
#                        exit;
                }
        }
#        break;
}

#3rd pic:
if($_FILES['userpicture3']['tmp_name'] != "") {
        $title    = $myts->addSlashes($_POST['title3']);
        if (trim($title)=='') {
           $title = $myts->addSlashes($_FILES['userpicture3']['name']);
        }

        // Check if the album id provided is valid
        if (!GALLERY_ADMIN_MODE){
                $result = $xoopsDB->query("SELECT category FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE aid='$album' and (uploads = 'YES' OR category = '".(USER_ID + FIRST_USER_CAT)."')");
                if ($xoopsDB->getRowsNum($result) == 0 )redirect_header('index.php',2,_MD_DB_UNKOWN);
                $row = $xoopsDB->fetchArray($result);
                $xoopsDB->freeRecordSet($result);
                $category = $row['category'];
        } else {
                $result = $xoopsDB->query("SELECT category FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE aid='$album'");
                if ($xoopsDB->getRowsNum($result) == 0 )redirect_header('index.php',2,_MD_DB_UNKOWN);
                $row = $xoopsDB->fetchArray($result);
                $xoopsDB->freeRecordSet($result);
                $category = $row['category'];
        }

        // Test if the filename of the temporary uploaded picture is empty
        if($_FILES['userpicture3']['tmp_name'] == '') redirect_header('index.php',2,_MD_DB_NO_PICUP);

        // Pictures are moved in a directory named 10000 + USER_ID
        if (USER_ID && !defined('SILLY_SAFE_MODE')) {
                $filepath = $xoopsModuleConfig['userpics'].(USER_ID+FIRST_USER_CAT);
            $dest_dir = $xoopsModuleConfig['fullpath'].$filepath;
                if (!is_dir($dest_dir)) {
                    mkdir($dest_dir, octdec($xoopsModuleConfig['default_dir_mode']),true);
                    chmod($dest_dir, octdec($xoopsModuleConfig['default_dir_mode']));
                        if (!is_dir($dest_dir)) redirect_header('index.php',2,sprintf(_MD_DB_ERR_MKDIR, $dest_dir));
                        $fp = fopen($dest_dir.'/index.html', 'w');
                        fwrite($fp, ' ');
                        fclose($fp);
                }
                $dest_dir .= '/';
                $filepath .= '/';
        } else {
                $filepath = $xoopsModuleConfig['userpics'];
            $dest_dir = $xoopsModuleConfig['fullpath'].$filepath;
        }

        // Check that target dir is writable
        if (!is_writable($dest_dir)) redirect_header('index.php',2,sprintf(_MD_DB_DEST_DIR_RO, $dest_dir));

        // Replace forbidden chars with underscores
        $matches = array();
        $forbidden_chars = strtr($xoopsModuleConfig['forbidden_fname_char'], array('&amp;' => '&', '&quot;' => '"', '&lt;' => '<', '&gt;' => '>'));

        // Check that the file uploaded has a valid extension
        $_FILES['userpicture3']['name'] = $myts->addSlashes($_FILES['userpicture3']['name']);
        $picture_name = strtr($_FILES['userpicture3']['name'], $forbidden_chars, str_repeat('_', strlen($xoopsModuleConfig['forbidden_fname_char'])));
        if (!preg_match("/(.+)\.(.*?)\Z/", $picture_name, $matches)){
                $matches[1] = 'invalid_fname';
                $matches[2] = 'xxx';
        }
        if ($matches[2]=='' || !stristr($xoopsModuleConfig['allowed_file_extensions'], $matches[2])) {
            redirect_header('index.php',2,sprintf(_MD_DB_ERR_FEXT, $xoopsModuleConfig['allowed_file_extensions']));
        }

        // Create a unique name for the uploaded file
        $nr = 0;
        $picture_name = $matches[1] . '.' . $matches[2];
        while(file_exists($dest_dir . $picture_name)){
                $picture_name = $matches[1] . '~'. $nr++ .'.' . $matches[2];
        }
        $uploaded_pic = $dest_dir.$picture_name;

        // Move the picture into its final location
        if(!move_uploaded_file($_FILES['userpicture3']['tmp_name'], $uploaded_pic ))
                redirect_header('index.php',2, sprintf(_MD_DB_ERR_MOVE, $picture_name, $dest_dir));

        // Change file permission
        chmod($uploaded_pic, octdec($xoopsModuleConfig['default_file_mode']));

        // Get picture information
        $imginfo = getimagesize($uploaded_pic);

        // Check that picture size (in pixels) is lower than the maximum allowed
        if (max($imginfo[0], $imginfo[1]) > $xoopsModuleConfig['max_upl_width_height']) {
                @unlink($uploaded_pic);
            redirect_header('index.php',2,sprintf(_MD_DB_ERR_PIC_SIZE, $xoopsModuleConfig['max_upl_width_height'], $xoopsModuleConfig['max_upl_width_height']));

        // Check that picture file size is lower than the maximum allowed
        } elseif (filesize($uploaded_pic) > ($xoopsModuleConfig['max_upl_size'] << 10)) {
                @unlink($uploaded_pic);
                redirect_header('index.php',2,sprintf(_MD_DB_ERR_FSIZE, $xoopsModuleConfig['max_upl_size']));

        // getimagesize does not recognize the file as a picture
        } elseif ($imginfo == null) {
                @unlink($uploaded_pic);
                redirect_header('index.php',2,_MD_DB_ERR_IMG_INVALID);

        // JPEG and PNG only are allowed with GD
        } elseif ($imginfo[2] != GIS_GIF && $imginfo[2] != GIS_JPG && $imginfo[2] != GIS_PNG && ($xoopsModuleConfig['thumb_method'] == 'gd1' || $xoopsModuleConfig['thumb_method'] == 'gd2')){
                @unlink($uploaded_pic);
                redirect_header('index.php',2,_MD_GD_FILE_TYPE_ERR);

        // Check image type is among those allowed for ImageMagick
        } elseif (!stristr($xoopsModuleConfig['allowed_img_types'], $IMG_TYPES[$imginfo[2]]) && $xoopsModuleConfig['thumb_method'] == 'im'){
                @unlink($uploaded_pic);
                redirect_header('index.php',2,sprintf(_MD_DB_IMG_ALLOWED, $xoopsModuleConfig['allowed_img_types']));
        } else {

                // Create thumbnail and internediate image and add the image into the DB
                $result = add_picture($album, $filepath, $picture_name, $title, '', '', '', '', '', '', $category);
                if(!$result){
                        @unlink($uploaded_pic);
                        redirect_header('index.php',2,sprintf(_MD_DB_ERR_INSERT, $uploaded_pic).'<br /><br />'.$ERROR);
                } elseif ($PIC_NEED_APPROVAL) {
                    redirect_header('index.php',2,_MD_DB_UPLOAD_SUCC);
                } else {
                        $header_location = ( @preg_match('/Microsoft|WebSTAR|Xitami/', getenv('SERVER_SOFTWARE')) ) ? 'Refresh: 0; URL=' : 'Location: ';
#                        $redirect = "displayimage.php?pid=".$picinID."&amp;pos=".-$picinID;
#                           redirect_header($redirect,2,_MD_DB_UPL_SUCC);
#                        exit;
                }
        }
#        break;
}

#4th pic:
if($_FILES['userpicture4']['tmp_name'] != "") {
        $title    = $myts->addSlashes($_POST['title4']);
        if (trim($title)=='') {
           $title = $myts->addSlashes($_FILES['userpicture4']['name']);
        }

        // Check if the album id provided is valid
        if (!GALLERY_ADMIN_MODE){
                $result = $xoopsDB->query("SELECT category FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE aid='$album' and (uploads = 'YES' OR category = '".(USER_ID + FIRST_USER_CAT)."')");
                if ($xoopsDB->getRowsNum($result) == 0 )redirect_header('index.php',2,_MD_DB_UNKOWN);
                $row = $xoopsDB->fetchArray($result);
                $xoopsDB->freeRecordSet($result);
                $category = $row['category'];
        } else {
                $result = $xoopsDB->query("SELECT category FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE aid='$album'");
                if ($xoopsDB->getRowsNum($result) == 0 )redirect_header('index.php',2,_MD_DB_UNKOWN);
                $row = $xoopsDB->fetchArray($result);
                $xoopsDB->freeRecordSet($result);
                $category = $row['category'];
        }

        // Test if the filename of the temporary uploaded picture is empty
        if($_FILES['userpicture4']['tmp_name'] == '') redirect_header('index.php',2,_MD_DB_NO_PICUP);

        // Pictures are moved in a directory named 10000 + USER_ID
        if (USER_ID && !defined('SILLY_SAFE_MODE')) {
                $filepath = $xoopsModuleConfig['userpics'].(USER_ID+FIRST_USER_CAT);
            $dest_dir = $xoopsModuleConfig['fullpath'].$filepath;
                if (!is_dir($dest_dir)) {
                    mkdir($dest_dir, octdec($xoopsModuleConfig['default_dir_mode']),true);
                    chmod($dest_dir, octdec($xoopsModuleConfig['default_dir_mode']));
                        if (!is_dir($dest_dir)) redirect_header('index.php',2,sprintf(_MD_DB_ERR_MKDIR, $dest_dir));
                        $fp = fopen($dest_dir.'/index.html', 'w');
                        fwrite($fp, ' ');
                        fclose($fp);
                }
                $dest_dir .= '/';
                $filepath .= '/';
        } else {
                $filepath = $xoopsModuleConfig['userpics'];
            $dest_dir = $xoopsModuleConfig['fullpath'].$filepath;
        }

        // Check that target dir is writable
        if (!is_writable($dest_dir)) redirect_header('index.php',2,sprintf(_MD_DB_DEST_DIR_RO, $dest_dir));

        // Replace forbidden chars with underscores
        $matches = array();
        $forbidden_chars = strtr($xoopsModuleConfig['forbidden_fname_char'], array('&amp;' => '&', '&quot;' => '"', '&lt;' => '<', '&gt;' => '>'));

        // Check that the file uploaded has a valid extension
        $_FILES['userpicture4']['name'] = $myts->addSlashes($_FILES['userpicture4']['name']);
        $picture_name = strtr($_FILES['userpicture4']['name'], $forbidden_chars, str_repeat('_', strlen($xoopsModuleConfig['forbidden_fname_char'])));
        if (!preg_match("/(.+)\.(.*?)\Z/", $picture_name, $matches)){
                $matches[1] = 'invalid_fname';
                $matches[2] = 'xxx';
        }
        if ($matches[2]=='' || !stristr($xoopsModuleConfig['allowed_file_extensions'], $matches[2])) {
            redirect_header('index.php',2,sprintf(_MD_DB_ERR_FEXT, $xoopsModuleConfig['allowed_file_extensions']));
        }

        // Create a unique name for the uploaded file
        $nr = 0;
        $picture_name = $matches[1] . '.' . $matches[2];
        while(file_exists($dest_dir . $picture_name)){
                $picture_name = $matches[1] . '~'. $nr++ .'.' . $matches[2];
        }
        $uploaded_pic = $dest_dir.$picture_name;

        // Move the picture into its final location
        if(!move_uploaded_file($_FILES['userpicture4']['tmp_name'], $uploaded_pic ))
                redirect_header('index.php',2, sprintf(_MD_DB_ERR_MOVE, $picture_name, $dest_dir));

        // Change file permission
        chmod($uploaded_pic, octdec($xoopsModuleConfig['default_file_mode']));

        // Get picture information
        $imginfo = getimagesize($uploaded_pic);

        // Check that picture size (in pixels) is lower than the maximum allowed
        if (max($imginfo[0], $imginfo[1]) > $xoopsModuleConfig['max_upl_width_height']) {
                @unlink($uploaded_pic);
            redirect_header('index.php',2,sprintf(_MD_DB_ERR_PIC_SIZE, $xoopsModuleConfig['max_upl_width_height'], $xoopsModuleConfig['max_upl_width_height']));

        // Check that picture file size is lower than the maximum allowed
        } elseif (filesize($uploaded_pic) > ($xoopsModuleConfig['max_upl_size'] << 10)) {
                @unlink($uploaded_pic);
                redirect_header('index.php',2,sprintf(_MD_DB_ERR_FSIZE, $xoopsModuleConfig['max_upl_size']));

        // getimagesize does not recognize the file as a picture
        } elseif ($imginfo == null) {
                @unlink($uploaded_pic);
                redirect_header('index.php',2,_MD_DB_ERR_IMG_INVALID);

        // JPEG and PNG only are allowed with GD
        } elseif ($imginfo[2] != GIS_GIF && $imginfo[2] != GIS_JPG && $imginfo[2] != GIS_PNG && ($xoopsModuleConfig['thumb_method'] == 'gd1' || $xoopsModuleConfig['thumb_method'] == 'gd2')){
                @unlink($uploaded_pic);
                redirect_header('index.php',2,_MD_GD_FILE_TYPE_ERR);

        // Check image type is among those allowed for ImageMagick
        } elseif (!stristr($xoopsModuleConfig['allowed_img_types'], $IMG_TYPES[$imginfo[2]]) && $xoopsModuleConfig['thumb_method'] == 'im'){
                @unlink($uploaded_pic);
                redirect_header('index.php',2,sprintf(_MD_DB_IMG_ALLOWED, $xoopsModuleConfig['allowed_img_types']));
        } else {

                // Create thumbnail and internediate image and add the image into the DB
                $result = add_picture($album, $filepath, $picture_name, $title, '', '', '', '', '', '', $category);
                if(!$result){
                        @unlink($uploaded_pic);
                        redirect_header('index.php',2,sprintf(_MD_DB_ERR_INSERT, $uploaded_pic).'<br /><br />'.$ERROR);
                } elseif ($PIC_NEED_APPROVAL) {
                    redirect_header('index.php',2,_MD_DB_UPLOAD_SUCC);
                } else {
                        $header_location = ( @preg_match('/Microsoft|WebSTAR|Xitami/', getenv('SERVER_SOFTWARE')) ) ? 'Refresh: 0; URL=' : 'Location: ';
#                        $redirect = "displayimage.php?pid=".$picinID."&amp;pos=".-$picinID;
#                           redirect_header($redirect,2,_MD_DB_UPL_SUCC);
#                        exit;
                }
        }
#        break;
}
redirect_header('index.php',1);
exit;

//
// Unknow event
//
        default:
        redirect_header('index.php',2,_MD_PARAM_MISSING);
}

?>