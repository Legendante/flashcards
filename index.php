<?php
$ActivePage['index'] = "class='active'";
include("db.inc.php");
include("header.inc.php");

if(isset($_SESSION['studysession']))
{
	$_SESSION['studysession']['cardset'] = '';
	$_SESSION['studysession']['order'] = '';
	$_SESSION['studysession']['qandaorder'] = '';
	$_SESSION['studysession']['id'] = '';
	unset($_SESSION['studysession']['cardset']);
	unset($_SESSION['studysession']['order']);
	unset($_SESSION['studysession']['qandaorder']);
	unset($_SESSION['studysession']['id']);
	$_SESSION['studysession'] = '';
	unset($_SESSION['studysession']);
}

unset($_SESSION['quizcards']);
unset($_SESSION['quizsize']);
unset($_SESSION['quiznum']);
unset($_SESSION['quizcategory']);
unset($_SESSION['quizset']);
unset($_SESSION['quizresult']);

if($ErrMsg != '')
	$ErrMsg = "<div class='row'><div class='col-md-12 bg-danger'>" . $ErrMsg . "</div></div>";
if((!isset($_SESSION['userid'])) || ($_SESSION['userid'] == ''))
	include_once("notloggedin.php");
else
	include_once("loggedinindex.php");
include("footer.inc.php");
?>