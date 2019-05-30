<?php
include("db.inc.php");
include("flash.inc.php");
$Username = pebkac($_POST['email'], 200, 'STRING');
$Password = pebkac($_POST['passwd'], 200, 'STRING');
$Firstname = pebkac($_POST['fname'], 200, 'STRING');
$Lastname = pebkac($_POST['lname'], 200, 'STRING');
$HashPw = hashPassword($Username, $Password);
$insQry = 'INSERT INTO userdetails(email, passsword, firstname, lastname) VALUES ("' . $Username . '", "' . $HashPw . '", "' . $Firstname . '", "' . $Lastname . '")';
$selRes = mysqli_query($dbCon, $insQry) or logDBError(mysqli_error($dbCon), $insQry, __FILE__, __FUNCTION__, __LINE__);
$NewUserID = mysqli_insert_id($dbCon);
$updQry = 'UPDATE userdetails SET parentuserid = ' . $NewUserID . ' WHERE userid = ' . $NewUserID . ' AND email = "' . $Username . '"';
$selRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
header("Location: index.php");
?>