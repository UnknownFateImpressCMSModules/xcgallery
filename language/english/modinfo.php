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

define("_MI_XCGAL_NAME","xcGallery");
define("_MI_XCGAL_ADMENU1", "Admin overview");
define("_MI_XCGAL_ADMENU2", "Categories");
define("_MI_XCGAL_ADMENU3", "Users");
define("_MI_XCGAL_ADMENU4", "Groups");
define("_MI_XCGAL_ADMENU5", "Ecards");
define("_MI_XCGAL_ADMENU6", "Batch Add Pictures");

define("_MI_XCGAL_SCROLL","Scrolling Thumbnails");
define("_MI_XCGAL_CATMENU","xcGallery Categories");
define("_MI_XCGAL_STATIC","Static Thumbnails");
define("_MI_XCGAL_METAALB","Meta Albums");

define("_MI_ANONSEE", "Allow anonymous users to see Pictures?");
define("_MI_SUBCAT_LEVEL", "Album list view: Number of levels of categories to display");
define("_MI_ALB_PER_PAGE", "Album list view: Number of albums to display");
define("_MI_ALB_LIST_COLS", "Album list view: Number of columns for the album list");
define("_MI_ALB_THUMB_SIZE", "Album list view: Size of thumbnails in pixels");
define("_MI_MAIN_LAYOUT", "Album list view: The content of the main page");
define("_MI_THUMBCOLS", "Thumbnail view: Number of columns on thumbnail page");
define("_MI_THUMBROWS", "Thumbnail view: Number of rows on thumbnail page");
define("_MI_MAX_TABS", "Thumbnail view: Maximum number of tabs to display");
define("_MI_TEXT_THUMBVIEW", "Thumbnail view: Display picture description (in addition to title) below the thumbnail");
define("_MI_COM_COUNT", "Thumbnail view: Display number of comments below the thumbnail");
define("_MI_DEF_SORT", "Thumbnail view: Default sort order for pictures");
define("_MI_SORT_NA", "Name ascending");
define("_MI_SORT_ND", "Name descending");
define("_MI_SORT_DA", "Date ascending");
define("_MI_SORT_DD", "Date descending");
define("_MI_MIN_VOTES", "Thumbnail view: Minimum number of votes for a picture to appear in the 'top-rated' list ");
define("_MI_DIS_PICINFO", "Image Display: Picture information are visible by default");
define("_MI_JPG_QUAL", "Pictures and thumbnails settings: Quality for JPEG files");
define("_MI_THUMB_WIDTH", "Pictures and thumbnails settings: Max width or height of a thumbnail *");
define("_MI_MAKE_INTERM", "Pictures and thumbnails settings: Create intermediate pictures");
define("_MI_PICTURE_WIDTH", "Pictures and thumbnails settings: Max width or height of an intermediate picture *");
define("_MI_MAX_UPL_SIZE", "Pictures and thumbnails settings: Max size for uploaded pictures (KB)");
define("_MI_MAX_UPL_WIDTH", "Pictures and thumbnails settings: Max width or height for uploaded pictures (pixels)");
define("_MI_ALLOW_PRIVATE", "User settings: Users can can have private albums");
define("_MI_UF_NAME1", "Custom field 1 name for image description (leave blank if unused)");
define("_MI_UF_NAME2", "Custom field 2 name for image description (leave blank if unused)");
define("_MI_UF_NAME3", "Custom field 3 name for image description (leave blank if unused)");
define("_MI_UF_NAME4", "Custom field 4 name for image description (leave blank if unused)");
define("_MI_FORB_FNAME", "Characters forbidden in filenames");
define("_MI_FILE_EXT", "Accepted file extensions for uploaded pictures");
define("_MI_THUMB_METHOD", "Method for resizing images");
define("_MI_IMPATH", "Path to ImageMagick/Netpbm 'convert' utility (example /usr/bin/X11/)");
define("_MI_ALLOW_IMG_TYPES", "Allowed image types (only valid for ImageMagick)");
define("_MI_IM_OPTIONS", "Command line options for ImageMagick");
define("_MI_READ_EXIF", "Read EXIF data in JPEG files (needs php exif extension");
define("_MI_FULLPATH", "The album directory *");
define("_MI_USERPICS", "The directory for user pictures *");
define("_MI_NORMAL_PFX", "The prefix for intermediate pictures *");
define("_MI_THUMB_PFX", "The prefix for thumbnails *");
define("_MI_DIR_MODE", "Default mode for directories");
define("_MI_PIC_MODE", "Default mode for pictures");
define("_MI_COOKIE_NAME", "Name of the cookie used by the script");
define("_MI_COOKIE_PATH", "Path of the cookie used by the script");
define("_MI_DEBUG_MODE", "Enable Gallery debug mode");
define("_MI_ECRAD_SEE_MORE", "Target address for the 'See more pictures' link in e-cards");
define("_MI_ECRAD_TYPE", "Select ecard type");
define("_MI_TEXT_CARD", "Text");
define("_MI_HTML_CARD", "Html");
define("_MI_ECRAD_PER_HOUR", "Allowed ecards, that a user can send per hour");
define("_MI_ECRAD_SAVE", "How long should ecards be saved in db (days)");
define("_MI_ECRAD_TEXT","Ecard text");
define("_MI_ECRAD_TEXTDESC","(for text ecards and as alternative text for html ecards)<br /><b>Useful Tags</b><br />{X_SITEURL} will print ".ICMS_URL."<br />{X_SITENAME} will print the site name<br />{R_NAME} will print recipient name<br />{R_MAIL} will print recipient email<br />{S_NAME} will print sender name<br />{S_MAIL} will print sender email<br />{SAVE_DAYS} will print number of day an ecard is saved in db<br />{CARD_LINK} will print the ecard pick-up url");
define("_MI_ECRAD_TEXT_VALUE","Dear {R_NAME},\n\n{S_NAME}({S_MAIL}) has sent an ecard for you.\nPlease, pick it up at {CARD_LINK}.\nYour ecard will be saved {SAVE_DAYS} days in our database.\n\nregards\n{X_SITENAME} team ({X_SITEURL})");
define("_MI_KEEP_VOTES", "How long should votes be saved in db (days) (0 if they should not be deleted");
define("_MI_SEARCH_THUMB", "Show thumbnail instead of xcGallery icon on search and userinfo pages");
define("_MI_WATERMARKING", "Use watermarking for JPG");
define("_MI_WATERMARK_TEXTDESC", "Watermark must be saved at xcgal/images/watermark.png");
define("_MI_BATCHSHOWALL", "Batchupload - Show all");
define("_MI_BATCHSHOWALLDESC", "All files are shown, also files that are already in an album. For NO only new files are displayed");
?>