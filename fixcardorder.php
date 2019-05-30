<?php
include_once("db.inc.php");
include_once("header.inc.php");
$SetID = (isset($_GET['s'])) ? pebkac($_GET['s'], 6) : 0;

$selQry = 'SELECT cardsetid FROM cardset';
$cardSetRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
while($cardSetData = mysqli_fetch_array($cardSetRes))
{
	$SetID = $cardSetData['cardsetid'];
	$selQry = 'SELECT cardid, cardorder FROM cards WHERE cardsetid = ' . $SetID . ' ORDER BY cardid';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$cardArr = array();
	$zeroOrderArr = array();
	$MaxOrder = 0;
	while($selData = mysqli_fetch_array($selRes))
	{
		$cardArr[$selData['cardid']] = $selData['cardorder'];
		$MaxOrder = ($MaxOrder < $selData['cardorder']) ? $selData['cardorder'] : $MaxOrder;
		if($selData['cardorder'] == 0)
			$zeroOrderArr[] = $selData['cardid'];
	}
	$nothing = array_shift($zeroOrderArr);
	$MaxOrder++;
	foreach($zeroOrderArr AS $CardID)
	{
		$updQry = "UPDATE cards SET cardorder = " . $MaxOrder . " WHERE cardid = " . $CardID;
		$selRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
		$MaxOrder++;
	}
}
?>