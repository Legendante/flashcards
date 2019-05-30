<?php
include_once("db.inc.php");
include_once("header.inc.php");
include_once("flash.inc.php");
$SetID = (isset($_GET['s'])) ? pebkac($_GET['s'], 6) : 0;
$Offset = (isset($_GET['o'])) ? pebkac($_GET['o'], 6) : 0;
$CardCategories = getCardSetCategories();
$CardSet = getCardSetByID($SetID);
$Category = getCategoryDetails($CardSet['categoryid']);
$MaxOrder = getCardsMaxOrderFromCardSet($SetID);
if($Offset > $MaxOrder)
	$Offset = $MaxOrder;
$Card = getStudyCard($SetID, $Offset);
?>
<script>

</script>
<div class='row'>
	<div class='col-md-3'>
	
	</div>
	<div class='col-md-9'>
		<div class='row'>
			<div class='col-md-8'><?php echo $Category['categoryname'] . " - <strong>" . $CardSet['setname'] . "</strong>"; ?></div>
		</div>
	<div class='row'><div class='col-md-12'>
	<div class='panel panel-default'>
	<div class='panel-heading'><strong>Question:</strong> <?php echo $Card['cardquestion']; ?></div>
	<div class='panel-body'>
		<div id='answerbox'><strong>Answer</strong> <?php echo $Card['cardanswer']; ?>
		<div class='btn-group pull-right'>
<?php
	if($Card['cardorder'] > 0)
		echo "<a href='learn.php?s=" . $SetID . "&o=" . ($Offset - 1) . "' class='btn btn-warning'><span class='fa fa-toggle-left fa-lg'></span> Previous card</a>";
	else
		echo "<button disabled='disabled' class='btn btn-danger'><span class='fa fa-stop fa-lg'></span></button>";
	if($MaxOrder > $Card['cardorder'])
		echo "<a href='learn.php?s=" . $SetID . "&o=" . ($Offset + 1) . "' class='btn btn-primary'>Next card <span class='fa fa-toggle-right fa-lg'></span></a>";
	else
		echo "<button disabled='disabled' class='btn btn-danger'><span class='fa fa-stop fa-lg'></span></button>";
?>
		</div>
		</div>
	</div>
	</div>
	</div></div>
	</div>
</div>
<?php
include("footer.inc.php");
?>