<?php
include_once("db.inc.php");
include_once("header.inc.php");
include_once("flash.inc.php");
$CategoryID = (isset($_GET['c'])) ? pebkac($_GET['c'], 6) : 0;
$CardCategories = getUserCardSetCategories(-99);
$CardCategories = getCardSetCategories();
// $CategoryCount = getUserCardSetCategoryCount(-99);
$CardSets = getUserCardSets(-99, $CategoryID);
$CardCounts = getUserCardSetCounts(-99, $CategoryID);
$UsrCardSets = getUserCardSets($_SESSION['parentuserid'], $CategoryID);
$AllowEdit = 0;
if($_SESSION['userid'] == $_SESSION['parentuserid'])
	$AllowEdit = 1;
$UserSets = array();
foreach($UsrCardSets AS $SetID => $SetRec)
{
	if($SetRec['originalcardsetid'] != '')
		$UserSets[$SetRec['originalcardsetid']] = $SetRec['originalcardsetid'];
}
?>
<div class='row'>
	<div class='col-md-3'>
		<div class='btn-group btn-group-sm'>
<?php
foreach($CardCategories AS $ArrCategoryID => $CategoryRec)
{
	if($CategoryRec['parentcategoryid'] == 0)
		echo "<a href='categories.php?c=" . $ArrCategoryID . "' class='btn btn-default btn-block'>" . $CategoryRec['categoryname'] . "</a><br>";
}
?>
		</div>
	</div>
	<div class='col-md-9'>
<?php	
if($CardCategories[$CategoryID]['parentcategoryid'] != 0)
	echo "<p><a href='categories.php?c=" . $CardCategories[$CategoryID]['parentcategoryid'] . "'>Back to " . $CardCategories[$CardCategories[$CategoryID]['parentcategoryid']]['categoryname'] . "</a></p>";
$InLoop = 0;	
foreach($CardCategories AS $ArrCategoryID => $CategoryRec)
{
	if($CategoryRec['parentcategoryid'] == $CategoryID)
	{
		if($InLoop == 0)
		{
			echo "<div class='panel panel-default'>\n";
			echo "<div class='panel-heading'><strong>Subcategories</strong>";
			echo "<button class='btn btn-primary btn-xs pull-right' onclick='$(\"#subcat\").toggleClass(\"hidden\"); $(\"#chv_subcat\").toggleClass(\"fa-chevron-down\");'>";
			echo "<span class='fa fa-chevron-up' id='chv_subcat'></span></button>";
			echo "</div>\n";
			echo "<div class='panel-body' id='subcat'>";
		}
		$InLoop = 1;
		echo "<a href='categories.php?c=" . $ArrCategoryID . "'>" . $CategoryRec['categoryname'] . "</a><br>";
	}
}
if($InLoop == 1)
	echo "</div>\n</div>\n";
?>
		<div class='row'>
			<div class='col-md-12'>
<?php
if(count($CardSets) > 0)
{
	$OldCategory = 0;
	foreach($CardSets AS $CardSetID => $CardSetRec)
	{
		$Disabled = '';
		if(isset($UserSets[$CardSetID]))
			$Disabled = ' disabled="disabled"';
		if($OldCategory != $CardSetRec['categoryid'])
		{
			if($OldCategory != 0)
				echo "</div>\n</div>\n";
			echo "<div class='panel panel-default'>\n";
			echo "<div class='panel-heading'><strong>" . $CardCategories[$CardSetRec['categoryid']]['categoryname'] . "</strong>";
			echo "<button class='btn btn-primary btn-xs pull-right' onclick='$(\"#window_" . $CardSetRec['categoryid'] . "\").toggleClass(\"hidden\"); $(\"#chv_" . $CardSetRec['categoryid'] . "\").toggleClass(\"fa-chevron-down\");'>";
			echo "<span class='fa fa-chevron-up' id='chv_" . $CardSetRec['categoryid'] . "'></span></button>";
			echo "</div>\n";
			echo "<div class='panel-body' id='window_" . $CardSetRec['categoryid'] . "'>\n";
			$OldCategory = $CardSetRec['categoryid'];
		}
		$CardCount = (isset($CardCounts[$CardSetID])) ? $CardCounts[$CardSetID] : 0;
		echo "<div class='row'><div class='col-md-6'>" . $CardSetRec['setname'] . " <span class='badge'>" . $CardCount . " cards</span></div>\n";
		echo "<div class='col-md-6'><div class='btn-group'><a href='viewCardSet.php?s=" . $CardSetID . "' class='btn btn-primary'" . $Disabled . "><span class='fa fa-eye'></span> View</a>\n";
		if($AllowEdit == 1)
			echo "<a href='SetSubscription.php?s=" . $CardSetID . "' class='btn btn-success'" . $Disabled . "><span class='fa fa-plus'></span> Add to my collection</a>\n";
		echo "</div></div></div>\n";
	}
	echo "</div>\n</div>\n";
}
else
{
	echo "<div class='panel panel-default'>\n";
	echo "<div class='panel-heading'><strong>No cardsets in this category</strong></div>\n";
	echo "<div class='panel-body'>";
	echo "<p>There are no cardsets in this category.</p>";
	echo "</div>\n</div>\n";
}
?>
			</div>
		</div>
	</div>
</div>
<?php
include("footer.inc.php");
?>