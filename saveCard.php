<?php
session_start();
include_once("db.inc.php");
// include_once("header.inc.php");
include_once("flash.inc.php");

$SetID = pebkac($_POST['SetID'], 6);
$CardID = pebkac($_POST['CardID'], 6);
$Question = trim(pebkac($_POST['question'], 255, 'STRING'));
$Answer = trim(pebkac($_POST['answer'], 100, 'STRING'));
$ManualAnswer = trim(pebkac($_POST['manualanswer'], 100, 'STRING'));
if($CardID == 0)
	$CardID = addCard($Question, $Answer, $SetID, $ManualAnswer);
else
	editCard($CardID, $Question, $Answer, $SetID, $ManualAnswer);

header("Location: editset.php?s=" . $SetID);
?>