<?php
include_once("serverconf.inc.php");

$dbCon = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);
if (mysqli_connect_errno()) 
	logDBError(mysqli_connect_error(), "Failed to connect to MySQL", __FILE__, __FUNCTION__, __LINE__);
mysqli_set_charset($dbCon, 'utf8');
function logDBError($Error, $MoreInfo, $File, $Function, $Line)
{
	global $dbCon;
	$Error = mysqli_real_escape_string($dbCon, $Error);
	$MoreInfo = mysqli_real_escape_string($dbCon, $MoreInfo);
	$File = mysqli_real_escape_string($dbCon, $File);
	$Function = mysqli_real_escape_string($dbCon, $Function);
	$Line = mysqli_real_escape_string($dbCon, $Line);
	
	echo "Error: " . $Error . "<br>\nMore Info: " . $MoreInfo . "<br>\nFile: " . $File . "<br>\nFunction: " . $Function . "<br>\nLine: " . $Line;
	$insQry = 'INSERT INTO errorlog(errormsg, moreinfo, filename, functionname, linenum, errordate, sessioninfo) VALUES ';
	$insQry .= '("' . $Error . '", "' . $MoreInfo . '", "' . $File . '", "' . $Function . '", "' . $Line . '", NOW(), "' . print_r($_SESSION, true) . '")';
	$selRes = mysqli_query($dbCon, $insQry);
	if(!$selRes)
	{
		$FileName = date("Ymd") . '_errors.log';
		$FileMsg = "[" . date("H:i:s") . "] First this happened :\n" . "Error: " . $Error . "\nMore Info: " . $MoreInfo . "\nFile: " . $File . "\nFunction: " . $Function . "\nLine: " . $Line . "\n\n";
		$FileMsg .= "[" . date("H:i:s") . "] Then this happened trying to log the error:\n" . "Error: " . mysqli_error($dbCon) . "\nMore Info: " . $insQry . "\nFile: " . __FILE__ . "\nFunction: " . __FUNCTION__ . "\nLine: " . __LINE__ . "\n\n";
		file_put_contents($FileName, $FileMsg, FILE_APPEND);
	}
}

function pebkac($InputVal, $MaxLength = 50, $VarType = 'INT')
{
	if($VarType == 'INT')
	{
		preg_match("/\d+/", $InputVal, $matches);
		$InputVal = $matches[0];
	}
	elseif($VarType == 'STRING')
	{
		$InputVal = str_replace('<', '&lt;', $InputVal);
		$InputVal = str_replace('>', '&gt;', $InputVal);
	}
	if($MaxLength > -1)
	{
		if(strlen($InputVal) > $MaxLength)
			logDBError("Dodgy Value", $InputVal, __FILE__, __FUNCTION__, __LINE__);
		if($MaxLength != '')
			$InputVal = substr($InputVal, 0, $MaxLength);
	}
	global $dbCon;
	$InputVal = mysqli_real_escape_string($dbCon, $InputVal);	
	return $InputVal;
}
?>