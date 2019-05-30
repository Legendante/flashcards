<?php
include_once("db.inc.php");
include_once("header.inc.php");
include_once("flash.inc.php");
$CategoryID = (isset($_GET['c'])) ? pebkac($_GET['c'], 6) : 0;
$UserID = (isset($_GET['u'])) ? pebkac($_GET['u'], 6) : $_SESSION['userid'];
$Sessions = getStudySessions($UserID, '', $_SESSION['parentuserid'], 2, $CategoryID);
$Results = getStudySessionResults($UserID, '', $_SESSION['parentuserid'], 2, $CategoryID);
// $Cards = getCardsFromCardSet($SetID);
echo "<div class='row'>";
if($_SESSION['userid'] == $_SESSION['parentuserid'])
{
	$Students = getStudentDetails($_SESSION['userid']);
	echo "<div class='col-md-3'>";
	echo "<p><a href='categoryquizresults.php?c=" . $CategoryID . "&u=" . $_SESSION['userid'] . "'>Mine</a></p>";
	foreach($Students AS $StudentID => $StudentRec)
	{
		echo "<p><a href='categoryquizresults.php?s=" . $CategoryID . "&u=" . $StudentID . "'>" . $StudentRec['firstname'] . " " . $StudentRec['lastname'] . "</a></p>";
	}
	echo "</div>";
	echo "<div class='col-md-9'>";
}
else
	echo "<div class='col-md-12'>";
foreach($Sessions AS $SessionID => $SessionRec)
{
	$NumWrong = 0;
	$NumRight = 0;
	$TotalNum = 0;
	$Percentage = '-';
	if(isset($Results[$SessionID]))
	{
		foreach($Results[$SessionID] AS $Cnt => $Result)
		{
			if($Result['cardcomplete'] == 1)
			{
				$TotalNum++;
				if($Result['result'] == 1)
					$NumRight++;
				elseif($Result['result'] == 0)
					$NumWrong++;
			}
		}
		$Percentage = ($TotalNum > 0 ) ? sprintf("%02d", ($NumRight * 100) / $TotalNum) : 0;
	}
	$BGCol = 'progress-bar-danger';
	if($Percentage >= 80)
		$BGCol = 'progress-bar-success';
	elseif($Percentage >= 40)
		$BGCol = 'progress-bar-warning';
	echo "<div class='row'><div class='col-md-12'>\n";
	echo "<div class='panel panel-default'>\n";
	echo "<div class='panel-heading'>Done: " . $SessionRec['sessionstart'];
	echo "<div class='progress'>\n";
	echo "<div class='progress-bar " . $BGCol . " progress-bar-striped' role='progressbar' aria-valuenow='" . $Percentage . "' aria-valuemin='0' aria-valuemax='100' style='width:" . $Percentage . "%'>";
	echo "<span># Questions answered: " . $TotalNum . " Correct: " . $NumRight . " Wrong: " . $NumWrong . " (" . $Percentage . "%)</span></div></div></div>\n";
	echo "<div class='panel-body'>\n";
	echo "<div class='row'><div class='col-md-3'><strong>Asked</strong></div><div class='col-md-3'><strong>Answer</strong></div><div class='col-md-3'><strong>Given</strong></div><div class='col-md-1'><strong>Result</strong></div></div>\n";
	if(isset($Results[$SessionID]))
	{
		foreach($Results[$SessionID] AS $Cnt => $Result)
		{
			$Card = getCardByID($Result['cardid']);
			// if(isset($Cards[$Result['cardid']]))
			// {
				$ShowResult = ($Result['result'] == 1) ? '<button class="btn btn-success" title="Correct"><span class="fa fa-check"></span></button>' : '<button class="btn btn-danger" title="Wrong"><span class="fa fa-times"></span></button>';
				$ShowResult = ($Result['cardcomplete'] == 0) ? '<button class="btn btn-warning" title="Not answered"><span class="fa fa-minus"></span></button>' : $ShowResult;
				// $Question = $Cards[$Result['cardid']]['cardquestion'];
				// $Answer = $Cards[$Result['cardid']]['cardanswer'];
				$Question = $Card['cardquestion'];
				$Answer = $Card['cardanswer'];
				$GivenAnswer = $Result['givenanswer'];
				if($Result['qaswapped'] == 1)
				{
					// $Question = $Cards[$Result['cardid']]['cardanswer'];
					// $Answer = $Cards[$Result['cardid']]['cardquestion'];
					$Question = $Card['cardanswer'];
					$Answer = $Card['cardquestion'];
				}
				echo "<div class='row'><div class='col-md-3'>" . $Question . "</div><div class='col-md-3'>" . $Answer . "</div><div class='col-md-3'>" . $GivenAnswer . "</div><div class='col-md-1'>" . $ShowResult . "</div></div>\n";
			// }
		}
	}
	echo "</div></div>\n";
	echo "</div></div>\n";
}
echo "</div></div>";
include("footer.inc.php");
?>