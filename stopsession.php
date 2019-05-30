<?php
$KeepExamSession = 1;
include_once("db.inc.php");
include_once("header.inc.php");
include_once("flash.inc.php");
stopStudySession($_SESSION['studysession']['id']);
$SetID = $_SESSION['studysession']['cardset'];
$QuestionOrder = $_SESSION['studysession']['order'];
$QAOrder = $_SESSION['studysession']['qandaorder'];
if($_POST['cardresult'] != '')
{
	$PrevCard = (isset($_POST['cardid'])) ? pebkac($_POST['cardid'], 6) : 0;
	$Result = ((isset($_POST['cardresult'])) && ($_POST['cardresult'] == 1)) ? 1 : 0;
	$QASwapped = (isset($_POST['qaorder'])) ? pebkac($_POST['qaorder'], 6) : 0;	// 0 - No, 1 - Yes, 2 - Random
	$AnswerGiven = (isset($_POST['answergiven'])) ? pebkac($_POST['answergiven'], 100, 'STRING') : '';
	saveStudyCard($_SESSION['studysession']['id'], $PrevCard, $Result, $QASwapped, $AnswerGiven);
}

$CardCategories = getCardSetCategories();
$CardSet = getCardSetByID($SetID);
$Category = getCategoryDetails($CardSet['categoryid']);
$Results = getSessionResults($_SESSION['studysession']['id']);
$Cards = getCardsFromCardSet($SetID);
$NumWrong = 0;
$NumRight = 0;
$TotalNum = 0;
$Percentage = '-';
foreach($Results AS $Cnt => $Result)
{
	$TotalNum++;
	if($Result['result'] == 1)
		$NumRight++;
	elseif($Result['result'] == 0)
		$NumWrong++;
	$Percentage = sprintf("%0.2f", ($NumRight * 100) / $TotalNum);
}
?>
<div class='row'>
	<div class='col-md-3'>
	
	</div>
	<div class='col-md-9'>
		<div class='row'><div class='col-md-8'><?php echo $Category['categoryname'] . " - <strong>" . $CardSet['setname'] . "</strong>"; ?></div></div>
	<div class='row'><div class='col-md-12'>
<?php
	// echo "<p>You answered " . $TotalNum . " questions.</p>";
	// echo "<p>" . $NumRight . " correct.</p>";
	// echo "<p>" . $NumWrong . " wrong.</p>";
	// echo "</div>";
	// echo "</div></div>";
	echo "<div class='panel panel-default'>";
	echo "<div class='panel-heading'># Questions answered: " . $TotalNum . " Correct: " . $NumRight . " Wrong: " . $NumWrong . "  (" . $Percentage . "%)</div>";
	echo "<div class='panel-body'>";
	echo "<div class='row'><div class='col-md-3'><strong>Asked</strong></div><div class='col-md-3'><strong>Answer</strong></div><div class='col-md-3'><strong>Given</strong></div><div class='col-md-1'><strong>Result</strong></div></div>";
	// print_r($Results[$SessionID]);
	foreach($Results AS $Cnt => $Result)
	{
		$ShowResult = ($Result['result'] == 1) ? '<button class="btn btn-success"><span class="fa fa-check"></span></button>' : '<button class="btn btn-danger"><span class="fa fa-times"></span></button>';
		$Question = $Cards[$Result['cardid']]['cardquestion'];
		$Answer = $Cards[$Result['cardid']]['cardanswer'];
		$GivenAnswer = $Result['givenanswer'];
		if($Result['qaswapped'] == 1)
		{
			$Question = $Cards[$Result['cardid']]['cardanswer'];
			$Answer = $Cards[$Result['cardid']]['cardquestion'];
		}
		echo "<div class='row'><div class='col-md-3'>" . $Question . "</div><div class='col-md-3'>" . $Answer . "</div><div class='col-md-3'>" . $GivenAnswer . "</div><div class='col-md-1'>" . $ShowResult . "</div></div>";
	}
	echo "</div></div>";
	echo "</div>";
	echo "</div>";
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
include("footer.inc.php");
?>