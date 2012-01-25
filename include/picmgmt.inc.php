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

// Add a picture to an album
function add_picture($aid, $filepath, $filename, $title='', $caption='', $keywords='', $user1='', $user2='', $user3='', $user4='', $category=0)
{
        global $xoopsModuleConfig, $ERROR, $USER_DATA, $PIC_NEED_APPROVAL;
        global $xoopsDB, $xoopsUser,$picinID, $_SERVER;
        $xcgalDir = basename(dirname(dirname(__FILE__)));

    $myts =& MyTextSanitizer::getInstance();// MyTextSanitizer object
        $image = $xoopsModuleConfig['fullpath'].$filepath.$filename;
        $normal= $xoopsModuleConfig['fullpath'].$filepath.$xoopsModuleConfig['normal_pfx'].$filename;
        $thumb = $xoopsModuleConfig['fullpath'].$filepath.$xoopsModuleConfig['thumb_pfx'].$filename;

        $imagesize = getimagesize($image);


        if(!$imagesize)
        {
            if(is_movie($image))
            {
                $filename_wo_ext = substr($filename,0,strrpos($filename,'.'));
                $thm_image = get_real_path().$filepath.$filename_wo_ext.".thm";
                if (file_exists($thm_image))
                {
                    copy($thm_image, $thumb);
                    copy($thm_image, $normal);

                }else
                {
                    copy(ICMS_ROOT_PATH."/modules/".$xcgalDir."/images/thumb_avi.jpg", $thumb);
                    copy(ICMS_ROOT_PATH."/modules/".$xcgalDir."/images/thumb_avi.jpg", $normal);
                }
                   $imagesize[0]=320;
                   $imagesize[1]=240;
            }
            
            elseif(is_audio($image))
            {
                $filename_wo_ext = substr($filename,0,strrpos($filename,'.'));
                $thm_image = get_real_path().$filepath.$filename_wo_ext.".thm";
                if (file_exists($thm_image))
                {
                    copy($thm_image, $thumb);
                    copy($thm_image, $normal);

                }else
                {
                    copy(ICMS_ROOT_PATH."/modules/".$xcgalDir."/images/thumb_mp3.jpg", $thumb);
                    copy(ICMS_ROOT_PATH."/modules/".$xcgalDir."/images/thumb_mp3.jpg", $normal);
                }
                   $imagesize[0]=320;
                   $imagesize[1]=240;
            }

            
            
        }
        else
        {




        if (!file_exists($thumb))
                if (!resize_image($image, $thumb, $xoopsModuleConfig['thumb_width'], $xoopsModuleConfig['thumb_method']))
                        return false;

        if(max($imagesize[0],$imagesize[1]) > $xoopsModuleConfig['picture_width'] && $xoopsModuleConfig['make_intermediate'] && !file_exists($normal))
                if (!resize_image($image, $normal, $xoopsModuleConfig['picture_width'], $xoopsModuleConfig['thumb_method']))
                        return false;
               }


        $image_filesize = filesize($image);
        $total_filesize = $image_filesize + (file_exists($normal) ? filesize($normal) : 0) + filesize($thumb);

        // Test if disk quota exceeded
        if (!USER_IS_ADMIN && $USER_DATA['group_quota']) {
            if (is_object($xoopsUser)) $quota_opt = "owner_id = '".USER_ID."'";
                else $quota_opt = "ip = '".$_SERVER['REMOTE_ADDR']."'";

            $result =$xoopsDB->query("SELECT sum(total_filesize) FROM ".$xoopsDB->prefix("xcgal_pictures")." WHERE $quota_opt");
                $record = $xoopsDB->fetchArray($result);
                $total_space_used = $record['sum(total_filesize)'];
                //echo $total_space_used;
                $xoopsDB->freeRecordSet($result);

                if ($total_space_used + $total_filesize > ($USER_DATA['group_quota'] << 10)){
                        @unlink($image);
                        @unlink($normal);
                        @unlink($thumb);
                        $msg = strtr(_MD_QUOTA_EXCEEDED, array(
                                '[quota]' => ($USER_DATA['group_quota']),
                                '[space]' => ($total_space_used >>10)));

                        redirect_header('index.php',2,$msg);
                }
        }

        // Test if picture requires approval
        if (!$USER_DATA['priv_upl_need_approval'] && $category == FIRST_USER_CAT + USER_ID) {
                $approved = 'YES';
        } elseif (!$USER_DATA['pub_upl_need_approval']){
                $approved = 'YES';
        } else {
                $approved = 'NO';
        }
        $PIC_NEED_APPROVAL = ($approved == 'NO');

        // User ID is not recorded when in admin mode (ie. for batch uploads)
        $user_id = USER_ID;
    $user_name = USER_NAME;
        $query = "INSERT INTO ".$xoopsDB->prefix("xcgal_pictures")." (aid, filepath, filename, filesize, total_filesize, pwidth, pheight, mtime, ctime, owner_id, owner_name, title, caption, keywords, approved, user1, user2, user3, user4, ip) VALUES ('$aid', '".$myts->addSlashes($filepath)."', '".$myts->addSlashes($filename)."', '$image_filesize', '$total_filesize', '{$imagesize[0]}', '{$imagesize[1]}','".time()."', '".time()."', '$user_id','$user_name', '$title', '$caption', '$keywords', '$approved', '$user1', '$user2', '$user3', '$user4','".$_SERVER['REMOTE_ADDR']."')";
        $result = $xoopsDB->queryf($query);
        if ($approved == 'YES')
        $picinID= $xoopsDB->getInsertId();
    if (($approved == 'YES') && is_object($xoopsUser)) $xoopsUser->incrementPost();
        return $result;
}

define("GIS_GIF",     1);
define("GIS_JPG",     2);
define("GIS_PNG",     3);

/**
 * resize_image()
 *
 * Create a file containing a resized image
 *
 * @param $src_file the source file
 * @param $dest_file the destination file
 * @param $new_size the size of the square within which the new image must fit
 * @param $method the method used for image resizing
 * @return 'true' in case of success
 **/
function resize_image($src_file, $dest_file, $new_size, $method)
{
        global $xoopsModuleConfig, $ERROR;

        $imginfo = getimagesize($src_file);
        if ($imginfo == null)
                return false;

        // GD can only handle JPG & PNG images
        if ($imginfo[2] != GIS_GIF && $imginfo[2] != GIS_JPG && $imginfo[2] != GIS_PNG && ($method == 'gd1' || $method == 'gd2')){
                $ERROR = _MD_GD_FILE_TYPE_ERR;
                return false;
        }

        // height/width
        $srcWidth = $imginfo[0];
        $srcHeight = $imginfo[1];

        $ratio = max($srcWidth, $srcHeight) / $new_size;
        $ratio = max($ratio, 1.0);
        $destWidth = (int)($srcWidth / $ratio);
        $destHeight = (int)($srcHeight / $ratio);

        // Method for thumbnails creation
        switch ($method) {
        case "im" :
                if (preg_match("#[A-Z]:|\\\\#Ai",__FILE__)){
                        // get the basedir, remove '/include'
                        $cur_dir = substr(dirname(__FILE__),0, -8);
                        $src_file =   '"'.$cur_dir.'\\'.strtr($src_file, '/', '\\').'"';
                        $im_dest_file = str_replace('%', '%%', ('"'.$cur_dir.'\\'.strtr($dest_file, '/', '\\').'"'));
                } else {
                        $src_file =   escapeshellarg($src_file);
                        $im_dest_file = str_replace('%', '%%', escapeshellarg($dest_file));
                }

                $output = array();
                $cmd = "{$xoopsModuleConfig['impath']}convert -quality {$xoopsModuleConfig['jpeg_qual']} {$xoopsModuleConfig['im_options']} -geometry {$destWidth}x{$destHeight} $src_file $im_dest_file";
                exec ($cmd, $output, $retval);

                if ($retval) {
                    $ERROR = _MD_IM_ERROR." $retval";
                        if ($xoopsModuleConfig['debug_mode']) {
                                // Re-execute the command with the backtit operator in order to get all outputs
                                // will not work is safe mode is enabled
                                $output = `$cmd 2>&1`;
                            $ERROR .= "<br /><br /><div align=\"left\">"._MD_IM_ERROR_CMD."<br /><font size=\"2\">".nl2br(htmlspecialchars($cmd))."</font></div>";
                            $ERROR .= "<br /><br /><div align=\"left\">"._MD_IM_ERROR_CONV."<br /><font size=\"2\">";
                                $ERROR .= nl2br(htmlspecialchars($output));
                                $ERROR .= "</font></div>";
                        }
                        @unlink($dest_file);
                        return false;
                }
                break;
    case "net" :
                if (preg_match("#[A-Z]:|\\\\#Ai",__FILE__)){
                        // get the basedir, remove '/include'
                        $cur_dir = substr(dirname(__FILE__),0, -8);
                        $src_file =   '"'.$cur_dir.'\\'.strtr($src_file, '/', '\\').'"';
                        $im_dest_file = str_replace('%', '%%', ('"'.$cur_dir.'\\'.strtr($dest_file, '/', '\\').'"'));
                } else {
                        $src_file =   escapeshellarg($src_file);
                        $im_dest_file = str_replace('%', '%%', escapeshellarg($dest_file));
                }
               switch ($imginfo[2]){
                case GIS_GIF:
                        $op_in   = 'giftopnm';
                        $op_out = 'ppmtogif';
                        $op_out2 = 'pnmtogif';
                        $cmd = "{$xoopsModuleConfig['impath']}{$op_in} $src_file | pnmscale -xsize={$destWidth} -ysize={$destHeight} | ppmquant 255 | {$op_out} > $im_dest_file";
                        break;

                case GIS_JPG:
                        $op_in   = 'jpegtopnm';
                        $op_out = 'pnmtojpeg';
                        $op_out2 = 'ppmtojpeg';
                        $cmd = "{$xoopsModuleConfig['impath']}{$op_in} $src_file | pnmscale -xsize={$destWidth} -ysize={$destHeight} | {$op_out} -quality={$xoopsModuleConfig['jpeg_qual']} > $im_dest_file";
                        $cmd2 = "{$xoopsModuleConfig['impath']}{$op_in} $src_file | pnmscale -xsize={$destWidth} -ysize={$destHeight} | {$op_out2} -quality={$xoopsModuleConfig['jpeg_qual']} > $im_dest_file";
            break;

                case GIS_PNG:
                        $op_in   = 'pngtopnm';
                        $op_out = 'pnmtopng';
                        $cmd = "{$xoopsModuleConfig['impath']}{$op_in} $src_file | pnmscale -xsize={$destWidth} -ysize={$destHeight} | {$op_out} > $im_dest_file";
                        break;
        }
                $output = array();
                echo $cmd;
        if(!(@exec ($cmd)) && isset($cmd2)) @exec ($cmd2);

                break;
        case "gd1" :
                if (!function_exists('imagecreatefromjpeg')) {
                    redirect_header('index.php',2,_MD_NO_GD_FOUND);
                }
                if ($imginfo[2] == GIS_JPG)
                        $src_img = imagecreatefromjpeg($src_file);
                elseif ($imginfo[2] == GIS_GIF)
                        $src_img = imagecreatefromgif($src_file);
                else
                        $src_img = imagecreatefrompng($src_file);
                if (!$src_img){
                        $ERROR = _MD_INVALID_IMG;
                        return false;
                }
                $dst_img = imagecreate($destWidth, $destHeight);
                imagecopyresized($dst_img, $src_img, 0, 0, 0, 0, $destWidth, (int)$destHeight, $srcWidth, $srcHeight);
                imagejpeg($dst_img, $dest_file, $xoopsModuleConfig['jpeg_qual']);
                imagedestroy($src_img);
                imagedestroy($dst_img);
                break;

        case "gd2" :
                if (!function_exists('imagecreatefromjpeg')) {
                    redirect_header('index.php',2,_MD_NO_GD_FOUND);
                }
                if (!function_exists('imagecreatetruecolor')) {
                    redirect_header('index.php',2,_MD_GD_VERSION_ERR);
                }
                if ($imginfo[2] == GIS_JPG)
                        $src_img = imagecreatefromjpeg($src_file);
                elseif ($imginfo[2] == GIS_GIF)
                        $src_img = imagecreatefromgif($src_file);
                else
                        $src_img = imagecreatefrompng($src_file);
                if (!$src_img){
                        $ERROR = _MD_INVALID_IMG;
                        return false;
                }
                $dst_img = imagecreatetruecolor($destWidth, $destHeight);
                imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $destWidth, (int)$destHeight, $srcWidth, $srcHeight);
                imagejpeg($dst_img, $dest_file, $xoopsModuleConfig['jpeg_qual']);
                imagedestroy($src_img);
                imagedestroy($dst_img);
                break;
        }


        // Set mode of uploaded picture
        chmod($dest_file, octdec($xoopsModuleConfig['default_file_mode']));

        // We check that the image is valid
        $imginfo = getimagesize($dest_file);
        if ($imginfo == null){
                $ERROR = _MD_RESIZE_FAILED;
                @unlink($dest_file);
                return false;
        } else {
                return true;
        }
}
?>