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

if ($search_string != '')
{
        $split_search = array();
        $split_search = split(' ', clean_words($search_string));

        $current_match_type = 'and';

        $pic_set= '';
        for($i = 0; $i < count($split_search); $i++)
        {
                switch ( $split_search[$i] )
                {
                        case 'and':
                                $current_match_type = 'and';
                                break;

                        case 'or':
                                $current_match_type = 'or';
                                break;

                        case 'not':
                                $current_match_type = 'not';
                                break;

                        default:
                                if (empty($split_search[$i])) break;

                                $match_word    = '%'.str_replace('*', '%', $myts->addSlashes($split_search[$i])).'%';
                                $match_keyword = '% '.str_replace('*', '%', $myts->addSlashes($split_search[$i])).' %';

                                $sql =  "SELECT pid ".
                                                "FROM ".$xoopsDB->prefix("xcgal_pictures")." ".
                                                "WHERE CONCAT(' ', keywords, ' ') LIKE '$match_keyword' ";

                                if ($query_all) $sql .=
                                                "OR filename LIKE '$match_word' ".
                                                "OR title LIKE '$match_word' ".
                                                "OR caption LIKE '$match_word' ".
                                                "OR user1 LIKE '$match_word' ".
                                                "OR user2 LIKE '$match_word' ".
                                                "OR user3 LIKE '$match_word' ".
                                                "OR user4 LIKE '$match_word' ";

                                $result = $xoopsDB->query($sql);
                                if ($xoopsDB->getRowsNum($result)) {
                                        $set ='';
                                while($row=$xoopsDB->fetchArray($result)){
                                            $set .= $row['pid'].',';
                                        } // while
                                        if (empty($pic_set)) {
                                                if ($current_match_type == 'not') {
                                                    $pic_set .= ' pid not in ('.substr($set, 0, -1).') ';
                                                } else {
                                                        $pic_set .= ' pid in ('.substr($set, 0, -1).') ';
                                                }
                                        } else {
                                                if ($current_match_type == 'not') {
                                                    $pic_set .= ' and pid not in ('.substr($set, 0, -1).') ';
                                                } else {
                                                        $pic_set .= ' '.$current_match_type.' pid in ('.substr($set, 0, -1).') ';
                                                }
                                        }
                                }

                                $xoopsDB->freeRecordSet($result);

                                $current_match_type = 'and';

                }
        }

        if (!empty($pic_set)) {
                $sql =  "SELECT count(*) ".
                                "FROM ".$xoopsDB->prefix("xcgal_pictures")." ".
                                "WHERE ($pic_set) ".
                                "AND approved = 'YES' ".
                                "$ALBUM_SET";
                $result = $xoopsDB->query($sql);
                $nbEnr = $xoopsDB->fetchArray($result);
                $count = $nbEnr['count(*)'];
                $xoopsDB->freeRecordSet($result);

                if($select_columns != '*') $select_columns .= ', title, caption';

                $sql =  "SELECT $select_columns ".
                                "FROM ".$xoopsDB->prefix("xcgal_pictures")." ".
                                "WHERE ($pic_set) ".
                                "AND approved = 'YES' ".
                                "$ALBUM_SET $limit";
                $result = $xoopsDB->query($sql);
                $rowset = db_fetch_rowset($result);
                $xoopsDB->freeRecordSet($result);

                if ($set_caption) foreach ($rowset as $key => $row){
                        $caption = $rowset[$key]['title'] ? "<span class=\"thumb_title\">".icms_core_DataFilter::htmlSpecialchars($rowset[$key]['title'])."</span>" : '';
                        if ($xoopsModuleConfig['caption_in_thumbview']){
                           $caption .= $rowset[$key]['caption'] ? "<span class=\"thumb_caption\">".$myts->makeTareaData4Show($rowset[$key]['caption'],0)."</span>" : '';
                        }
                        $rowset[$key]['caption_text'] = $caption;
                }

        } else {
                $count = 0;
                $rowset = array();
        }
} else {
        $count = 0;
        $rowset = array();
}

?>
