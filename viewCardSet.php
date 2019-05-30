<?php
include_once("db.inc.php");
include_once("header.inc.php");
include_once("flash.inc.php");
$SetID = (isset($_GET['s'])) ? pebkac($_GET['s'], 6) : 0;
$CardCategories = getCardSetCategories();
$CardSet = getCardSetByID($SetID);
$Cards = getCardsFromCardSet($SetID);
$AllowEdit = 0;
if($_SESSION['userid'] == $_SESSION['parentuserid'])
	$AllowEdit = 1;
?>
<div class='row'>
	<div class='col-md-3'>
	
	</div>
	<div class='col-md-9'>
		<div class='row'>
			<div class='col-md-8'><?php echo $CardCategories[$CardSet['categoryid']] . " - <strong>" . $CardSet['setname'] . "</strong>"; ?></div>
<?php
if($AllowEdit == 1)
	echo "<div class='col-md-4'><a href='SetSubscription.php?s=" . $SetID . "' class='btn btn-success pull-right'><span class='fa fa-plus'></span> Add to my collection</a></div>";
echo "</div>";
foreach($Cards AS $CardID => $CardRec)
{
	echo "<div class='row'><div class='col-md-12'>";
	echo "<div class='panel panel-default'>";
	echo "<div class='panel-heading'><strong>Question:</strong> <span id='Q_" . $CardID . "'>" . $CardRec['cardquestion'] . "</span></div>";
	echo "<div class='panel-body'><strong>Answer</strong> <span id='A_" . $CardID . "'>" . $CardRec['cardanswer'] . "</span></div>";
	echo "</div>";
	echo "</div></div>";
}
?>
	</div>
</div>
<?php
include("footer.inc.php");
?>