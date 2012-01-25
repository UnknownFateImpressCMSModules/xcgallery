<?php
// $Id$
//  ------------------------------------------------------------------------ //
//                    xcGal 2.0 - XOOPS Gallery Modul                        //
//  ------------------------------------------------------------------------ //
//  Based on      xcGallery 1.1 RC1 - XOOPS Gallery Modul                    //
//                    Copyright (c) 2003 Derya Kiran                         //
//  ------------------------------------------------------------------------ //
//  Based on Coppermine Photo Gallery 1.10 http://coppermine.sourceforge.net///
//                      developed by Gr�gory DEMAR                           //
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
$modversion['name'] = 'xcGallery';
$modversion['version'] = '2.04';
$modversion['description'] = 'Gallery module for Xoops 2.2 and higher based on xcgal 1.1 RC what is based on Coppermine 1.10 &copy; Gr�gory DEMAR (http://coppermine.sourceforge.net)';
$modversion['credits'] = "http://dev.xoops.org";
$modversion['author'] = "Vers. 1.1: Derya Kiran, edited for Xoops 2.2 by mcleines";
$modversion['help'] = "top.html";
$modversion['license'] = "GPL see LICENSE";
$modversion['official'] = 1;
$modversion['image'] = "images/slogo.png";
$modversion['dirname'] = basename(dirname(__FILE__));

//Admin things
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin/index.php";
$modversion['adminmenu'] = "admin/menu.php";

// Menu
$modversion['hasMain'] = 1;
//search
$modversion['hasSearch'] = 1;
$modversion['search']['file'] = "include/search.php";
$modversion['search']['func'] = "xcgal_search";
//DB
$modversion['sqlfile']['mysql'] = "sql/mysql.sql";

$modversion['tables'][0] = "xcgal_albums";
$modversion['tables'][1] = "xcgal_categories";
$modversion['tables'][2] = "xcgal_pictures";
$modversion['tables'][3] = "xcgal_usergroups";
$modversion['tables'][4] = "xcgal_votes";
$modversion['tables'][5] = "xcgal_ecard";

// Blocks
$modversion['blocks'][1] = array(
	'file' => "xcgal_blocks.php",
	'name' => _MI_XCGAL_SCROLL,
	'description' => "Scrolling Thumbnails",
	'show_func' => "xcgal_block_func",
	'edit_func' => "xcgal_block_edit",
	'options' => "1|1|1|100|1|5",
	'template' => 'xcgal_block_scroll.html');

$modversion['blocks'][] = array(
	'file' => "xcgal_blocks.php",
	'name' => _MI_XCGAL_CATMENU,
	'description' => "xcGallery categorie menu",
	'show_func' => "xcgal_catmenu_block_func",
	'template' => 'xcgal_block_catmenu.html');

$modversion['blocks'][] = array(
	'file' => "xcgal_blocks.php",
	'name' => _MI_XCGAL_STATIC,
	'description' => "Static Thumbnails",
	'show_func' => "xcgal_block_func",
	'edit_func' => "xcgal_block_edit",
	'options' => "2|4|2|0|1|5",
	'template' => 'xcgal_block_static.html');

$modversion['blocks'][] = array(
	'file' => "xcgal_blocks.php",
	'name' => _MI_XCGAL_METAALB,
	'description' => "Meta Albums",
	'show_func' => "xcgal_block_meta_func",
	'edit_func' => "xcgal_block_meta_edit",
	'options' => "lastup,1/mostsend,1/topn,1|4|1",
	'template' => 'xcgal_block_meta.html');

$modversion['blocks'][] = array(
	'file' => "xcgal_blocks.php",
	'name' => "Filmstrip",
	'description' => "Filmstrip",
	'show_func' => "xcgal_block_filmstrip_func",
	'edit_func' => "xcgal_block_edit",
	'options' => "2|4|2|0|1|5",
	'template' => 'xcgal_block_filmstrip.html');

// Templates
$modversion['templates'][1] = array(
	'file' => 'xcgal_header.html',
	'description' => '');
$modversion['templates'][] = array(
	'file' => 'xcgal_footer.html',
	'description' => '');
$modversion['templates'][] = array(
	'file' => 'xcgal_index.html',
	'description' => '');
$modversion['templates'][] = array(
	'file' => 'xcgal_modifyalb.html',
	'description' => '');
$modversion['templates'][] = array(
	'file' => 'xcgal_editpics.html',
	'description' => '');
$modversion['templates'][] = array(
	'file' => 'xcgal_delete.html',
	'description' => '');
$modversion['templates'][] = array(
	'file' => 'xcgal_fullsize.html',
	'description' => '');
$modversion['templates'][] = array(
	'file' => 'xcgal_slideshow.html',
	'description' => '');
$modversion['templates'][] = array(
	'file' => 'xcgal_upload.html',
	'description' => '');
$modversion['templates'][] = array(
	'file' => 'xcgal_albmgr.html',
	'description' => '');
$modversion['templates'][] = array(
	'file' => 'xcgal_display.html',
	'description' => '');
$modversion['templates'][] = array(
	'file' => 'xcgal_ecard.html',
	'description' => '');
$modversion['templates'][] = array(
	'file' => 'xcgal_search.html',
	'description' => '');
$modversion['templates'][] = array(
	'file' => 'xcgal_thumb.html',
	'description' => '');
$modversion['templates'][] = array(
	'file' => 'xcgal_discard.html',
	'description' => '');
$modversion['templates'][] = array(
	'file' => 'xcgal_uploadmore.html',
	'description' => '');

//$modversion['templates'][1]['file'] = 'coppermine.html';
$modversion['hasComments'] = 1;
$modversion['comments']['itemName'] = 'pid';
$modversion['comments']['pageName'] = 'displayimage.php';

$modversion['config'][1] = array(
	'name' => 'anosee',
	'title' => '_MI_ANONSEE',
	'description' => '',
	'formtype' => 'yesno',
	'valuetype' => 'int',
	'default' => 1);

$modversion['config'][] = array(
	'name' => 'subcat_level',
	'title' => '_MI_SUBCAT_LEVEL',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'int',
	'default' => 2);

$modversion['config'][] = array(
	'name' => 'albums_per_page',
	'title' => '_MI_ALB_PER_PAGE',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'int',
	'default' => 12);

$modversion['config'][] = array(
	'name' => 'album_list_cols',
	'title' => '_MI_ALB_LIST_COLS',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'int',
	'default' => 2);

$modversion['config'][] = array(
	'name' => 'alb_list_thumb_size',
	'title' => '_MI_ALB_THUMB_SIZE',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'int',
	'default' => 50);

$modversion['config'][] = array(
	'name' => 'main_page_layout',
	'title' => '_MI_MAIN_LAYOUT',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'text',
	'default' => 'catlist/alblist/random,2/lastup,2');

$modversion['config'][] = array(
	'name' => 'thumbcols',
	'title' => '_MI_THUMBCOLS',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'int',
	'default' => 4);

$modversion['config'][] = array(
	'name' => 'thumbrows',
	'title' => '_MI_THUMBROWS',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'int',
	'default' => 3);

$modversion['config'][] = array(
	'name' => 'max_tabs',
	'title' => '_MI_MAX_TABS',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'int',
	'default' => 12);

$modversion['config'][] = array(
	'name' => 'caption_in_thumbview',
	'title' => '_MI_TEXT_THUMBVIEW',
	'description' => '',
	'formtype' => 'yesno',
	'valuetype' => 'int',
	'default' => 0);

$modversion['config'][] = array(
	'name' => 'display_comment_count',
	'title' => '_MI_COM_COUNT',
	'description' => '',
	'formtype' => 'yesno',
	'valuetype' => 'int',
	'default' => 1);

$modversion['config'][] = array(
	'name' => 'default_sort_order',
	'title' => '_MI_DEF_SORT',
	'description' => '',
	'formtype' => 'select',
	'valuetype' => 'text',
	'default' => 'na',
	'options' => array('_MI_SORT_NA' => 'na', '_MI_SORT_ND' => 'nd' ,  '_MI_SORT_DA' => 'da' , '_MI_SORT_DD' => 'dd'));

$modversion['config'][] = array(
	'name' => 'min_votes_for_rating',
	'title' => '_MI_MIN_VOTES',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'int',
	'default' => 1);

$modversion['config'][] = array(
	'name' => 'display_pic_info',
	'title' => '_MI_DIS_PICINFO',
	'description' => '',
	'formtype' => 'yesno',
	'valuetype' => 'int',
	'default' => 1);

$modversion['config'][] = array(
	'name' => 'jpeg_qual',
	'title' => '_MI_JPG_QUAL',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'int',
	'default' => 80);

$modversion['config'][] = array(
	'name' => 'thumb_width',
	'title' => '_MI_THUMB_WIDTH',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'int',
	'default' => 100);

$modversion['config'][] = array(
	'name' => 'make_intermediate',
	'title' => '_MI_MAKE_INTERM',
	'description' => '',
	'formtype' => 'yesno',
	'valuetype' => 'int',
	'default' => 1);

$modversion['config'][] = array(
	'name' => 'picture_width',
	'title' => '_MI_PICTURE_WIDTH',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'int',
	'default' => 400);

$modversion['config'][] = array(
	'name' => 'max_upl_size',
	'title' => '_MI_MAX_UPL_SIZE',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'int',
	'default' => 1024);

$modversion['config'][] = array(
	'name' => 'max_upl_width_height',
	'title' => '_MI_MAX_UPL_WIDTH',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'int',
	'default' => 2048);

$modversion['config'][] = array(
	'name' => 'allow_private_albums',
	'title' => '_MI_ALLOW_PRIVATE',
	'description' => '',
	'formtype' => 'yesno',
	'valuetype' => 'int',
	'default' => 1);

$modversion['config'][] = array(
	'name' => 'user_field1_name',
	'title' => '_MI_UF_NAME1',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'text',
	'default' => '');

$modversion['config'][] = array(
	'name' => 'user_field2_name',
	'title' => '_MI_UF_NAME2',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'text',
	'default' => '');

$modversion['config'][] = array(
	'name' => 'user_field3_name',
	'title' => '_MI_UF_NAME3',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'text',
	'default' => '');

$modversion['config'][] = array(
	'name' => 'user_field4_name',
	'title' => '_MI_UF_NAME4',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'text',
	'default' => '');

$modversion['config'][] = array(
	'name' => 'forbidden_fname_char',
	'title' => '_MI_FORB_FNAME',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'text',
	'default' => "$/\:*?\"'<>|`");

$modversion['config'][] = array(
	'name' => 'allowed_file_extensions',
	'title' => '_MI_FILE_EXT',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'text',
	'default' => 'GIF/PNG/JPG/JPEG/TIF/TIFF/AVI/MP3');

$modversion['config'][] = array(
	'name' => 'thumb_method',
	'title' => '_MI_THUMB_METHOD',
	'description' => '',
	'formtype' => 'select',
	'valuetype' => 'text',
	'default' => 'gd2',
	'options' => array( 'Image Magick' => 'im', 'Netpbm' => 'net', 'GD version 1.x' => 'gd1', 'GD version 2.x' => 'gd2'));

$modversion['config'][] = array(
	'name' => 'impath',
	'title' => '_MI_IMPATH',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'text',
	'default' => '');

$modversion['config'][] = array(
	'name' => 'allowed_img_types',
	'title' => '_MI_ALLOW_IMG_TYPES',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'text',
	'default' => 'JPG/GIF/PNG/TIFF');

$modversion['config'][] = array(
	'name' => 'im_options',
	'title' => '_MI_IM_OPTIONS',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'text',
	'default' => '-antialias');

$modversion['config'][] = array(
	'name' => 'read_exif_data',
	'title' => '_MI_READ_EXIF',
	'description' => '',
	'formtype' => 'yesno',
	'valuetype' => 'int',
	'default' => 0);

$modversion['config'][] = array(
	'name' => 'fullpath',
	'title' => '_MI_FULLPATH',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'text',
	'default' => 'albums/');

$modversion['config'][] = array(
	'name' => 'userpics',
	'title' => '_MI_USERPICS',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'text',
	'default' => 'userpics/');

$modversion['config'][] = array(
	'name' => 'normal_pfx',
	'title' => '_MI_NORMAL_PFX',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'text',
	'default' => 'normal_');

$modversion['config'][] = array(
	'name' => 'thumb_pfx',
	'title' => '_MI_THUMB_PFX',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'text',
	'default' => 'thumb_');

$modversion['config'][] = array(
	'name' => 'default_dir_mode',
	'title' => '_MI_DIR_MODE',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'text',
	'default' => '0755');

$modversion['config'][] = array(
	'name' => 'default_file_mode',
	'title' => '_MI_PIC_MODE',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'text',
	'default' => '0644');

$modversion['config'][] = array(
	'name' => 'cookie_name',
	'title' => '_MI_COOKIE_NAME',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'text',
	'default' => 'xcgal');

$modversion['config'][] = array(
	'name' => 'cookie_path',
	'title' => '_MI_COOKIE_PATH',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'text',
	'default' => '/');

$modversion['config'][] = array(
	'name' => 'debug_mode',
	'title' => '_MI_DEBUG_MODE',
	'description' => '',
	'formtype' => 'yesno',
	'valuetype' => 'int',
	'default' => 0);

$modversion['config'][] = array(
	'name' => 'ecards_more_pic_target',
	'title' => '_MI_ECRAD_SEE_MORE',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'text',
	'default' => XOOPS_URL);

$modversion['config'][] = array(
	'name' => 'ecards_type',
	'title' => '_MI_ECRAD_TYPE',
	'description' => '',
	'formtype' => 'select',
	'valuetype' => 'int',
	'default' => 1,
	'options' => array('_MI_TEXT_CARD' => 1, '_MI_HTML_CARD' => 2));

$modversion['config'][] = array(
	'name' => 'ecards_per_hour',
	'title' => '_MI_ECRAD_PER_HOUR',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'int',
	'default' => 5);

$modversion['config'][] = array(
	'name' => 'ecards_saved_db',
	'title' => '_MI_ECRAD_SAVE',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'int',
	'default' => 15);

$modversion['config'][] = array(
	'name' => 'ecards_text',
	'title' => '_MI_ECRAD_TEXT',
	'description' => '_MI_ECRAD_TEXTDESC',
	'formtype' => 'textarea',
	'valuetype' => 'text',
	'default' => _MI_ECRAD_TEXT_VALUE);

$modversion['config'][] = array(
	'name' => 'keep_votes_time',
	'title' => '_MI_KEEP_VOTES',
	'description' => '',
	'formtype' => 'textbox',
	'valuetype' => 'int',
	'default' => 30);

$modversion['config'][] = array(
	'name' => 'search_thumb',
	'title' => '_MI_SEARCH_THUMB',
	'description' => '',
	'formtype' => 'yesno',
	'valuetype' => 'int',
	'default' => 0);

$modversion['config'][] = array(
	'name' => 'watermarking',
	'title' => '_MI_WATERMARKING',
	'description' => '_MI_WATERMARK_TEXTDESC',
	'formtype' => 'yesno',
	'valuetype' => 'int',
	'default' => 0);

$modversion['config'][] = array(
	'name' => 'batch_all',
	'title' => '_MI_BATCHSHOWALL',
	'description' => '_MI_BATCHSHOWALLDESC',
	'formtype' => 'yesno',
	'valuetype' => 'int',
	'default' => 1);
?>
