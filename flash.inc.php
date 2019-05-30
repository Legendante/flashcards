<?php
function saltAndPepper($Username, $Password)
{
	$RetArr = array();
	$SplitSize = strlen(PASSTHEPEPPER) / 2;
	$RetArr[0] = substr(PASSTHEPEPPER, 0, $SplitSize);
	$RetArr[1] = substr(PASSTHEPEPPER, $SplitSize);
	return substr($Password . $Username . $RetArr[0] . $RetArr[1], 0, 70);
}

function hashPassword($Username, $Password)
{
	$Peppered = saltAndPepper($Username, $Password);
	$options = array("cost" => 14);
	return password_hash($Peppered, PASSWORD_BCRYPT, $options);
}

function getCardSetByID($SetID)
{
	global $dbCon;
	
	$insQry = 'SELECT cardsetid, setname, categoryid, originalcardsetid FROM cardset WHERE cardsetid = ' . $SetID;
	$selRes = mysqli_query($dbCon, $insQry) or logDBError(mysqli_error($dbCon), $insQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array('cardsetid' => '', 'setname' => '', 'categoryid' => '');
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr['cardsetid'] = $selData['cardsetid'];
		$retArr['setname'] = $selData['setname'];
		$retArr['categoryid'] = $selData['categoryid'];
		$retArr['originalcardsetid'] = $selData['originalcardsetid'];
	}
	return $retArr;
}

function addCardSet($SetName, $SetCategory, $UserID)
{
	global $dbCon;
	
	$updQry = 'UPDATE cardset SET setorder = setorder + 1 WHERE userid = ' . $UserID . ' AND categoryid = ' . $SetCategory;
	$selRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	
	$insQry = 'INSERT INTO cardset(setname, categoryid, userid) VALUES ("' . $SetName . '", "' . $SetCategory . '", "' . $UserID . '")';
	$selRes = mysqli_query($dbCon, $insQry) or logDBError(mysqli_error($dbCon), $insQry, __FILE__, __FUNCTION__, __LINE__);
	$CardSetID = mysqli_insert_id($dbCon);
	return $CardSetID;
}

function editCardSet($SetID, $SetName, $SetCategory)
{
	global $dbCon;
	
	$insQry = 'UPDATE cardset SET setname = "' . $SetName . '", categoryid = "' . $SetCategory . '" WHERE cardsetid = ' . $SetID;
	$selRes = mysqli_query($dbCon, $insQry) or logDBError(mysqli_error($dbCon), $insQry, __FILE__, __FUNCTION__, __LINE__);
}

function getCardsFromCardSet($CardSetID)
{
	global $dbCon;
	
	$selQry = 'SELECT cardid, cardquestion, cardanswer, cardsetid, cardorder, manualanswer FROM cards WHERE cardsetid = ' . $CardSetID . ' ORDER BY cardorder';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['cardid']]['cardid'] = $selData['cardid'];
		$retArr[$selData['cardid']]['cardquestion'] = $selData['cardquestion'];
		$retArr[$selData['cardid']]['cardanswer'] = $selData['cardanswer'];
		$retArr[$selData['cardid']]['cardsetid'] = $selData['cardsetid'];
		$retArr[$selData['cardid']]['cardorder'] = $selData['cardorder'];
		$retArr[$selData['cardid']]['manualanswer'] = $selData['manualanswer'];
	}
	return $retArr;
}

function getStudyCard($CardSetID, $Offset = 0)
{
	global $dbCon;
	
	$selQry = 'SELECT cardid, cardquestion, cardanswer, cardsetid, cardorder, manualanswer FROM cards WHERE cardsetid = ' . $CardSetID . ' ';
	if($Offset === 'R')
		$selQry .= 'ORDER BY RAND() LIMIT 1';
	else
		$selQry .= 'ORDER BY cardorder LIMIT 1 OFFSET ' . $Offset;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	$selData = mysqli_fetch_array($selRes);
	return $selData;
}

function getCardByID($CardID)
{
	global $dbCon;
	
	$selQry = 'SELECT cardid, cardquestion, cardanswer, cardsetid, cardorder, manualanswer FROM cards WHERE cardid = ' . $CardID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$selData = mysqli_fetch_array($selRes);
	return $selData;
}


function getCardsMaxOrderFromCardSet($CardSetID)
{
	global $dbCon;
	
	$selQry = 'SELECT MAX(cardorder) AS maxorder FROM cards WHERE cardsetid = ' . $CardSetID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$selData = mysqli_fetch_array($selRes);
	return $selData['maxorder'];
}

function moveCardUp($CardSetID, $CardID)
{
	global $dbCon;
	
	$selQry = 'SELECT cardorder FROM cards WHERE cardsetid = ' . $CardSetID . ' AND cardid = ' . $CardID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$selData = mysqli_fetch_array($selRes);
	$ThisOrder = $selData['cardorder'];
	$selQry = 'SELECT cardid FROM cards WHERE cardsetid = ' . $CardSetID . ' AND cardorder = ' . ($ThisOrder - 1);
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$selData = mysqli_fetch_array($selRes);
	$SwapCard = $selData['cardid'];
	$updQry = 'UPDATE cards SET cardorder = ' . $ThisOrder . ' WHERE cardid = ' . $SwapCard . ' AND cardsetid = ' . $CardSetID;
	$selRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	$updQry = 'UPDATE cards SET cardorder = ' . ($ThisOrder - 1) . ' WHERE cardid = ' . $CardID . ' AND cardsetid = ' . $CardSetID;
	$selRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
}

function moveCardDown($CardSetID, $CardID)
{
	global $dbCon;
	
	global $dbCon;
	
	$selQry = 'SELECT cardorder FROM cards WHERE cardsetid = ' . $CardSetID . ' AND cardid = ' . $CardID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$selData = mysqli_fetch_array($selRes);
	$ThisOrder = $selData['cardorder'];
	$selQry = 'SELECT cardid FROM cards WHERE cardsetid = ' . $CardSetID . ' AND cardorder = ' . ($ThisOrder + 1);
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$selData = mysqli_fetch_array($selRes);
	$SwapCard = $selData['cardid'];
	$updQry = 'UPDATE cards SET cardorder = ' . $ThisOrder . ' WHERE cardid = ' . $SwapCard . ' AND cardsetid = ' . $CardSetID;
	$selRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	$updQry = 'UPDATE cards SET cardorder = ' . ($ThisOrder + 1) . ' WHERE cardid = ' . $CardID . ' AND cardsetid = ' . $CardSetID;
	$selRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
}

function addCard($Question, $Answer, $SetID, $ManualAnswer = 0)
{
	global $dbCon;
	
	$updQry = 'UPDATE cards SET cardorder = cardorder + 1 WHERE cardsetid = ' . $SetID;
	$selRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	$insQry = 'INSERT INTO cards(cardquestion, cardanswer, cardsetid, manualanswer) VALUES ("' . $Question . '", "' . $Answer . '", "' . $SetID . '", "' . $ManualAnswer . '")';
	$selRes = mysqli_query($dbCon, $insQry) or logDBError(mysqli_error($dbCon), $insQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}

function editCard($CardID, $Question, $Answer, $SetID, $ManualAnswer = 0)
{
	global $dbCon;
	
	$insQry = 'UPDATE cards SET cardquestion = "' . $Question . '", cardanswer = "' . $Answer . '", manualanswer = "' . $ManualAnswer . '" WHERE cardid = ' . $CardID . ' AND cardsetid = ' . $SetID;
	$selRes = mysqli_query($dbCon, $insQry) or logDBError(mysqli_error($dbCon), $insQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}

function deleteCard($CardID, $SetID)
{
	global $dbCon;
	
	$selQry = 'SELECT cardorder FROM cards WHERE cardid = ' . $CardID . ' AND cardsetid = ' . $SetID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$selData = mysqli_fetch_array($selRes);
	
	$updQry = 'UPDATE cards SET cardorder = cardorder - 1 WHERE cardorder > ' . $selData['cardorder'] . ' AND cardsetid = ' . $SetID;
	$selRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	
	
	$insQry = 'DELETE FROM cards WHERE cardid = ' . $CardID . ' AND cardsetid = ' . $SetID;
	$selRes = mysqli_query($dbCon, $insQry) or logDBError(mysqli_error($dbCon), $insQry, __FILE__, __FUNCTION__, __LINE__);
}

function getCardSetCategories()
{
	global $dbCon;
	
	$selQry = 'SELECT categoryid, categoryname, parentcategoryid FROM cardcategories ORDER BY categoryname';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['categoryid']]['categoryname'] = $selData['categoryname'];
		$retArr[$selData['categoryid']]['parentcategoryid'] = $selData['parentcategoryid'];
	}
	return $retArr;
}

function getCategoryDetails($CategoryID)
{
	global $dbCon;
	
	$selQry = 'SELECT categoryid, categoryname, parentcategoryid FROM cardcategories WHERE categoryid = ' . $CategoryID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$selData = mysqli_fetch_array($selRes);
	return $selData;
}

function getUserCardSets($UserID, $CategoryID = 0)
{
	global $dbCon;
	
	$selQry = 'SELECT cardset.cardsetid, setname, categoryid, originalcardsetid, setorder FROM cardset WHERE userid = ' . $UserID . ' ';
	if($CategoryID != 0)
		$selQry .= 'AND categoryid = ' . $CategoryID . ' ';
	$selQry .= 'ORDER BY categoryid, setorder, setname';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['cardsetid']]['cardsetid'] = $selData['cardsetid'];
		$retArr[$selData['cardsetid']]['setname'] = $selData['setname'];
		$retArr[$selData['cardsetid']]['categoryid'] = $selData['categoryid'];
		$retArr[$selData['cardsetid']]['originalcardsetid'] = $selData['originalcardsetid'];
		$retArr[$selData['cardsetid']]['setorder'] = $selData['setorder'];
	}
	return $retArr;
}

function getUserOwnedCardSets($UserID, $CategoryID = 0)
{
	global $dbCon;
	
	$selQry = 'SELECT cardset.cardsetid, setname, categoryid, originalcardsetid, setorder FROM cardset WHERE userid = ' . $UserID . ' AND originalcardsetid IS NULL ';
	if($CategoryID != 0)
		$selQry .= 'AND categoryid = ' . $CategoryID . ' ';
	$selQry .= 'ORDER BY categoryid, setorder, setname';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['cardsetid']]['cardsetid'] = $selData['cardsetid'];
		$retArr[$selData['cardsetid']]['setname'] = $selData['setname'];
		$retArr[$selData['cardsetid']]['categoryid'] = $selData['categoryid'];
		$retArr[$selData['cardsetid']]['originalcardsetid'] = $selData['originalcardsetid'];
		$retArr[$selData['cardsetid']]['setorder'] = $selData['setorder'];
	}
	return $retArr;
}

function getUserCardSetCounts($UserID, $CategoryID = 0)
{
	global $dbCon;
	
	$selQry = 'SELECT cardset.cardsetid, COUNT(cards.cardid) AS cardcount FROM cardset ';
	$selQry .= 'INNER JOIN cards ON cards.cardsetid = cardset.cardsetid ';
	$selQry .= 'WHERE userid = ' . $UserID . ' ';
	if($CategoryID != 0)
		$selQry .= 'AND categoryid = ' . $CategoryID . ' ';
	$selQry .= 'GROUP BY cardset.cardsetid';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['cardsetid']] = $selData['cardcount'];
	}
	return $retArr;
}

function getUserCardSetCategories($UserID)
{
	global $dbCon;
	
	$selQry = 'SELECT cardcategories.categoryid, categoryname, parentcategoryid FROM cardcategories ';
	$selQry .= 'INNER JOIN cardset ON cardset.categoryid = cardcategories.categoryid ';
	$selQry .= 'WHERE userid = ' . $UserID . ' ';
	$selQry .= 'ORDER BY categoryname';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['categoryid']]['categoryname'] = $selData['categoryname'];
		$retArr[$selData['categoryid']]['parentcategoryid'] = $selData['parentcategoryid'];
	}
	return $retArr;
}

function getUserCardSetCategoryCount($UserID)
{
	global $dbCon;
	
	$selQry = 'SELECT cardcategories.categoryid, COUNT(cardset.cardsetid) AS setcount FROM cardcategories ';
	$selQry .= 'INNER JOIN cardset ON cardset.categoryid = cardcategories.categoryid ';
	$selQry .= 'WHERE userid = ' . $UserID . ' ';
	$selQry .= 'GROUP BY cardcategories.categoryid';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['categoryid']] = $selData['setcount'];
	}
	return $retArr;
}

function addCardSetToUser($UserID, $CardSetID)
{
	global $dbCon;
	
	$CardSet = getCardSetByID($CardSetID);
	$updQry = 'UPDATE cardset SET setorder = setorder + 1 WHERE userid = ' . $UserID . ' AND categoryid = ' . $CardSet['categoryid'];
	$selRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	
	$insQry = 'INSERT INTO cardset(setname, categoryid, originalcardsetid, userid) SELECT setname, categoryid, cardsetid, ' . $UserID . ' FROM cardset WHERE cardsetid = ' . $CardSetID;
	$selRes = mysqli_query($dbCon, $insQry) or logDBError(mysqli_error($dbCon), $insQry, __FILE__, __FUNCTION__, __LINE__);
	$NewCardSetID = mysqli_insert_id($dbCon);
	$insQry = 'INSERT INTO cards(cardquestion, cardanswer, cardsetid, cardorder) SELECT cardquestion, cardanswer, ' . $NewCardSetID . ', cardorder FROM cards WHERE cardsetid = ' . $CardSetID;
	$selRes = mysqli_query($dbCon, $insQry) or logDBError(mysqli_error($dbCon), $insQry, __FILE__, __FUNCTION__, __LINE__);
	return $NewCardSetID;
}

function getStudySessions($UserID, $CardSetID, $ParentUserID, $SessionType, $CategoryID = '')
{
	global $dbCon;
		
	$selQry = 'SELECT sessionid, sessionstart, sessionend, cardsetid, tempsession.userid, categoryid FROM tempsession ';
	$selQry .= 'INNER JOIN userdetails ON userdetails.userid = tempsession.userid AND userdetails.parentuserid = ' . $ParentUserID . ' ';
	$selQry .= 'WHERE sessiontype = ' . $SessionType . ' AND tempsession.userid = ' . $UserID . ' ';
	if($CardSetID != '')
		$selQry .= 'AND cardsetid = ' . $CardSetID . ' ';
	if($CategoryID != '')
		$selQry .= 'AND categoryid = ' . $CategoryID . ' ';
	$selQry .= 'ORDER BY sessionstart DESC';
	// echo $selQry . "<Br>\n";
	
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['sessionid']]['sessionid'] = $selData['sessionid'];
		$retArr[$selData['sessionid']]['sessionstart'] = $selData['sessionstart'];
		$retArr[$selData['sessionid']]['sessionend'] = $selData['sessionend'];
		$retArr[$selData['sessionid']]['cardsetid'] = $selData['cardsetid'];
		$retArr[$selData['sessionid']]['userid'] = $selData['userid'];
		$retArr[$selData['sessionid']]['categoryid'] = $selData['categoryid'];
	}
	return $retArr;
}

function getStudySessionResults($UserID, $CardSetID, $ParentUserID, $SessionType, $CategoryID = '')
{
	global $dbCon;
		
	$selQry = 'SELECT tempsessioncards.sessionid, cardid, cardresult, qaswapped, givenanswer, cardcomplete FROM tempsessioncards ';
	$selQry .= 'INNER JOIN tempsession ON tempsession.sessionid = tempsessioncards.sessionid AND tempsession.userid = ' . $UserID . ' ';
	if($CardSetID != '')
		$selQry .= 'AND cardsetid = ' . $CardSetID . ' ';
	if($CategoryID != '')
		$selQry .= 'AND categoryid = ' . $CategoryID . ' ';
	$selQry .= 'INNER JOIN userdetails ON userdetails.userid = tempsession.userid AND userdetails.parentuserid = ' . $ParentUserID . ' ';
	$selQry .= 'ORDER BY historyserial DESC';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	$cnt = 0;
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['sessionid']][$cnt]['cardid'] = $selData['cardid'];
		$retArr[$selData['sessionid']][$cnt]['result'] = $selData['cardresult'];
		$retArr[$selData['sessionid']][$cnt]['qaswapped'] = $selData['qaswapped'];
		$retArr[$selData['sessionid']][$cnt]['givenanswer'] = $selData['givenanswer'];
		$retArr[$selData['sessionid']][$cnt]['cardcomplete'] = $selData['cardcomplete'];
		$cnt++;
	}
	return $retArr;
}

function getStudySessionCounts($UserID, $ParentUserID, $SessionType)
{
	global $dbCon;
	
	if($SessionType == 2)
		$selQry = 'SELECT COUNT(sessionid) AS recCount, categoryid AS theid FROM tempsession ';
	else
		$selQry = 'SELECT COUNT(sessionid) AS recCount, cardsetid AS theid FROM tempsession ';
	$selQry .= 'INNER JOIN userdetails ON userdetails.userid = tempsession.userid AND userdetails.parentuserid = ' . $ParentUserID . ' ';
	$selQry .= 'WHERE sessiontype = ' . $SessionType . ' AND tempsession.userid = ' . $UserID . ' ';
	if($SessionType == 2)
		$selQry .= 'GROUP BY categoryid';
	else
		$selQry .= 'GROUP BY cardsetid';
	
	// echo $selQry . "<Br>\n";
	
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		if(!isset($retArr[$selData['theid']]))
			$retArr[$selData['theid']] = 0;
		$retArr[$selData['theid']] += $selData['recCount'];
	}
	return $retArr;
}

function getSessionResults($SessionID)
{
	global $dbCon;
	
	$selQry = 'SELECT cardid, cardresult, qaswapped, givenanswer FROM tempsessioncards WHERE sessionid = ' . $SessionID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	$cnt = 0;
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$cnt]['cardid'] = $selData['cardid'];
		$retArr[$cnt]['result'] = $selData['cardresult'];
		$retArr[$cnt]['qaswapped'] = $selData['qaswapped'];
		$retArr[$cnt]['givenanswer'] = $selData['givenanswer'];
		$cnt++;
	}
	return $retArr;
}

function getStudentDetails($ParentUserID)
{
	global $dbCon;
	
	$selQry = 'SELECT userid, email, firstname, lastname FROM userdetails WHERE parentuserid = ' . $ParentUserID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['userid']]['userid'] = $selData['userid'];
		$retArr[$selData['userid']]['email'] = $selData['email'];
		$retArr[$selData['userid']]['firstname'] = $selData['firstname'];
		$retArr[$selData['userid']]['lastname'] = $selData['lastname'];
	}
	return $retArr;	
}

function startTempSession($UserID, $CardArr, $SessionType, $CardSetID = '', $CategoryID = '')
{
	global $dbCon;
	
	$insQry = 'INSERT INTO tempsession(sessionstart, lastaction, userid, sessiontype, cardsetid, categoryid) VALUES (NOW(),NOW(), "' . $UserID . '", "' . $SessionType . '", "' . $CardSetID . '", "' . $CategoryID . '")';
	$selRes = mysqli_query($dbCon, $insQry) or logDBError(mysqli_error($dbCon), $insQry, __FILE__, __FUNCTION__, __LINE__);
	$SessionID = mysqli_insert_id($dbCon);
	
	foreach($CardArr AS $cnt => $CardID)
	{
		$insQry = 'INSERT INTO tempsessioncards(sessionid, cardid) VALUES ("' . $SessionID . '", "' . $CardID . '")';
		$selRes = mysqli_query($dbCon, $insQry) or logDBError(mysqli_error($dbCon), $insQry, __FILE__, __FUNCTION__, __LINE__);
	}
	return $SessionID;
}

function getTempSessionByID($SessionID)
{
	global $dbCon; 
	
	$selQry = 'SELECT sessionstart, lastaction, userid, cardsetid FROM tempsession WHERE sessionid = ' . $SessionID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$selData = mysqli_fetch_array($selRes);
	return $selData;
}

function getTempSessionCardID($SessionID, $Random = 0)
{
	global $dbCon;

	$selQry = 'SELECT cardid FROM tempsessioncards WHERE cardcomplete = 0 AND sessionid = ' . $SessionID;
	if($Random == 1)
		$selQry .= ' ORDER BY RAND()';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$selData = mysqli_fetch_array($selRes);
	return $selData['cardid'];
}

function getTempSessionCardCount($SessionID)
{
	global $dbCon;

	$selQry = 'SELECT COUNT(cardid) AS rowcount, cardcomplete FROM tempsessioncards WHERE sessionid = ' . $SessionID . ' GROUP BY cardcomplete';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array(0 => 0, 1 => 0);
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['cardcomplete']] = $selData['rowcount'];
	}
	return $retArr;
}

function setTempSessionCardPlayed($SessionID, $CardID, $Result, $Given, $QASwapped = 0)
{
	global $dbCon;
	$selQry = 'UPDATE tempsessioncards SET cardcomplete = 1, cardresult = ' . $Result . ', givenanswer = "' . $Given . '", qaswapped = ' . $QASwapped . ' WHERE sessionid = ' . $SessionID . ' AND cardid = ' . $CardID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$selQry = 'UPDATE tempsession SET lastaction = NOW() WHERE sessionid = ' . $SessionID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$selQry = 'UPDATE tempsessioncards SET historyserial = historyserial + 1 WHERE sessionid = ' . $SessionID . ' AND cardcomplete = 1';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
}

function getTempSessionResults($SessionID)
{
	global $dbCon;
	$selQry = 'SELECT cardid, qaswapped, cardresult, givenanswer, cardcomplete FROM tempsessioncards WHERE sessionid = ' . $SessionID . ' ORDER BY historyserial DESC';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['cardid']]['cardid'] = $selData['cardid'];
		$retArr[$selData['cardid']]['cardresult'] = $selData['cardresult'];
		$retArr[$selData['cardid']]['givenanswer'] = $selData['givenanswer'];
		$retArr[$selData['cardid']]['cardcomplete'] = $selData['cardcomplete'];
		$retArr[$selData['cardid']]['qaswapped'] = $selData['qaswapped'];
	}
	return $retArr;
}

function getUserCourses($ParentUserID)
{
	return array();
}
?>