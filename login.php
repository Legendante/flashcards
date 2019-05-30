<?php
session_start();
include("db.inc.php");
include("flash.inc.php");

$Username = pebkac($_POST['username'], 200, 'STRING');
$Password = pebkac($_POST['userpass'], 200, 'STRING');

$insQry = 'SELECT userid, email, passsword, parentuserid FROM userdetails WHERE MD5(email) = MD5("' . $Username . '")';
$selRes = mysqli_query($dbCon, $insQry) or logDBError(mysqli_error($dbCon), $insQry, __FILE__, __FUNCTION__, __LINE__);
$selData = mysqli_fetch_array($selRes);
if($selData['userid'] == '')
{
	$_SESSION['errmsg'] = 'Login Failed';
}
else
{
	$HashPw = saltAndPepper($Username, $Password);
	if(password_verify($HashPw, $selData['passsword']))
	{
		$_SESSION['userid'] = $selData['userid'];
		$_SESSION['parentuserid'] = $selData['userid'];
	}
}
header("Location: index.php");
?>