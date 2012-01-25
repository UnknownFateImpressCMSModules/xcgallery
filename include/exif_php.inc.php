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

define("EXIF_CACHE_FILE","exif.dat");

function exif_parse_fracval($val, &$num, &$den)
{
        $num = intval(strtok($val, "/"));
        $den = intval(strtok("/"));
}

function exif_load_from_cache($filepath)
{
        $dir = dirname($filepath);
        $file = basename($filepath);

        if (!is_readable($dir."/".EXIF_CACHE_FILE)) return false;

        $exifDataArray=unserialize(fread(fopen($dir."/".EXIF_CACHE_FILE, 'rb'), filesize($dir."/".EXIF_CACHE_FILE)));

        return $exifDataArray[$file];
}

function exif_save_to_cache($exifData, $filepath)
{
        $dir = dirname($filepath);
        $file = basename($filepath);

        if (!is_writable($dir)) return false;

        if (is_readable($dir."/".EXIF_CACHE_FILE))
                $exifDataArray=unserialize(fread(fopen($dir."/".EXIF_CACHE_FILE, 'rb'), filesize($dir."/".EXIF_CACHE_FILE)));

        $exifDataArray[$file] = $exifData;

        fwrite(fopen($dir."/".EXIF_CACHE_FILE, 'wb'), serialize($exifDataArray));
}

function exif_parse_file($filename)
{
        if (!is_readable($filename)) return false;
        $size = @getimagesize($filename);
        if ($size[2] != 2) return false; // Not a JPEG file

        $exif = exif_read_data($filename,0,true);
        if (!$exif) return false;

        $exifParsed = array();

        $Make = isset($exif['IFD0']['Make']);
        $Model = isset($exif['IFD0']['Model']);
        if (isset($exif['IFD0']['Make']) && isset($exif['IFD0']['Model']))
                $exifParsed['Camera'] = $exif['IFD0']['Make']." - ".$exif['IFD0']['Model'];

        if (isset($exif['EXIF']['DateTimeDigitized']))
                $exifParsed['DateTaken'] = $exif['EXIF']['DateTimeDigitized'];

        if (isset($exif['EXIF']['FNumber'])){
                exif_parse_fracval($exif['EXIF']['FNumber'], $num, $den);
                $exifParsed['Aperture'] = "f/".$num / ($den ? $den : 1);
        } elseif (isset($exif['COMPUTED']['ApertureFNumber']))
                $exifParsed['Aperture'] = $exif['COMPUTED']['ApertureFNumber'];

        if (isset($exif['COMPUTED']['ExposureTime']))
                $exifParsed['ExposureTime'] = $exif['COMPUTED']['ExposureTime'];
        elseif (isset($exif['EXIF']['ExposureTime'])){
                exif_parse_fracval($exif['EXIF']['ExposureTime'], $num, $den);
                $exTime = $num / ($den ? $den : 1);
                if ($exTime <= 0.5 )
                        $exifParsed['ExposureTime'] = sprintf("%0.3f s (1/%d)", $exTime, 1/$exTime);
                else
                        $exifParsed['ExposureTime'] = sprintf("%3.2f s", $exTime);
        }

        if (isset($exif['EXIF']['FocalLength'])){
                exif_parse_fracval($exif['EXIF']['FocalLength'], $num, $den);
                $exifParsed['FocalLength'] = sprintf("%d mm", $num / ($den ? $den : 1));
        }

        if (isset($exif['COMMENT'])){
                $exifParsed['Comment'] = '';
                foreach ($exif['COMMENT'] as $comment)
                        $exifParsed['Comment'] .= ($exifParsed['Comment'] ? '<br />' : '').$comment;
        }

        return $exifParsed;
}
?>
