<?php
session_start();

$ErrMsg = (!isset($_SESSION['errmsg'])) ? '' : $_SESSION['errmsg'];
$ErrMsg2 = (!isset($_SESSION['errmsg2'])) ? '' : $_SESSION['errmsg2'];
unset($_SESSION['errmsg']);
unset($_SESSION['errmsg2']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Flash Card Magic Study</title>
	<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
	<link rel="stylesheet" href="css/bootstrap-theme.min.css" type="text/css">
	<link rel="stylesheet" href="css/jquery-ui.min.css" type="text/css">
	<link rel="stylesheet" href="css/jquery-ui.theme.min.css" type="text/css">
	<link rel="stylesheet" href="css/jquery-ui.structure.min.css" type="text/css">
	<link rel="stylesheet" href="font-awesome/css/font-awesome.min.css" type="text/css">
	<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css">
	<link rel="stylesheet" href="css/flashcards.css"/>
	<script type="text/javascript" src="js/jquery-2.1.4.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui.min.js"></script>
</head>
<body>
<div class="container" role="main">
	<nav class="navbar navbar-inverse" role="navigation" style="margin-bottom: 0">
		<div class="navbar-header"><a class="navbar-brand" href="index.php">Flash Card Magic</a></div>
<?php
if((!isset($_SESSION['userid'])) || ($_SESSION['userid'] == ''))
{
?>
			<ul class="nav navbar-top-links navbar-nav">
			<li><a href="index.php">Home</a></li>
			</ul>
<?php
}
else
{
?>
			<ul class="nav navbar-top-links navbar-nav">
<?php
			if($_SESSION['userid'] == $_SESSION['parentuserid'])
			{
				echo "<li><a href='addStudent.php'>Add Student</a></li>\n";
				echo "<li><a href='addCourse.php'>Add Course</a></li>\n";
			}
?>
			</ul>
			<ul class="nav navbar-top-links navbar-right" style='margin-right: 10px;'><li><a href="logout.php" style='height: 20px;'><button type='button' class='btn btn-danger btn-sm' title='Logout'><span class='fa fa-power-off'></span> Logout</button></a></li></ul>
<?php
}	
?>
	</nav>
	<div class="page-header"><strong>Flash Card Magic</strong></div>