<?php
include_once("db.inc.php");
include_once("header.inc.php");
include_once("flash.inc.php");
$CardSetCategories = getUserCardSetCategories($_SESSION['parentuserid']);
$CategoryCount = getUserCardSetCategoryCount($_SESSION['parentuserid']);
$PublicSetCategories = getCardSetCategories();
$PublicCategoryCount = getUserCardSetCategoryCount(-99);
$CategoryID = (isset($_GET['c'])) ? pebkac($_GET['c'], 6) : 0;

$CardSets = getUserCardSets($_SESSION['parentuserid'], $CategoryID);
$CardCounts = getUserCardSetCounts($_SESSION['parentuserid'], $CategoryID);
$MyCourses = getUserCourses($_SESSION['parentuserid']);
$ResCounts = getStudySessionCounts($_SESSION['userid'], $_SESSION['parentuserid'], 0);
$CatResCounts = getStudySessionCounts($_SESSION['userid'], $_SESSION['parentuserid'], 2);
// STUDY MODE
// Learn
// Quiz
// Review
// PRINT OPTION
// Study Guide
// Single sided cards
// Double sided cards
?>
<div class='row'>
	<div class='col-md-3'>
		<p><strong>My categories</strong></p>
		<ul class="list-group">
<?php
foreach($CardSetCategories AS $LoopCategoryID => $CategoryRec)
{
	if($CategoryRec['parentcategoryid'] == 0)
		echo "<li class='list-group-item active'><a href='index.php?c=" . $LoopCategoryID . "' style='color: #FFFFFF;'>" . $CategoryRec['categoryname'] . "</a></li>";
	foreach($CardSetCategories AS $SubCategoryID => $CategoryRec)
	{
		if($CategoryRec['parentcategoryid'] == $LoopCategoryID)
			echo "<li class='list-group-item list-group-item-info'><a href='index.php?c=" . $SubCategoryID . "' style='margin-left: 10px;'>" . $CategoryRec['categoryname'] . "</a></li>";
	}
}
?>
		</ul>
		<!-- <a href='categories.php' class='btn btn-default'>View Categories</a><br> -->
		<p><a href='editset.php' class='btn btn-success'><span class='fa fa-plus'></span> New Cardset</a></p>
		
		<p><strong><a href='categories.php'>Available categories</a></strong></p>
<?php
foreach($PublicSetCategories AS $LoopCategoryID => $CategoryRec)
{
	if($CategoryRec['parentcategoryid'] == 0)
		echo "<p><a href='categories.php?c=" . $LoopCategoryID . "'>" . $CategoryRec['categoryname'] . "</a></p>";
}
?>
		<p><strong>My courses</strong></p>
<?php
foreach($MyCourses AS $LoopCategoryID => $CategoryName)
{
	echo "<p><a href='index.php?c=" . $LoopCategoryID . "'>" . $CategoryName . " <span class='badge'>" . $CategoryCount[$LoopCategoryID] . "</span></a></p>";
}
?>
	</div>
	<div class='col-md-9'>
		<div class='row'>
			<div class='col-md-12'>
<?php
if(count($CardSets) > 0)
{
	$OldCategory = 0;
	foreach($CardSets AS $CardSetID => $CardSetRec)
	{
		if($OldCategory != $CardSetRec['categoryid'])
		{
			if($OldCategory != 0)
				echo "</div>\n</div>\n";
			$CategoryStr = $CardSetCategories[$CardSetRec['categoryid']]['categoryname'];
			if($CardSetCategories[$CardSetRec['categoryid']]['parentcategoryid'] != 0)
				$CategoryStr = $CardSetCategories[$CardSetCategories[$CardSetRec['categoryid']]['parentcategoryid']]['categoryname'] . " -> " . $CategoryStr;
			echo "<div class='panel panel-default'>\n";
			echo "<div class='panel-heading'><div class='row'><div class='col-md-7'><strong>" . $CategoryStr . "</strong></div>";
			echo "<div class='col-md-5'><div class='btn-group btn-group-sm pull-right'>";
			echo "<button class='btn btn-primary' style='margin-left: 5px;' onclick='$(\"#window_" . $CardSetRec['categoryid'] . "\").toggleClass(\"hidden\"); $(\"#chv_" . $CardSetRec['categoryid'] . "\").toggleClass(\"fa-chevron-down\");'>";
			echo "<span class='fa fa-chevron-up' id='chv_" . $CardSetRec['categoryid'] . "'></span>&nbsp;</button></div>";
			echo "<div class='btn-group btn-group-sm pull-right'>";
			echo "<a href='categoryquiz.php?c=" . $CardSetRec['categoryid'] . "' class='btn btn-success'><span class='fa fa-clock-o'></span> Category Quiz</a>";
			if(isset($CatResCounts[$CardSetRec['categoryid']]))
				echo "<a href='categoryquizresults.php?c=" . $CardSetRec['categoryid'] . "' class='btn btn-danger'><span class='fa fa-line-chart'></span> Category Results</a>";
			else
				echo "<button class='btn btn-danger' disabled='disabled' title='No results available'><span class='fa fa-line-chart'></span> Category Results</button>";
			echo "</div></div></div>";
			echo "</div>\n";
			echo "<div class='panel-body' id='window_" . $CardSetRec['categoryid'] . "'>\n";
			$OldCategory = $CardSetRec['categoryid'];
		}
		$Count = (isset($CardCounts[$CardSetID])) ? $CardCounts[$CardSetID] : 0;
		echo "<div class='row'><div class='col-md-5'><a href='editset.php?s=" . $CardSetID . "'><span class='fa fa-edit'></span> " . $CardSetRec['setname'] . " <span class='badge btn-info'>" . $Count . " cards</span></a></div>";
		echo "<div class='col-md-7'><div class='btn-group btn-group-block btn-group-sm'><a href='learn.php?s=" . $CardSetID . "' class='btn btn-info'><span class='fa fa-pencil'></span> Learn</a>";
		echo "<a href='study.php?s=" . $CardSetID . "' class='btn btn-primary'><span class='fa fa-graduation-cap'></span> Study</a>";
		echo "<a href='quiz.php?s=" . $CardSetID . "' class='btn btn-success'><span class='fa fa-question'></span> Quiz</a>";
		echo "<button href='' class='btn btn-warning' onclick='launchSession(" . $CardSetID . ");'><span class='fa fa-clock-o'></span> Test Session</button></div>";
		if(isset($ResCounts[$CardSetID]))
			echo "<a href='results.php?s=" . $CardSetID . "' class='btn btn-danger btn-sm pull-right'><span class='fa fa-line-chart'></span> Results</a>";
		else
			echo "<button class='btn btn-danger btn-sm pull-right' disabled='disabled' title='No results available'><span class='fa fa-line-chart'></span> Results</button>";
		echo "</div></div>";
	}
	echo "</div>\n</div>\n";
}
else
{
	echo "<div class='panel panel-default'>\n";
	echo "<div class='panel-heading'><strong>No cardsets</strong></div>\n";
	echo "<div class='panel-body'>";
	echo "<p>You currently have no cardsets on your profile.</p>";
	echo "<p>You can add a <a href='editset.php' class='btn btn-success'><span class='fa fa-plus'></span> New Cardset</a></p>";
	echo "</div>\n</div>\n";
}
?>
			</div>
		</div>
	</div>
</div>
<div class='modal fade' id='session-modal' tabindex='-1' role='dialog' aria-hidden='true'>
	<div class='modal-dialog modal-xs'>
		<div class='modal-content'>
			<div class='modal-header'><strong>Start Test Session</strong><a href="#" class="close" data-dismiss="modal" class='pull-right'>&times;</a></div>
			<div class='modal-body'>
				<form method='POST' action='examsession.php' id='sessionfrm'>
					<input type='hidden' name='sessCardSet' id='sessCardSet' value=''>
					<div class='row'><div class='col-md-12'>You're about to start a test session. Please set the session parameters</div></div>
					<div class='row'><div class='col-md-4'>Random order</div><div class='col-md-8'><input type='checkbox' name='randomord' id='randomord' checked='checked' value='R'></div></div>
					<div class='row'><div class='col-md-4'>Questions/Answers</div><div class='col-md-8'>
						<input type='radio' name='qaorder' id='qaorder0' value='0' checked='checked'> <label for='qaorder0'>Normal</label><br>
						<input type='radio' name='qaorder' id='qaorder1' value='1'> <label for='qaorder1'>Swapped</label><br>
						<input type='radio' name='qaorder' id='qaorder2' value='2'> <label for='qaorder2'>Random swap</label><br>
					</div></div>
				</form>
			</div>
			<div class='modal-footer'><button type='button' class='btn btn-default btn-sm' data-dismiss='modal'><span class='glyphicon glyphicon-remove'></span> Close</button>
			<button class='btn btn-primary btn-sm' onclick='$("#sessionfrm").submit();'><span class='fa fa-clock-o'></span> Launch session</button></div>
		</div>
	</div>
</div>
<script>
function launchSession(CardSetID)
{
	$('#sessCardSet').val(CardSetID);
	$('#session-modal').modal('show');
}
</script>