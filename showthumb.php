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

require("include/init.inc.php");

if(!is_object($xoopsUser) || !($xoopsUser->isAdmin($xoopsModule->mid()))) redirect_header(ICMS_URL."/", 3, _NOPERM);

define("GIS_GIF",     1);
define("GIS_JPG",     2);
define("GIS_PNG",     3);

define("UNKNOW_ICON", 'images/unk48x48.gif');
define("GIF_ICON",    'images/gif48x48.gif');
define("READ_ERROR_ICON",    'images/read_error48x48.gif');

function makethumbnail($src_file, $newSize, $method)
{
        global $xoopsModuleConfig;

        $content_type = array(
                GIS_GIF => 'gif',
                GIS_JPG => 'jpeg',
                GIS_PNG => 'png'
        );

        // Checks that file exists and is readable
        if (!filesize($src_file) || !is_readable($src_file)){
                header("Content-type: image/gif");
                fpassthru(fopen(READ_ERROR_ICON, 'rb'));
                exit;
        }

        // find the image size, no size => unknow type
        $imginfo = getimagesize($src_file);
        if ($imginfo == null){
                header("Content-type: image/gif");
                fpassthru(fopen(UNKNOW_ICON, 'rb'));
                exit;
        }

        // GD can't handle gif images
        if ($imginfo[2] == GIS_GIF && ($method == 'gd1' || $method == 'gd2')){
                header("Content-type: image/gif");
                fpassthru(fopen(GIF_ICON, 'rb'));
                exit;
        }

        // height/width
        $srcWidth = $imginfo[0];
        $srcHeight = $imginfo[1];

        $ratio = max($srcWidth, $srcHeight) / $newSize;
        $ratio = max($ratio, 1.0);
        $destWidth = (int)($srcWidth / $ratio);
        $destHeight = (int)($srcHeight / $ratio);

        // Choose method for thumb creation

        switch ($method) {
                case "im" :
                if (preg_match("#[A-Z]:|\\\\#Ai",__FILE__)){
                        $cur_dir = dirname(__FILE__);
                        $src_file = '"'.$cur_dir.'\\'.strtr($src_file, '/', '\\').'"';
                } else {
                        $src_file =   escapeshellarg($src_file);
                }
                header("Content-type: image/".($content_type[$imginfo[2]]));
                passthru("{$xoopsModuleConfig['impath']}convert -quality {$xoopsModuleConfig['jpeg_qual']} -antialias -geometry {$destWidth}x{$destHeight} $src_file -");
                break;

                case "gd2" :
                if ($imginfo[2] == GIS_JPG)
                        $src_img = imagecreatefromjpeg($src_file);
                else
                        $src_img = imagecreatefrompng($src_file);
                $dst_img = imagecreatetruecolor($destWidth, $destHeight);
                imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $destWidth, (int)$destHeight, $srcWidth, $srcHeight);
                header("Content-type: image/jpeg");
                imagejpeg($dst_img);
                imagedestroy($src_img);
                imagedestroy($dst_img);
                break;

                default :
                if ($imginfo[2] == GIS_JPG)
                        $src_img = imagecreatefromjpeg($src_file);
                else
                        $src_img = imagecreatefrompng($src_file);
                $dst_img = imagecreate($destWidth, $destHeight);
                imagecopyresized($dst_img, $src_img, 0, 0, 0, 0, $destWidth, (int)$destHeight, $srcWidth, $srcHeight);
                header("Content-type: image/jpeg");
                imagejpeg($dst_img);
                imagedestroy($src_img);
                imagedestroy($dst_img);
                break;
        }
}

makethumbnail($xoopsModuleConfig['fullpath'].$_GET['picfile'], $_GET['size'], $xoopsModuleConfig['thumb_method'] );
?>
