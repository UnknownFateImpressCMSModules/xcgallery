<?php
// $Id$
//  ------------------------------------------------------------------------ //
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
// Author: Ricardo H. Rekowsky (eyekeeper)                                   //
// ------------------------------------------------------------------------- //


  // get the pictures source
  $b = imagecreatefromjpeg($_GET[picturename]) or die ("Could not create image from JPEG");
  $bx = imagesx($b); 
 // source image width
 $by = imagesy($b); 
 // source image height 
 $lm = $b; 
 if ($bx > 200)  
 {  
 // here we add the watermark to the thumbnails
 // possible variables
 // bottomright = bottom on the right
 // bottomleft = bottom on the left
 // topright = top on the right
 // topleft = top on the right
 $pos = "bottomleft";
 //watermark positioning
 if ($pos == "topleft") 
 { 
    $src_x = 0; $src_y = 0; 
 } 
 else 
    if ($pos == "topright") 
         { $src_x = $bx - 193; 
           $src_y = 0; 
         } 
         else 
           if ($pos == "bottomleft") 
           { 
              $src_x = 0; 
                  $src_y = $by - 20;
                } 
                else 
                  if ($pos == "bottomright") 
                  { 
                    $src_x = $bx - 270; 
                        $src_y = $by - 10; }      // position in pixel from bottom
                        // blend the watermark with the picture
                        ImageAlphaBlending($lm, true) or die ("Could not create the watermark"); 
                        // enable GD 2+ 
            // file that will be used as the watermark (png format)
                         if (file_exists('images/watermark.png')) {
                            $logoImage = ImageCreateFromPNG('images/watermark.png');
                            $logoW = ImageSX($logoImage);
                            $logoH = ImageSY($logoImage);
                            ImageCopy($lm,$logoImage,$src_x,$src_y,0,0,$logoW,$logoH);
                            }
                         elseif (file_exists('images/watermark_default.png')) {
                            $logoImage = ImageCreateFromPNG('images/watermark_default.png');
                            $logoW = ImageSX($logoImage);
                            $logoH = ImageSY($logoImage);
                            ImageCopy($lm,$logoImage,$src_x,$src_y,0,0,$logoW,$logoH);
                            }
                   } 
                   // jpeg quality level- here defined to 80
                   Imagejpeg($lm,'',80);  
                   imageDestroy($lm);
?>