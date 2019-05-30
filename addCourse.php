<?php
include("db.inc.php");
include("header.inc.php");
include("flash.inc.php");
if($ErrMsg != '')
	$ErrMsg = '<div class="row"><div class="col-md-12 bg-danger">' . $ErrMsg . '</div></div>';
$CardSets = getUserOwnedCardSets($_SESSION['parentuserid']);
$CardCategories = getUserCardSetCategories($_SESSION['parentuserid']);
?>
<div class="page-header"><strong>Add a course</strong></div>
<form method='POST' action='doAddCourse.php' id='courseFrm'>
	<?php echo $ErrMsg; ?>
	<div class="row"><div class="col-md-2">Course Name:</div><div class="col-md-4"><input type='text' name='cname' id='cname' maxlength='200' class="validate[required] form-control input-sm"></div></div>
	<div class="row"><div class="col-md-12">Include cardsets:</div></div>
<?php
$OldCategory = 0;
$ColCount = 0;
foreach($CardSets AS $CardSetID => $SetRec)
{
	if($OldCategory != $SetRec['categoryid'])
	{
		if($OldCategory != 0)
			echo "</div>\n</div>\n";
		echo "<div class='panel panel-default'>\n";
		echo "<div class='panel-heading'><strong>" . $CardCategories[$SetRec['categoryid']] . "</strong>";
		echo "<button class='btn btn-primary btn-xs pull-right' onclick='$(\"#window_" . $SetRec['categoryid'] . "\").toggleClass(\"hidden\"); $(\"#chv_" . $SetRec['categoryid'] . "\").toggleClass(\"fa-chevron-down\");'>";
		echo "<span class='fa fa-chevron-up' id='chv_" . $SetRec['categoryid'] . "'></span></button>";
		echo "</div>\n";
		echo "<div class='panel-body' id='window_" . $SetRec['categoryid'] . "'>\n";
		$OldCategory = $SetRec['categoryid'];
	}
	if($ColCount == 0)
		echo "<div class='row'>";
	echo "<div class='col-md-1'><input type='checkbox' name='csets[]' id='cset_" . $CardSetID . "' value='" . $CardSetID . "'></div><div class='col-md-3'><label for='cset_" . $CardSetID . "'>" . $SetRec['setname'] . "</label></div>";
	$ColCount++;
	if($ColCount == 3)
	{
		$ColCount = 0;
		echo "</div>\n";
	}
}
if($ColCount != 0)
	echo "</div>\n";
echo "</div>\n</div>\n";
?>	
	
	
	<div class="row"><div class="col-md-6"><button type='submit' class="btn btn-info pull-right"><span class='fa fa-download'></span> Create course</button></div></div>
</form>
<script>
$(document).ready(function()
{
	$("#courseFrm").validationEngine();
});
</script>
<?php
include("footer.inc.php");
?>