<?php
$KeepExamSession = 1;
include_once("db.inc.php");
include_once("header.inc.php");
include_once("flash.inc.php");
if(isset($_POST['sessCardSet']))
{
	$SetID = (isset($_POST['sessCardSet'])) ? pebkac($_POST['sessCardSet'], 6) : 0;
	$QuestionOrder = (isset($_POST['randomord'])) ? pebkac($_POST['randomord'], 6, 'STRING') : -1;
	$QAOrder = (isset($_POST['qaorder'])) ? pebkac($_POST['qaorder'], 6) : 0;	// 0 - No, 1 - Yes, 2 - Random
	$_SESSION['studysession']['order'] = $QuestionOrder;
	$_SESSION['studysession']['qandaorder'] = $QAOrder;
	$CardArr = array();
	$TmpArr = getCardsFromCardSet($SetID);
	foreach($TmpArr AS $CardID => $CardRec)
	{
		$CardArr[$CardID] = $CardID;
	}
	$StudyID = startTempSession($_SESSION['userid'], $CardArr, 0, $SetID);
}
else
{
	$LastID = (isset($_POST['cardid'])) ? pebkac($_POST['cardid'], 6) : 0;
	$Result = ((isset($_POST['cardresult'])) && ($_POST['cardresult'] == 1)) ? 1 : 0;
	$QASwapped = (isset($_POST['qaorder'])) ? pebkac($_POST['qaorder'], 6) : 0;	// 0 - No, 1 - Yes, 2 - Random
	$AnswerGiven = (isset($_POST['answergiven'])) ? pebkac($_POST['answergiven'], 100, 'STRING') : '';
	$StudyID = (isset($_POST['sessionid'])) ? pebkac($_POST['sessionid'], 6) : 0;
	setTempSessionCardPlayed($StudyID, $LastID, $Result, $AnswerGiven, $QASwapped);
}
$SessionDets = getTempSessionByID($StudyID);
$SessionCount = getTempSessionCardCount($StudyID);

if($SessionCount[0] == 0)
{
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
	echo "<div class='panel-heading'><strong>Done!</strong>\n";
	echo "<div class='progress'>\n";
	echo "<div class='progress-bar " . $BGCol . " progress-bar-striped' role='progressbar' aria-valuenow='" . $Percentage . "' aria-valuemin='0' aria-valuemax='100' style='width:" . $Percentage . "%'>\n";
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
$QuestionOrder = $_SESSION['studysession']['order'];
$QAOrder = $_SESSION['studysession']['qandaorder'];
$QuizSize = ($SessionCount[0] + $SessionCount[1]);
$QuizNum = ($QuizSize - $SessionCount[0]) + 1;
$Random = 0;
if($QuestionOrder == 'R')
	$Random = 1;
$CardID = getTempSessionCardID($StudyID, $Random);
$Card = getCardByID($CardID);	
$CardSet = getCardSetByID($SessionDets['cardsetid']);
$Category = getCategoryDetails($CardSet['categoryid']);
$Question = trim($Card['cardquestion']);
$Answer = trim($Card['cardanswer']);
if($QAOrder == 2)
	$QAOrder = mt_rand(0,1);
if($QAOrder == 1)
{
	$Question = $Card['cardanswer'];
	$Answer = $Card['cardquestion'];
}

?>
<div class='row'>
	<div class='col-md-3'>
	
	</div>
	<div class='col-md-9'>
		<div class='row'><div class='col-md-8'><?php echo $Category['categoryname'] . " - <strong>" . $CardSet['setname'] . "</strong>"; ?></div></div>
	<div class='row'><div class='col-md-12'>
	<div class='panel panel-default'>
	<div class='panel-heading'><strong>Question:</strong> <?php echo $Question; ?><span class='pull-right'><?php echo $QuizNum . " of " . $QuizSize; ?></span></div>
	<div class='panel-body'>
		<form id='quizfrm' onsubmit='checkAnswer(); return false;'>
<?php
if($Card['manualanswer'] == 1)
	echo "<input type='text' name='given' id='given'>";
?>			
			<button id='showbutton' type='button' class='btn btn-warning pull-right' onclick='$("#quizfrm").submit();'><span class='fa fa-question'></span> Answer</button>
		</form>
		<div id='answerbox' style='display: none;'><strong>Answer</strong> <?php echo $Answer; ?></div>
		<div class='btn-toolbar pull-right'>
<?php
if($Card['manualanswer'] == 1)
	echo "<button onclick='$(\"#resultfrm\").submit();' id='nxtbutton' class='btn pull-right' style='display: none;'>Next card <span class='fa fa-toggle-right fa-lg'></span></button>";
else
	echo "<button onclick='$(\"#cardresult\").val(0); $(\"#resultfrm\").submit();' id='wrongbutton' class='btn btn-warning pull-right' style='display: none;'>Wrong <span class='fa fa-frown-o fa-lg'></span></button>";
	echo "<button onclick='$(\"#cardresult\").val(1); $(\"#resultfrm\").submit();' id='rightbutton' class='btn btn-success pull-right' style='display: none;'><span class='fa fa-smile-o fa-lg'></span> Correct!</button>";
?>
		</div></div>
		
	</div>
		
	</div>
	</div>
	</div></div>
	</div>
</div>
<form method='POST' action='examsession.php' id='resultfrm'>";
	<input type='hidden' name='sessionid' id='sessionid' value='<?php echo $StudyID; ?>'>
	<input type='hidden' name='cardid' id='cardid' value='<?php echo $CardID; ?>'>
	<input type='hidden' name='cardresult' id='cardresult' value=''>
	<input type='hidden' name='answergiven' id='answergiven' value=''>
	<input type='hidden' name='qaorder' id='qaorder' value='<?php echo $QAOrder; ?>'>
</form>
<script>
$(document).ready(function()
{
	$("#given").focus();
});

function checkAnswer()
{
<?php
if($Card['manualanswer'] == 1)
{
?>	
	var answer = "<?php echo $Answer; ?>";
	var given = $("#given").val();
	$('#answergiven').val(given);
	var buttTxt = 'Next card';
<?php
if(count($_SESSION['sessioncards']) <= 1)
	echo "var buttTxt = 'Finished';";
else
	echo "var buttTxt = 'Next card';";
?>
	if(answer == given)
	{
		$('#cardresult').val(1);
		$('#nxtbutton').addClass('btn-success');
		$('#nxtbutton').html('Correct! ' + buttTxt);
	}
	else
	{
		$('#cardresult').val(0);
		$('#nxtbutton').addClass('btn-danger');
		$('#nxtbutton').html('Wrong! ' + buttTxt);
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
include("footer.inc.php");
?>