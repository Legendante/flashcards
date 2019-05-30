<?php
session_start();
include_once("db.inc.php");
include_once("flash.inc.php");
$SetID = (isset($_GET['s'])) ? pebkac($_GET['s'], 6) : 0;
$SetID = addCardSetToUser($_SESSION['parentuserid'], $SetID);
$CardSet = getCardSetByID($SetID);
header("Location: categories.php?c=" . $CardSet['categoryid']);
?>