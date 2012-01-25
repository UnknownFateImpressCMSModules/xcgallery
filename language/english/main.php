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
// shortcuts for Byte, Kilo, Mega
define("_MD_BYTES","Bytes");
define("_MD_KB","KB");
define("_MD_MB","MB");

define("_MD_NPICS","%s picture(s)");
define("_MD_PICS","Pictures");
define("_MD_ALBUM","Album");
define("_MD_ERROR","BACK");
define("_MD_KEYS","Keywords");
define("_MD_CONTINUE","CONTINUE");

define("_MD_RANDOM","Random pictures");
define("_MD_LASTUP","Recent additions");
define("_MD_LASTCOM","Recent comments");
define("_MD_TOPN","Most viewed");
define("_MD_TOPRATED","Top rated");
define("_MD_LASTHITS","Last viewed");
define("_MD_SEARCH","Search results");
define("_MD_USEARCH","Photos submitted by ");
define("_MD_MOST_SENT","Most sent ecards");

define("_MD_ACCESS_DENIED","You don't have permission to access this page.");
define("_MD_PERM_DENIED","You don't have permission to perform this operation.");
define("_MD_PARAM_MISSING","Script called without the required parameter(s).");
define("_MD_NON_EXIST_AP","The selected album/picture does not exist !");
define("_MD_QUOTA_EXCEEDED","Disk quota exceeded<br /><br />You have a space quota of [quota]K, your pictures currently use [space]K, adding this picture would make you exceed your quota.");
define("_MD_GD_FILE_TYPE_ERR","When using the GD image library allowed image types are only JPEG and PNG.");
define("_MD_INVALID_IMG","The image you have uploaded is corrupted or can't be handled by the GD library");
define("_MD_RESIZE_FAILED","Unable to create thumbnail or reduced size image.");
define("_MD_NO_IMG_TO_DISPLAY","No image to display");
define("_MD_NO_EXIST_CAT","The selected category does not exist");
define("_MD_ORPHAN_CAT","A category has a non-existing parent, run the category manager to correct the problem.");
define("_MD_DIRECTORY_RO","Directory '%s' is not writable, pictures can't be deleted");
define("_MD_PIC_IN_INVALID_ALBUM","Picture is in a non existant album (%s)!?");
define("_MD_GD_VERSION_ERR","PHP running on your server does not support GD version 2.x, please switch to GD version 1.x on the config page");
define("_MD_NO_GD_FOUND","PHP running on your server does not support the GD image library, check with your webhost if ImageMagick or Netpbm is installed");
define("_MD_IM_ERROR","Error executing ImageMagick - Return value:");
define("_MD_IM_ERROR_CMD","Cmd line :");
define("_MD_IM_ERROR_CONV","The convert program said:");

// ------------------------------------------------------------------------- //
// File include/theme_func.php
// ------------------------------------------------------------------------- //
define("_MD_THM_ALB_LT","Go to the album list");
define("_MD_THM_ALB_LL","Album list");
define("_MD_THM_GAL_MYT","Go to my personal gallery");
define("_MD_THM_GAL_MYL","My gallery");
define("_MD_THM_ADM_MT","Switch to admin mode");
define("_MD_THM_ADM_ML","Admin mode");
define("_MD_THM_USER_MT","Switch to user mode");
define("_MD_THM_USER_ML","User mode");
define("_MD_THM_UPLT","Upload a picture into an album");
define("_MD_THM_UPLL","Upload picture");
define("_MD_THM_UPLLMORE","Upload more pictures");
define("_MD_THM_LAST_UPL","Recent additions");
define("_MD_THM_LAST_COM","Recent comments");
define("_MD_THM_MOST_VIEW","Most viewed");
define("_MD_THM_TOP_RATE","Top rated");
define("_MD_THM_SEARCH","Search");
define("_MD_THM_UPL_APPR","Upload approval");

define("_MD_THM_ALBMGR_LNK","Create / order my albums");
define("_MD_THM_MODIFY_LNK","Modify my albums");
define("_MD_THM_CAT","Category");
define("_MD_THM_ALB","Albums");
define("_MD_THM_PIC","Pictures");
define("_MD_THM_ALBONPAGE","%d albums on %d page(s)");
define("_MD_THM_DATE","DATE");
define("_MD_THM_NAME","FILENAME");
define("_MD_THM_SORT_DA","Sort by date ascending");
define("_MD_THM_SORT_DD","Sort by date descending");
define("_MD_THM_SORT_NA","Sort by name ascending");
define("_MD_THM_SORT_ND","Sort by name descending");
define("_MD_THM_PICPAGE","%d pictures on %d page(s)");
define("_MD_THM_USERPAGE","%d users on %d page(s)");

// ------------------------------------------------------------------------- //
// File include/functions.inc.php
// ------------------------------------------------------------------------- //

define("_MD_FUNC_FNAME","Filename : ");
define("_MD_FUNC_FSIZE","Filesize : ");
define("_MD_FUNC_DIM","Dimensions : ");
define("_MD_FUNC_DATE","Date added : ");
define("_MD_FUNC_COM","%s comments");
define("_MD_FUNC_VIEW","%s views");
define("_MD_FUNC_VOTE","%s votes");
define("_MD_FUNC_SEND","%s times");
define("_MD_FUNC_DELUSER","Deleted User");
// ------------------------------------------------------------------------- //
// File admin.php
// ------------------------------------------------------------------------- //
define("_MD_ADMIN_LEAVE","Leaving gallery admin mode...");
define("_MD_ADMIN_ENTER","Entering gallery admin mode...");

// ------------------------------------------------------------------------- //
// File albmgr.php
// ------------------------------------------------------------------------- //

define("_MD_ALBMGR_NEED_NAME","Albums need to have a name!");
define("_MD_ALBMGR_CONF_MOD","Are you sure you want to make these modifications?");
define("_MD_ALBMGR_NO_CHANGE","You did not make any change!");
define("_MD_ALBMGR_NEW_ALB","New album");
define("_MD_ALBMGR_CONF_DEL1","Are you sure you want to delete this album?");
define("_MD_ALBMGR_CONF_DEL2","All pictures and comments it contains will be lost!");
define("_MD_ALBMGR_SELECT_FIRST","Select an album first");
define("_MD_ALBMGR_ALB_MGR","Album Manager");
define("_MD_ALBMGR_MY_GAL","* My gallery *");
define("_MD_ALBMGR_NO_CAT","* No category *");
define("_MD_ALBMGR_DEL","Delete");
define("_MD_ALBMGR_NEW","New");
define("_MD_ALBMGR_APPLY","Apply modifications");
define("_MD_ALBMGR_SELECT","Select category");

// ------------------------------------------------------------------------- //
// File db_input.php
// ------------------------------------------------------------------------- //

define("_MD_DB_ALB_NEED_TITLE","You have to provide a title for the album!");
define("_MD_DB_NO_NEED","No update needed.");
define("_MD_DB_ALB_UPDATED","The album was updated");
define("_MD_DB_UNKOWN","Selected album does not exist or you don't have permission to upload in this album");
define("_MD_DB_NO_PICUP","No picture was uploaded!<br /><br />If you have really selected a picture to upload, check that the server allows file uploads...");
define("_MD_DB_ERR_MKDIR","Failed to create directory %s !");
define("_MD_DB_DEST_DIR_RO","Destination directory %s is not writable by the script!");
define("_MD_DB_ERR_FEXT","Only files with the following extensions are accepted : <br /><br />%s.");
define("_MD_DB_ERR_MOVE","Impossible to move %s to %s!");
define("_MD_DB_ERR_PIC_SIZE","The size of picture you have uploaded is too large (maximum allowed is %s x %s");
define("_MD_DB_ERR_FSIZE","The size of the file you have uploaded is too large (maximum allowed is %s KB) !");
define("_MD_DB_ERR_IMG_INVALID","The file you have uploaded is not a valid image !");
define("_MD_DB_IMG_ALLOWED","You can only upload %s images.");
define("_MD_DB_ERR_INSERT","The picture '%s' can't be inserted in the album ");
define("_MD_DB_UPLOAD_SUCC","Your picture was uploaded successfully<br /><br />It will be visible after admin approval.");
define("_MD_DB_UPL_SUCC","Your picture was successfully added");
// ------------------------------------------------------------------------- //
// File delete.php
// ------------------------------------------------------------------------- //
define("_MD_DEL_CAPTION","Caption");
define("_MD_DEL_FS_PIC","full size image");
define("_MD_DEL_DEL_SUCCESS","successfully deleted");
define("_MD_DEL_NS_PIC","normal size image");
define("_MD_DEL_ERR_DEL","can't be deleted");
define("_MD_DEL_THUMB","thumbnail");
define("_MD_DEL_COMMENT","comment");
define("_MD_DEL_IMGALB","image in album");
define("_MD_DEL_ALB_DEL_SUC","Album '%s' deleted");
define("_MD_DEL_ALBMGR","Album Manager");
define("_MD_DEL_INVALID","Invalid data received in '%s'");
define("_MD_DEL_CREATE","Creating album '%s'");
define("_MD_DEL_UPDATE","Updating album '%s' with title '%s' and index '%s'");
define("_MD_DEL_DELPIC","Delete picture");
define("_MD_DEL_DELALB","Delete album");

// ------------------------------------------------------------------------- //
// File displayimage.php
// ------------------------------------------------------------------------- //
define("_MD_DIS_CONF_DEL","Are you sure you want to DELETE this picture ? Comments will also be deleted.");
define("_MD_DIS_DEL_PIC","DELETE THIS PICTURE");
define("_MD_DIS_SIZE","%s x %s pixels");
define("_MD_DIS_VIEWS","%s times");
define("_MD_DIS_SLIDE","Slideshow");
define("_MD_DIS_STOP_SLIDE","STOP SLIDESHOW");
define("_MD_DIS_FULL","Click to view full size image");
define("_MD_DIS_TITLE","Picture information");
define("_MD_DIS_FNAME","Filename");
define("_MD_DIS_ANAME","Album name");
define("_MD_DIS_RATING","Rating (%s votes)");
define("_MD_DIS_FSIZE","File Size");
define("_MD_DIS_DIMEMS","Dimensions");
define("_MD_DIS_DISPLAYED","Displayed");
define("_MD_DIS_CAMERA","Camera");
define("_MD_DIS_DATA_TAKEN","Date taken");
define("_MD_DIS_APERTURE","Aperture");
define("_MD_DIS_EXPTIME","Exposure time");
define("_MD_DIS_FLENGTH","Focal length");
define("_MD_DIS_COMMENT","Comment");
define("_MD_DIS_BACK_TNPAGE","Return to the thumbnail page");
define("_MD_DIS_SHOW_PIC_INFO","Display/hide picture information");
define("_MD_DIS_SEND_CARD","Send this picture as an e-card");
define("_MD_DIS_CARD_DISABLE","e-cards are disabled");
define("_MD_DIS_CARD_DISABLEMSG","You don't have permission to send ecards");
define("_MD_DIS_NEXT","See next picture");
define("_MD_DIS_PREV","See previous picture");
define("_MD_DIS_PICPOS","PICTURE %s/%s");
define("_MD_DIS_RATE_THIS","Rate this picture ");
define("_MD_DIS_NO_VOTE","(No vote yet)");
define("_MD_DIS_RATINGCUR","(current rating : %s / 5 with %s votes)");
define("_MD_DIS_RUBBISH","Rubbish");
define("_MD_DIS_POOR","Poor");
define("_MD_DIS_FAIR","Fair");
define("_MD_DIS_GOOD","Good");
define("_MD_DIS_EXCELLENT","Excellent");
define("_MD_DIS_GREAT","Great");
define("_MD_DIS_UPLOADER","Submitted by");
define("_MD_DIS_EXIF_ERR","PHP running on your server does not support reading EXIF data in JPEG files, please turn this off on the general configuration page.");
define("_MD_DIS_VIEW_MORE_BY","view more pictures submitted by");
define("_MD_DIS_SUBIP","Submitter ip");
define("_MD_DIS_SENT","Sent as ecard");
// ------------------------------------------------------------------------- //
// File ecard.php
// ------------------------------------------------------------------------- //

define("_MD_CARD_TITLE","Send an e-card");
define("_MD_CARD_INVALIDE_EMAIL","<b>Warning</b> : invalid email address!");
define("_MD_CARD_ECARD_TITLE","An e-card from %s for you");
define("_MD_CARD_VIEW_ECARD","If the e-card does not display correctly, click this link");
define("_MD_CARD_VIEW_MORE_PICS","Click this link to view more pictures!");
define("_MD_CARD_SEND_SUCCESS","Your ecard was sent");
define("_MD_CARD_SEND_FAILED","Sorry but the server can't send your e-card...");
define("_MD_CARD_FROM","From");
define("_MD_CARD_YOUR_NAME","Your name");
define("_MD_CARD_YOUR_EMAIL","Your email address");
define("_MD_CARD_TO","To");
define("_MD_CARD_RCPT_NAME","Recipient name");
define("_MD_CARD_RCPT_EMAIL","Recipient email address");
define("_MD_CARD_GREETINGS","Greetings");
define("_MD_CARD_MESSAGE","Message");
define("_MD_CARD_PERHOUR","You're only allowed to send %s ecard(s) per hour. Please, try it later again.");
define("_MD_CARD_NOTINDB","Couldn't insert ecard data to database!<br />Please, contact our siteadmins.");


// ------------------------------------------------------------------------- //
// File editpics.php
// ------------------------------------------------------------------------- //

define("_MD_EDITPICS_PIC_INFO","Picture&nbsp;info");
define("_MD_EDITPICS_TITLE","Title");
define("_MD_EDITPICS_DESC","Description");
define("_MD_EDITPICS_INFOSTR","%sx%s - %sKB - %s views - %s votes");
define("_MD_EDITPICS_APPROVE","Approve picture");
define("_MD_EDITPICS_PP_APPROVE","Postpone approval");
define("_MD_EDITPICS_DEL_PIC","Delete picture");
define("_MD_EDITPICS_RVIEW","Reset view counter");
define("_MD_EDITPICS_RVOTES","Reset votes");
define("_MD_EDITPICS_DCOM","Delete comments");
define("_MD_EDITPICS_UPL_APPROVAL","Upload approval");
define("_MD_EDITPICS_EDIT","Edit pictures");
define("_MD_EDITPICS_NEXT","See next pictures");
define("_MD_EDITPICS_PREV","See previous pictures");
define("_MD_EDITPICS_NUMDIS","Number of picture to display");
define("_MD_EDITPICS_APPLY","Apply modifications");

// ------------------------------------------------------------------------- //
// File index.php
// ------------------------------------------------------------------------- //

define("_MD_INDEX_CONF_DEL","Are you sure you want to DELETE this album ? All pictures and comments will also be deleted.");
define("_MD_INDEX_DEL","DELETE");
define("_MD_INDEX_MOD","PROPERTIES");
define("_MD_INDEX_EDIT","EDIT PICS");
define("_MD_INDEX_STAT1","<b>[pictures]</b> pictures in <b>[albums]</b> albums and <b>[cat]</b> categories with <b>[comments]</b> comments viewed <b>[views]</b> times");
define("_MD_INDEX_STAT2","<b>[pictures]</b> pictures in <b>[albums]</b> albums viewed <b>[views]</b> times");
define("_MD_INDEX_USERS_GAL","%s's Gallery");
define("_MD_INDEX_STAT3","<b>[pictures]</b> pictures in <b>[albums]</b> albums with <b>[comments]</b> comments viewed <b>[views]</b> times");
define("_MD_INDEX_ULIST","User list");
define("_MD_INDEX_NO_UGAL","There are no user galleries");
define("_MD_INDEX_NALBS","%s album(s)");
define("_MD_INDEX_NPICS","%s picture(s)");
define("_MD_INDEX_LASTADD",", last one added on %s");

// ------------------------------------------------------------------------- //
// File modifyalb.php
// ------------------------------------------------------------------------- //
define("_MD_MODIFYALB_UPD_ALB_N","Update album %s");
define("_MD_MODIFYALB_GEN_SET","General settings");
define("_MD_MODIFYALB_ALB_TITLE","Album title");
define("_MD_MODIFYALB_ALB_CAT","Album category");
define("_MD_MODIFYALB_ALB_DESC","Album description");
define("_MD_MODIFYALB_ALB_THUMB","Album thumbnail");
define("_MD_MODIFYALB_ALB_PERM","Permissions for this album");
define("_MD_MODIFYALB_CAN_VIEW","Album can be viewed by");
define("_MD_MODIFYALB_CAN_UPLOAD","Visitors can upload pictures");
define("_MD_MODIFYALB_CAN_COM","Visitors can post comments");
define("_MD_MODIFYALB_CAN_RATE","Visitors can rate pictures");
define("_MD_MODIFYALB_USER_GAL","User Gallery");
define("_MD_MODIFYALB_NO_CAT","* No category *");
define("_MD_MODIFYALB_ALB_EMPTY","Album is empty");
define("_MD_MODIFYALB_LAST_UPL","Last uploaded");
define("_MD_MODIFYALB_PUB_ALB","Everybody (public album)");
define("_MD_MODIFYALB_ME_ONLY","Me only");
define("_MD_MODIFYALB_OWNER_ONLY","Album owner (%s) only");
define("_MD_MODIFYALB_GROUP_ONLY","Members of the '%s' group");
define("_MD_MODIFYALB_ERR_NO_ALB","No album you can modify in the database.");
define("_MD_MODIFYALB_UPDATE","Update album");

// ------------------------------------------------------------------------- //
// File ratepic.php
// ------------------------------------------------------------------------- //
define("_MD_RATE_ALREADY","Sorry but you have already rated this picture");
define("_MD_RATE_OK","Your vote was accepted");

// ------------------------------------------------------------------------- //
// File search.php - OK
// ------------------------------------------------------------------------- //
define("_MD_SEARCH_TITLE","Search the image collection");

// ------------------------------------------------------------------------- //
// File upload.php
// ------------------------------------------------------------------------- //
define("_MD_UPL_TITLE","Upload picture");
define("_MD_UPL_MAX_FSIZE","Maximum allowed file size is %s KB");
define("_MD_UPL_ALBUM","Album");
define("_MD_UPL_PICTURE","Picture");
define("_MD_UPL_PIC_TITLE","Picture title");
define("_MD_UPL_DESCRIPTION","Picture description");
define("_MD_UPL_KEYWORDS","Keywords (separate with spaces)");
define("_MD_UPL_ERR_NO_ALB_UPLOAD","Sorry there is no album where you are allowed to upload pictures");
define("_MD_UPL_YOURALB","Your private Albums");
define("_MD_UPL_ALBPUB","Public Albums");
define("_MD_UPL_OUSERALB","Other User Albums");



?>
