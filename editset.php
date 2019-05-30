<?php
include_once("db.inc.php");
include_once("header.inc.php");
include_once("flash.inc.php");
$SetID = (isset($_GET['s'])) ? pebkac($_GET['s'], 6) : 0;
$CardCategories = getCardSetCategories();
$CardSet = getCardSetByID($SetID);
$Cards = getCardsFromCardSet($SetID);
$MaxOrder = getCardsMaxOrderFromCardSet($SetID);
$AllowEdit = 0;
if($_SESSION['userid'] == $_SESSION['parentuserid'])
	$AllowEdit = 1;
$CategoryStr = $CardCategories[$CardSet['categoryid']]['categoryname'];
if($CardCategories[$CardSet['categoryid']]['parentcategoryid'] != 0)
{
	$ParentCat = getCategoryDetails($CardCategories[$CardSet['categoryid']]['parentcategoryid']);
	$CategoryStr = $ParentCat['categoryname'] . " -> " . $CategoryStr;
}
?>
<script>
function editCard(cardid)
{
	clearForm();
	$('#CardID').val(cardid);
	if($('#manual_' + cardid).val() == 1)
		$('#manualanswer1').attr('checked','checked');
	else
		$('#manualanswer0').attr('checked','checked');
	$('#question').val($('#Q_' + cardid).html());
	$('#answer').val($('#A_' + cardid).html());
	$('#card-modal').modal("show");
}

function clearForm()
{
	$('#CardID').val(0);
	$('#question').val('');
	$('#answer').val('');
}
</script>
<div class='row'><div class='col-md-6'><strong><?php echo $CardSet['setname']; ?></strong></div><div class='col-md-3'><?php echo $CategoryStr; ?></div>
<?php
if($AllowEdit == 1)
{
	echo "<div class='col-md-3'>";
	echo "<div class='btn-toolbar pull-right'>\n";
	echo "<button class='btn btn-primary btn-sm' data-toggle='modal' data-target='#cardset-modal'><span class='fa fa-edit'></span> Edit Cardset</button>";
	echo "<button class='btn btn-success btn-sm'  onclick='editCard(0);'><span class='fa fa-plus'></span> Add Card</button>";
	echo "</div></div>";
}
?>
</div>
<?php
foreach($Cards AS $CardID => $CardRec)
{
	echo "<div class='row'><div class='col-md-12'>\n";
	echo "<div class='panel panel-default'>\n";
	echo "<div class='panel-heading'><div class='row'><div class='col-md-6'><strong>Question:</strong> <span id='Q_" . $CardID . "'>" . $CardRec['cardquestion'] . "</span></div>\n";
	$Manual = ($CardRec['manualanswer'] == 1) ? "<span class='fa fa-laptop text-primary fa-lg' title='Check Results automatically'></span>" : "<span class='fa fa-user text-primary fa-lg' title='Check Results manually'></span>";
	echo "<div class='col-md-2'>" . $Manual . "</div>";
	if($AllowEdit == 1)
	{
		echo "<div class='col-md-4'>";
		echo "<input type='hidden' name='manual_" . $CardID . "' id='manual_" . $CardID . "' value='" . $CardRec['manualanswer'] . "'>";
		echo "<div class='btn-toolbar pull-right'>\n";
		echo "<button type='button' onclick='deleteCard(" . $SetID . ", " . $CardID . ");' class='btn btn-danger btn-sm'><span class='fa fa-times-circle-o'></span> Delete</button>\n";
		echo "<button type='button' class='btn btn-primary btn-sm' onclick='editCard(" . $CardID. ");'><span class='fa fa-edit'></span> Edit</button>\n";
		if($CardRec['cardorder'] < $MaxOrder)
			echo "<a href='cardOrder.php?s=" . $SetID . "&c=" . $CardID . "&d=0' class='btn btn-info btn-sm' title='Move card down'><span class='fa fa-toggle-down fa-lg'></span></a>\n";
		else
			echo "<button class='btn btn-default' disabled='disabled'><span class='fa fa-toggle-down fa-lg'></span></button>";
		if($CardRec['cardorder'] != 0)
			echo "<a href='cardOrder.php?s=" . $SetID . "&c=" . $CardID . "&d=1' class='btn btn-info btn-sm' title='Move card up'><span class='fa fa-toggle-up fa-lg'></span></a>\n";
		else
			echo "<button class='btn btn-default' disabled='disabled'><span class='fa fa-toggle-up fa-lg'></span></button>";
		echo "</div></div>\n";
	}
	echo "</div></div>\n";
	echo "<div class='panel-body'><strong>Answer</strong> <span id='A_" . $CardID . "'>" . $CardRec['cardanswer'] . "</span></div>\n";
	echo "</div>\n";
	echo "</div></div>\n";
}
if($AllowEdit == 1)
{
	$DDArr = array();
foreach($CardCategories AS $CategoryID => $CategoryRec)
{
	$DDArr[$CategoryRec['parentcategoryid']][$CategoryID] = 1;
}
print_r($DDArr);
?>
<div class='modal fade' id='cardset-modal' tabindex='-1' role='dialog' aria-hidden='true'>
	<div class='modal-dialog modal-xs'>
		<div class='modal-content'>
			<div class='modal-header'><strong>Edit Cardset</strong><a href="#" class="close pull-right" data-dismiss="modal">&times;</a></div>
			<div class='modal-body'>
				<form method='POST' action='saveCardSet.php' id='cardsetfrm'>
					<input type='hidden' name='SetID' id='SetID' value='<?php echo $SetID; ?>'>
					<div class='row'><div class='col-md-4'>Card Set Name:</div><div class='col-md-8'><input type='text' maxlength='100' name='setname' id='setname' class='validate[required] form-control' value='<?php echo $CardSet['setname']; ?>'></div></div>
					<div class='row'>
						<div class='col-md-4'>Category:</div><div class='col-md-8'><select name='setcat' id='setcat' class='validate[required] form-control'>
						<option value=''>-- Select one --</option>
<?php
// foreach($CardCategories AS $CategoryID => $CategoryRec)
foreach($DDArr[0] AS $LoopID => $CategoryRec)
{
	echo "<option value='" . $LoopID . "'";
	if($CardSet['categoryid'] == $LoopID)
		echo " selected='selected'";
	echo ">" . $CardCategories[$LoopID]['categoryname'] . "</option>";
	foreach($DDArr[$LoopID] AS $SubID => $CategoryRec)
	{
		echo "<option value='" . $SubID . "'";
		if($CardSet['categoryid'] == $SubID)
			echo " selected='selected'";
		echo ">-- " . $CardCategories[$SubID]['categoryname'] . "</option>";
	}
}
?>
						</select></div></div>
					<div class='row'></div>
				</form>
			</div>
			<div class='modal-footer'><div class='btn-toolbar pull-right'>
				<button type='button' class='btn btn-warning' data-dismiss='modal'><span class='fa fa-times'></span> Cancel</button>
				<button class='btn btn-success' onclick='$("#cardsetfrm").submit();'><span class='fa fa-floppy-o'></span> Save</button>
			</div></div>
		</div>
	</div>
</div>
<div class='modal fade' id='card-modal' tabindex='-1' role='dialog' aria-hidden='true'>
	<div class='modal-dialog modal-xs'>
		<div class='modal-content'>
			<div class='modal-header'><strong>Edit Card</strong><a href="#" class="close pull-right" data-dismiss="modal">&times;</a></div>
			<div class='modal-body'>
				<form method='POST' action='saveCard.php' id='cardfrm'>
					<input type='hidden' name='SetID' id='SetID' value='<?php echo $SetID; ?>'>
					<input type='hidden' name='CardID' id='CardID' value=''>
					<div class='row'><div class='col-md-3'>Question:</div><div class='col-md-9'><input type='text' name='question' id='question' maxlength='255' class='validate[required] form-control'></div></div>
					<div class='row'><div class='col-md-3'>Answer:</div><div class='col-md-9'><input type='text' name='answer' id='answer' maxlength='100' class='validate[required] form-control'></div></div>
					<div class='row'><div class='col-md-3'>Provide Result:</div><div class='col-md-9'>
						<input type='radio' name='manualanswer' id='manualanswer0' value='0'><label for='manualanswer0'>Manually</label><br>
						<input type='radio' name='manualanswer' id='manualanswer1' value='1'><label for='manualanswer1'>Automatically</label>
					</div></div>
				</form>
			</div>
			<div class='modal-footer'><div class='btn-toolbar pull-right'>
				<button type='button' class='btn btn-warning' data-dismiss='modal'><span class='fa fa-times'></span> Cancel</button>
				<button  onclick='$("#cardfrm").submit();' class='btn btn-success'><span class='fa fa-floppy-o'></span> Save</button>
			</div></div>
		</div>
	</div>
</div>
<script>
$(document).ready(function()
{
	$("#cardsetfrm").validationEngine();
});

function deleteCard(SetID, CardID)
{
	if(confirm("Are you sure you want to delete this card?"))
	{
		window.location = 'deleteCard.php?s=' + SetID + '&c=' + CardID;
		return;
	}
}
</script>
<?php
}
include("footer.inc.php");
?>