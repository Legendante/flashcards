<?php
session_start();
include("db.inc.php");
include("flash.inc.php");

$Username = pebkac($_POST['email'], 200, 'STRING');
$Password = pebkac($_POST['passwd'], 200, 'STRING');
$Firstname = pebkac($_POST['fname'], 200, 'STRING');
$Lastname = pebkac($_POST['lname'], 200, 'STRING');

$selQry = 'SELECT userid FROM userdetails WHERE parentuserid = ' . $_SESSION['userid'] . ' AND email = "' . $Username . '"';
$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
$selData = mysqli_fetch_array($selRes);
if($selData['userid'] != '')
{
	$_SESSION['errmsg'] = 'A Student with that username is already registered in your profile';
	header("Location: addStudent.php");
}
else	
{
	$HashPw = hashPassword($Username, $Password);
	$insQry = 'INSERT INTO userdetails(email, passsword, parentuserid, firstname, lastname) VALUES ("' . $Username . '", "' . $HashPw . '", "' . $_SESSION['userid'] . '", "' . $Firstname . '", "' . $Lastname . '")';
	$selRes = mysqli_query($dbCon, $insQry) or logDBError(mysqli_error($dbCon), $insQry, __FILE__, __FUNCTION__, __LINE__);
	header("Location: index.php");
}
?>