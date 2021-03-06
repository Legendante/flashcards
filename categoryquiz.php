<?php
include_once("db.inc.php");
include_once("header.inc.php");
include_once("flash.inc.php");
?>
<div class='row'>
	<div class='col-md-3'>

	
	</div>
	<div class='col-md-9'>
<?php
if(isset($_GET['c']))
{
	$CategoryID = pebkac($_GET['c'], 6);
	$CardSets = getUserCardSets($_SESSION['parentuserid'], $CategoryID);
	echo "<form method='POST' action='categoryquiz.php' id='quizfrm'>";
	echo "<input type='hidden' name='CategoryID' id='CategoryID' value='" . $CategoryID . "'>";
	echo "<div class='row'><div class='col-md-12'><strong>Please select the cardsets</strong></div></div>";
	echo "<div class='row'><div class='col-md-12'>Please select which cardsets you would like to use in this quiz</div></div>";
	foreach($CardSets AS $CardSetID => $CardSetRec)
	{
		echo "<div class='row'><div class='col-md-2'><input type='checkbox' name='cardsetlist[]' id='cset_" . $CardSetID . "' value='" . $CardSetID . "'></div>";
		echo "<div class='col-md-10'><label for='cset_" . $CardSetID . "'>" . $CardSetRec['setname'] . "</label></div></div>\n";
	}
	echo "<div class='row'><div class='col-md-12'><button type='submit' class='btn btn-primary pull-right'><span class='fa fa-play'></span> Quiz Me</button></div></div>";
	echo "</form>";
}
else
{
	if(isset($_POST['cardsetlist']))
	{
		$CategoryID = (isset($_POST['CategoryID'])) ? pebkac($_POST['CategoryID'], 6) : 0;
		$CardArr = array();
		$TmpArr = array();
		foreach($_POST['cardsetlist'] AS $CardSetID)
		{
			$TmpArr = getCardsFromCardSet($CardSetID);
			foreach($TmpArr AS $CardID => $CardRec)
			{
				$CardArr[$CardID] = $CardID;
			}
		}
		$StudyID = startTempSession($_SESSION['userid'], $CardArr, 2, NULL, $CategoryID);
	}
	if(isset($_POST['sessionid']))
	{
		$StudyID = (isset($_POST['sessionid'])) ? pebkac($_POST['sessionid'], 6) : 0;
		$LastID = (isset($_POST['lastcard'])) ? pebkac($_POST['lastcard'], 6) : 0;
		$Result = (isset($_POST['cardresult'])) ? pebkac($_POST['cardresult'], 1) : 0;
		$Given = (isset($_POST['answergiven'])) ? pebkac($_POST['answergiven'], 100, 'STRING') : '';
		setTempSessionCardPlayed($StudyID, $LastID, $Result, $Given); //, $QASwapped = 0)
	}
	$CategoryID = pebkac($_POST['CategoryID'], 6);
	$Category = getCategoryDetails($CategoryID);
	$SessionCount = getTempSessionCardCount($StudyID);
	if($SessionCount[0] == 0)
	{
		$SessionDets = getTempSessionByID($StudyID);
		$Results = getTempSessionResults($StudyID);
		$TotalNum = 0;
		$NumRight = 0;
		$NumWrong = 0;
		foreach($Results AS $Cnt => $Result)
		{
			$TotalNum++;
			if($Result['cardresult'] == 1)
				$NumRight++;
			elseif($Result['cardresult'] == 0)
				$NumWrong++;
		}
		$Percentage = ($TotalNum > 0 ) ? sprintf("%02d", ($NumRight * 100) / $TotalNum) : 0;
		$BGCol = 'progress-bar-danger';
		if($Percentage >= 80)
			$BGCol = 'progress-bar-success';
		elseif($Percentage >= 40)
			$BGCol = 'progress-bar-warning';
		echo "<div class='row'><div class='col-md-12'>\n";
		echo "<div class='panel panel-default'>\n";
		echo "<div class='panel-heading'><strong>Done!</strong>";
		echo "<div class='progress'>\n";
		echo "<div class='progress-bar " . $BGCol . " progress-bar-striped' role='progressbar' aria-valuenow='" . $Percentage . "' aria-valuemin='0' aria-valuemax='100' style='width:" . $Percentage . "%;'>";
		echo "<span># Questions answered: " . $TotalNum . " Correct: " . $NumRight . " Wrong: " . $NumWrong . " (" . $Percentage . "%)</span></div></div></div>\n";
		echo "<div class='panel-body'>\n";
		echo "<div class='row'><div class='col-md-3'><strong>Asked</strong></div><div class='col-md-3'><strong>Answer</strong></div><div class='col-md-3'><strong>Given</strong></div><div class='col-md-1'><strong>Result</strong></div></div>\n";
		foreach($Results AS $CardID => $ResRec)
		{
			$Card = getCardByID($CardID);
			$ShowResult = ($ResRec['cardresult'] == 1) ? '<button class="btn btn-success"><span class="fa fa-check"></span></button>' : '<button class="btn btn-danger"><span class="fa fa-times"></span></button>';
			$Question = $Card['cardquestion'];
			$Answer = $Card['cardanswer'];
			$GivenAnswer = $ResRec['givenanswer'];
			if($Card['manualanswer'] == 0)
				$GivenAnswer = '***';
			if($ResRec['qaswapped'] == 1)
			{
				$Question = $Card['cardanswer'];
				$Answer = $Card['cardquestion'];
			}
			echo "<div class='row'><div class='col-md-3'>" . $Question . "</div><div class='col-md-3'>" . $Answer . "</div><div class='col-md-3'>" . $GivenAnswer . "</div><div class='col-md-1'>" . $ShowResult . "</div></div>\n";
		}
		echo "</div></div>\n";
		echo "</div></div>\n";
		exit();
	}
	$QuizSize = ($SessionCount[0] + $SessionCount[1]);
	$QuizNum = ($QuizSize - $SessionCount[0]) + 1;
	$CardID = getTempSessionCardID($StudyID, 1);
	$Card = getCardByID($CardID);	
?>
	<div class='row'>
		<div class='col-md-12'>
			<div class='panel panel-default'>
			<div class='panel-heading'><strong>Question:</strong> <?php echo $Card['cardquestion']; ?><span class='pull-right'><?php echo $QuizNum . " of " . $QuizSize; ?></span></div>
			<div class='panel-body'>
				<form id='quizfrm' onsubmit='checkAnswer(); return false;'>
<?php
if($Card['manualanswer'] == 1)
	echo "<input type='text' name='given' id='given'>";
?>	
				<button id='showbutton' type='button' class='btn btn-warning pull-right' onclick='$("#quizfrm").submit();'><span class='fa fa-question'></span> Answer</button>
				</form>
				<div id='answerbox' style='display: none;'><strong>Answer</strong> <?php echo $Card['cardanswer']; ?></div>
				<div class='btn-toolbar pull-right'>
<?php
if($Card['manualanswer'] == 1)
	echo "<button onclick='nextQuestion(-1);' id='nxtbutton' class='btn' style='display: none;'>Next card <span class='fa fa-toggle-right fa-lg'></span></button>";
else
	echo "<button onclick='nextQuestion(0);' id='wrongbutton' class='btn btn-warning' style='display: none;'>Wrong <span class='fa fa-frown-o fa-lg'></span></button>";
	echo "<button onclick='nextQuestion(1);' id='rightbutton' class='btn btn-success' style='display: none;'><span class='fa fa-smile-o fa-lg'></span> Correct!</button>";
?>
				</div></div>
				
			</div>
		</div>
	</div>
	<form method='POST' action='categoryquiz.php' id='answerFrm'>
		<input type='hidden' name='sessionid' id='sessionid' value='<?php echo $StudyID; ?>'>
		<input type='hidden' name='lastcard' id='lastcard' value='<?php echo $CardID; ?>'>
		<input type='hidden' name='CategoryID' id='CategoryID' value='<?php echo $CategoryID; ?>'>
		<input type='hidden' name='cardresult' id='cardresult' value=''>
		<input type='hidden' name='answergiven' id='answergiven' value=''>
	</form>
<script>
$(document).ready(function()
{
	$("#given").focus();
});
function nextQuestion(res)
{
	if(res != -1)
	{
		$('#cardresult').val(res);
	}
	$("#answerFrm").submit();
}

function checkAnswer()
{
<?php
if($Card['manualanswer'] == 1)
{
?>	
	var answer = "<?php echo $Card['cardanswer']; ?>";
	var given = $("#given").val();
	$('#answergiven').val(given);
	if(answer == given)
	{
		$('#cardresult').val(1);
		$('#nxtbutton').addClass('btn-success');
		$('#nxtbutton').html('Correct! Next card');
	}
	else
	{
		$('#cardresult').val(0);
		$('#nxtbutton').addClass('btn-danger');
		$('#nxtbutton').html('Wrong! Next card');
		$('#answerbox').show();
	}
	$('#showbutton').hide();
	$('#nxtbutton').show();
	$('#nxtbutton').focus();
<?php
}
else
{
?>
	$('#answerbox').show();
	$('#showbutton').hide();
	$('#wrongbutton').show();
	$('#rightbutton').show();
<?php
}
?>
}
</script>
<?php
}
?>
	</div>
</div>
<?php
include("footer.inc.php");
?>