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
$xcgalDir = basename(dirname(__FILE__));
require('include/init.inc.php');
#include('include/htmlMimeMail.php');
$myts =& MyTextSanitizer::getInstance(); // MyTextSanitizer object
$xoopsMailer =& getMailer();
if (!USER_CAN_SEND_ECARDS) redirect_header('index.php',2,_MD_ACCESS_DENIED);
function send_ecard($recipient_email, $recipient_name, $greetings, $msg_content, $sender_name, $sender_email, $image, $n_picname, $redirect_link)
{
    global $_SERVER, $xoopsUser, $xoopsModuleConfig, $USER, $xoopsDB;
    global $xoopsModule, $xoopsConfig, $myts;

    if(is_object($xoopsUser)) $s_uid= "|| sender_uid = ".$xoopsUser->uid();
    else $s_uid = "";
    $s_time = time()-3600;
    $result = $xoopsDB->query("SELECT * from ".$xoopsDB->prefix("xcgal_ecard")." WHERE (sess_id ='".session_id()."' || sender_email = '".$myts->addSlashes($sender_email)."' || sender_ip ='".$_SERVER['REMOTE_ADDR']."' ".$s_uid.") AND s_time > ".$s_time."");
    if ($xoopsDB->getRowsNum($result) >= $xoopsModuleConfig['ecards_per_hour']){
        redirect_header('index.php',2,sprintf(_MD_CARD_PERHOUR, $xoopsModuleConfig['ecards_per_hour']));
        return;
        }
    if (is_array($USER['ecard']) && count($USER['ecard']) >= $xoopsModuleConfig['ecards_per_hour']){
        $s_count = 0;
        foreach($USER['ecard'] as $sent){
            if ($sent > $s_time) {
                $s_count++;
                }
        }
        if ($s_count >= $xoopsModuleConfig['ecards_per_hour']){
            redirect_header('index.php',2,sprintf(_MD_CARD_PERHOUR, $xoopsModuleConfig['ecards_per_hour']));
            return;
            }

    }
    if (count($USER['ecard']) >= ($xoopsModuleConfig['ecards_per_hour']+2)) array_shift($USER['ecard']);


    $delete_time = time() - ($xoopsModuleConfig['ecards_saved_db'] * 86400);
    $xoopsDB->query("DELETE from ".$xoopsDB->prefix("xcgal_ecard")." WHERE s_time < ".$delete_time."");

    if(is_object($xoopsUser)) $sender_uid= $xoopsUser->uid();
    else $sender_uid = 0;
    $e_id = get_message_id();

    $sql = "INSERT INTO ".$xoopsDB->prefix("xcgal_ecard")." (e_id, sess_id, sender_ip, sender_uid, sender_name, sender_email, recipient_name, recipient_email, greetings, message, s_time, pid, picked) VALUES ('".$e_id."', '".session_id()."', '".$_SERVER['REMOTE_ADDR']."', $sender_uid, '".$myts->addSlashes($sender_name)."', '".$myts->addSlashes($sender_email)."', '".$myts->addSlashes($recipient_name)."', '".$myts->addSlashes($recipient_email)."', '".$myts->addSlashes($greetings)."', '".$myts->makeTareaData4Save($msg_content)."', ". time().", $image, 0)";
    if (!$xoopsDB->queryF($sql)){
        redirect_header('index.php',2,_MD_CARD_NOTINDB);
        }
    $xoopsDB->queryF("UPDATE ".$xoopsDB->prefix("xcgal_pictures")." SET sent_card=sent_card+1 WHERE pid='".$image."'");
    $USER['ecard'][]= time();
    user_save_profile();

    $myts =& MyTextSanitizer::getInstance();
    $xoopsMailer =& getMailer();


    $xoopsMailer->setFromEmail($sender_email);
    $xoopsMailer->setFromName($sender_name);

    $ecardText = $xoopsModuleConfig['ecards_text'];
    $ecardText= str_replace("{R_NAME}", $recipient_name, $ecardText);
    $ecardText= str_replace("{R_MAIL}", $recipient_email, $ecardText);
    $ecardText= str_replace("{S_NAME}", $sender_name, $ecardText);
    $ecardText= str_replace("{S_MAIL}", $sender_email, $ecardText);
    $ecardText= str_replace("{SAVE_DAYS}", $xoopsModuleConfig['ecards_saved_db'], $ecardText);
    $ecardText= str_replace("{X_SITEURL}", ICMS_URL, $ecardText);
    $ecardText= str_replace("{X_SITENAME}", $xoopsConfig['sitename'], $ecardText);
    $ecardText= str_replace("{CARD_LINK}", ICMS_URL."/modules/".$xcgalDir."/displayecard.php?data=".$e_id , $ecardText);    $xoopsMailer->setToEmails($recipient_email);

    $xoopsMailer->setSubject(sprintf(_MD_CARD_ECARD_TITLE, $sender_name));
    $xoopsMailer->setBody($ecardText);

    if ($xoopsModuleConfig['ecards_type'] != 1){
       $htmlCard = build_html_card($sender_name, $sender_email, $n_picname, $msg_content, $greetings,$e_id);

       $xoopsMailer->multimailer->IsHTML(true);
       $xoopsMailer->setBody($htmlCard);
    }

    $xoopsMailer->useMail();

    $xoopsMailer->send(true);

        $ev_success = $xoopsMailer->getSuccess();
        //$ev_errors = $xoopsMailer->getErrors();    #enable to show detailed errors

        if (!empty($ev_success)){
                redirect_header($redirect_link,2,_MD_CARD_SEND_SUCCESS);
                exit;
        } else {
                redirect_header($redirect_link,2,_MD_CARD_SEND_FAILED."<br />".$ev_errors);
                exit;
        }

}

function build_html_card($sender_name, $sender_email, $n_picname, $message, $greetings,$e_id){
    global $myts, $xoopsConfig, $xoopsModuleConfig;
        if(!stristr($n_picname, 'http:')) $n_picname = ICMS_URL."/modules/".$xcgalDir."/".$n_picname;

        $msg_content = $myts->makeTareaData4Show($message,0);

    require_once ICMS_ROOT_PATH.'/class/template.php';
        $ecardTpl = new XoopsTpl();
        $ecardTpl->assign('sitename', $xoopsConfig['sitename']);
        $ecardTpl->assign('ecard_title', sprintf(_MD_CARD_ECARD_TITLE, icms_core_DataFilter::htmlSpecialchars($sender_name)));
        $ecardTpl->assign('charset',_CHARSET);
        $ecardTpl->assign('view_ecard_tgt', ICMS_URL."/modules/".$xcgalDir."/displayecard.php?data=".$e_id);
        $ecardTpl->assign('view_ecard_lnk',_MD_CARD_VIEW_ECARD);
        $ecardTpl->assign('pic_url',$n_picname);
        $ecardTpl->assign('greetings',icms_core_DataFilter::htmlSpecialchars($greetings));
        $ecardTpl->assign('message',$msg_content);
        $ecardTpl->assign('sender_email',icms_core_DataFilter::htmlSpecialchars($sender_email));
        $ecardTpl->assign('sender_name',icms_core_DataFilter::htmlSpecialchars($sender_name));
        $ecardTpl->assign('view_more_tgt',$xoopsModuleConfig['ecards_more_pic_target']);
        $ecardTpl->assign('view_more_lnk', _MD_CARD_VIEW_MORE_PICS);
        $ecardTpl->assign('xoops_module_header', $xcgal_module_header);
        $ecardTpl->assign('xoops_url', ICMS_URL);
    $card = $ecardTpl->fetch('db:xcgal_discard.html');
    return $card;

    }

function get_message_id()
{
        $pool  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pool .= 'abcdefghijklmnopqrstuvwxyz';
        $pool .= '0123456789';
        mt_srand ((double) microtime() * 1000000);
        $unique_id = "";
        for ($index = 0; $index < 12; $index++)
        {
                $unique_id .= substr($pool, (mt_rand()%(strlen($pool))), 1);
        }// end for
        $unique_id = date("ymdHms").$unique_id;
        return $unique_id;
}// end get_message_id

function get_post_var($name, $default='')
{
        global $_POST;

        return isset($_POST[$name]) ? $_POST[$name] : $default;
}

$pid   = (int)$_GET['pid'];
$album = (int)$_GET['album'];
$pos   = (int)$_GET['pos'];

$sender_name  = get_post_var('sender_name', USER_ID ? USER_NAME : (isset($USER['name']) ? $USER['name'] : ''));
$sender_email = get_post_var('sender_email', USER_ID ? $USER_DATA['user_email'] : (isset($USER['email']) ? $USER['email'] : ''));
$recipient_name = get_post_var('recipient_name');
$recipient_email = get_post_var('recipient_email');
$greetings = get_post_var('greetings');
$message = get_post_var('message');
$sender_email_warning = '';
$recipient_email_warning = '';

// Build the private album set
if (!GALLERY_ADMIN_MODE && $xoopsModuleConfig['allow_private_albums']) get_private_album_set();

// Get picture thumbnail url
$result = $xoopsDB->query("SELECT * from ".$xoopsDB->prefix("xcgal_pictures")." WHERE pid='$pid' $ALBUM_SET");
if (!$xoopsDB->getRowsNum($result)) redirect_header('index.php',2,_MD_NON_EXIST_AP);
$row = $xoopsDB->fetchArray($result);
$thumb_pic_url = get_pic_url($row, 'thumb');

// Check supplied email address
$valid_email_pattern = "^[_\.0-9a-z\-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,6}$";
$valid_sender_email = eregi($valid_email_pattern, $sender_email);
$valid_recipient_email = eregi($valid_email_pattern,$recipient_email);
$invalid_email = '<font size="1">'._MD_CARD_INVALIDE_EMAIL.'</font>';
if (!$valid_sender_email && count($_POST) > 0) $sender_email_warning = $invalid_email;
if (!$valid_recipient_email && count($_POST) > 0) $recipient_email_warning = $invalid_email;

// Create and send the e-card
if (count($_POST) > 0 && $valid_sender_email && $valid_recipient_email){

        $gallery_dir = strtr(dirname($PHP_SELF),'\\','/');
        $gallery_url_prefix = 'http://'.$_SERVER['HTTP_HOST'] . $gallery_dir . (substr($gallery_dir, -1) == '/' ? '' : '/');

        if ($xoopsModuleConfig['make_intermediate'] && max($row['pwidth'], $row['pheight']) > $xoopsModuleConfig['picture_width'])  {
            $n_picname = get_pic_url($row, 'normal');
        } else {
            $n_picname = get_pic_url($row, 'fullsize');
        }

        $redirect_link = "displayimage.php?pid=".$row['pid']."&amp;album=".$album."&amp;pos=".$pos."";
        $result = send_ecard($recipient_email, $recipient_name, $greetings, $message, $sender_name, $sender_email, $row['pid'], $n_picname, $redirect_link);

        if (!USER_ID) {
                $USER['name'] = $sender_name;
                $USER['email'] = $sender_email;
        }

}
$xoopsOption['template_main'] = 'xcgal_ecard.html';
include ICMS_ROOT_PATH."/header.php";
$xoopsTpl->assign('xoops_module_header', $xcgal_module_header);
$xoopsTpl->assign('ecard_title',_MD_CARD_TITLE);
$xoopsTpl->assign('lang_ecard_from',_MD_CARD_FROM);
$xoopsTpl->assign('thumb_url',$thumb_pic_url);
$xoopsTpl->assign('album',$album);
$xoopsTpl->assign('pid',$pid);
$xoopsTpl->assign('pos',$pos);
$xoopsTpl->assign('lang_your_name',_MD_CARD_YOUR_NAME);
$xoopsTpl->assign('sender_name',$sender_name);
$xoopsTpl->assign('lang_your_email',_MD_CARD_YOUR_EMAIL);
$xoopsTpl->assign('sender_email',$sender_email);
$xoopsTpl->assign('sender_email_warning',$sender_email_warning);
$xoopsTpl->assign('lang_ecard_to',_MD_CARD_TO);
$xoopsTpl->assign('lang_rcpt_name',_MD_CARD_RCPT_NAME);
$xoopsTpl->assign('recipient_name',$recipient_name);
$xoopsTpl->assign('lang_rcpt_email',_MD_CARD_RCPT_EMAIL);
$xoopsTpl->assign('recipient_email',$recipient_email);
$xoopsTpl->assign('recipient_email_warning',$recipient_email_warning);
$xoopsTpl->assign('lang_ecard_greetings',_MD_CARD_GREETINGS);
$xoopsTpl->assign('greetings',$greetings);
$xoopsTpl->assign('lang_ecard_message',_MD_CARD_MESSAGE);
include_once ICMS_ROOT_PATH."/include/xoopscodes.php";
$myts =& MyTextSanitizer::getInstance(); // MyTextSanitizer object
ob_start();
$GLOBALS["message"] = icms_core_DataFilter::htmlSpecialchars($message);
xoopsCodeTarea("message",60,8);
$xoopsTpl->assign('xoops_codes', ob_get_contents());
ob_end_clean();
ob_start();
xoopsSmilies("message");
$xoopsTpl->assign('xoops_smilies', ob_get_contents());
ob_end_clean();

user_save_profile();
$xoopsTpl->assign('gallery', $xoopsModule->getVar('name'));
include_once "include/theme_func.php";
main_menu();
do_footer();
include_once "../../footer.php";
?>