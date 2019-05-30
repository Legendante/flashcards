<?php
session_start();
include_once("db.inc.php");
// include_once("header.inc.php");
include_once("flash.inc.php");

$SetID = pebkac($_POST['SetID'], 6);
$CardID = pebkac($_POST['CardID'], 6);
$SetName = trim(pebkac($_POST['setname'], 100, 'STRING'));
$SetCat = pebkac($_POST['setcat'], 6);

if($SetID == 0)
	$SetID = addCardSet($SetName, $SetCat, $_SESSION['parentuserid']);
else
	editCardSet($SetID, $SetName, $SetCat);
header("Location: editset.php?s=" . $SetID);
?>