<?php
session_start();
include_once("db.inc.php");
// include_once("header.inc.php");
include_once("flash.inc.php");

$SetID = pebkac($_GET['s'], 6);
$CardID = pebkac($_GET['c'], 6);
deleteCard($CardID, $SetID);
header("Location: editset.php?s=" . $SetID);
?>