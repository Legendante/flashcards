<?php
session_start();
include_once("db.inc.php");
include_once("flash.inc.php");
$SetID = (isset($_GET['s'])) ? pebkac($_GET['s'], 6) : 0;
$CardID = (isset($_GET['c'])) ? pebkac($_GET['c'], 6) : 0;
$Direction = (isset($_GET['d']) && ($_GET['d'] == 1)) ? 1 : 0;
if($Direction == 1)
	moveCardUp($SetID, $CardID);
else
	moveCardDown($SetID, $CardID);
header("Location: editset.php?s=" . $SetID);
?>