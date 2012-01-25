<?php
// $Id$

function xcgal_search($queryarray, $andor, $limit, $offset, $userid){
	global $xoopsDB, $ALBUM_SET_SEARCH;
	$xcgalCon = search_album_set();
	$sql = "SELECT pid, filepath, filename, ctime, owner_id, title FROM ".$xoopsDB->prefix("xcgal_pictures")." WHERE approved='YES' AND ctime<=".time()."";
	if ( $userid != 0 ) {
		$sql .= " AND owner_id=".$userid." $ALBUM_SET_SEARCH";
	}
	// because count() returns 1 even if a supplied variable
	// is not an array, we must check if $querryarray is really an array
	if ( is_array($queryarray) && $count = count($queryarray) ) {
		$sql .= " AND ((CONCAT(' ', keywords, ' ') LIKE '%$queryarray[0]%' OR filename LIKE '%$queryarray[0]%' OR title LIKE '%$queryarray[0]%' OR caption LIKE '%$queryarray[0]%' OR user1 LIKE '%$queryarray[0]%' OR user2 LIKE '%$queryarray[0]%' OR user3 LIKE '%$queryarray[0]%' OR user4 LIKE '%$queryarray[0]%')";
		for($i=1;$i<$count;$i++){
			$sql .= " $andor ";
			$sql .= "(CONCAT(' ', keywords, ' ') LIKE '%$queryarray[$i]%' OR filename LIKE '%$queryarray[$i]%' OR title LIKE '%$queryarray[$i]%' OR caption LIKE '%$queryarray[$i]%' OR user1 LIKE '%$queryarray[$i]%' OR user2 LIKE '%$queryarray[$i]%' OR user3 LIKE '%$queryarray[$i]%' OR user4 LIKE '%$queryarray[$i]%')";
		}
		$sql .= ") ";
	}
	$sql .= "ORDER BY ctime DESC";
	$result = $xoopsDB->query($sql,$limit,$offset);
	$ret = array();
	$i = 0;
 	while($myrow = $xoopsDB->fetchArray($result)){
 	    if ($xcgalCon['search_thumb'] == 1 ) $ret[$i]['image'] = $xcgalCon['fullpath'].str_replace("%2F","/",rawurlencode($myrow['filepath'].$xcgalCon['thumb_pfx'].$myrow['filename']));
 	    else $ret[$i]['image'] = "images/xcgal.gif";
        $ret[$i]['link'] = "displayimage.php?pid=".$myrow['pid']."";
		$ret[$i]['title'] = $myrow['title'];
		$ret[$i]['time'] = $myrow['ctime'];
		$ret[$i]['uid'] = $myrow['owner_id'];
		$i++;
	}
	return $ret;
}

function search_album_set()
{
	global $ALBUM_SET_SEARCH, $xoopsDB, $xoopsUser;
	$xcgalDir = basename(dirname(dirname(__FILE__)));
	if (is_object ($xoopsUser)){
        $usergroups= $xoopsUser->getgroups();
	    $usergroup= implode(",",$usergroups);
        $buid= $xoopsUser->uid();
    } else {
        $usergroup= XOOPS_GROUP_ANONYMOUS;
        $buid = 0;
    }
    $user_cat = 10000;
    $module_handler= & xoops_gethandler('module');
    $xcgalModule = $module_handler->getByDirname($xcgalDir);
    $config_handler =& xoops_gethandler('config');
	$xcgalCon =& $config_handler->getConfigsByCat(0, $xcgalModule->mid());
    if(is_object($xoopsUser) && $xoopsUser->isAdmin($xcgalModule->mid())) $ALBUM_SET_SEARCH= "";
    else {
	$result = $xoopsDB->query("SELECT aid FROM ".$xoopsDB->prefix("xcgal_albums")." WHERE visibility NOT IN ($usergroup, 0,".($user_cat + $buid).")");
	if (($xoopsDB->getRowsNum($result))) {
		$set ='';
	    while($album=$xoopsDB->fetchArray($result)){
	    	$set .= $album['aid'].',';
	    } // while
		$ALBUM_SET_SEARCH .= 'AND aid NOT IN ('.substr($set, 0, -1).') ';
	}
	$xoopsDB->freeRecordSet($result);
	}
	return $xcgalCon;
}
?>
