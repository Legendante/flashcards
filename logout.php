<?php
session_start();
$_SESSION['userid'] = '';
$_SESSION['parentuserid'] = '';
unset($_SESSION['userid']);
unset($_SESSION['parentuserid']);
header("Location: index.php");
?>