<?php
include("db.inc.php");
include("header.inc.php");
if($ErrMsg != '')
	$ErrMsg = '<div class="row"><div class="col-md-12 bg-danger">' . $ErrMsg . '</div></div>';
?>
<div class="page-header"><strong>Add a student</strong></div>
<form method='POST' action='doAddStudent.php' id='registerFrm'>
	<?php echo $ErrMsg; ?>
	<div class="row"><div class="col-md-2">Firstname:</div><div class="col-md-4"><input type='text' name='fname' id='fname' maxlength='200' class="validate[required] form-control input-sm"></div></div>
	<div class="row"><div class="col-md-2">Surname:</div><div class="col-md-4"><input type='text' name='lname' id='lname' maxlength='200' class="validate[required] form-control input-sm"></div></div>
	<div class="row"><div class="col-md-2">Username:</div><div class="col-md-4"><input type='text' name='email' id='email' maxlength='200' class="validate[required] form-control input-sm"></div></div>
	<div class="row"><div class="col-md-2">Password:</div><div class="col-md-4"><input type='password' name='passwd' id='passwd' class="validate[required] form-control input-sm"></div></div>
	<div class="row"><div class="col-md-6"><button type='submit' class="btn btn-info pull-right"><span class='fa fa-sign-in'></span> Add Student</button></div></div>
</form>
<script>
$(document).ready(function()
{
	$("#registerFrm").validationEngine();
});
</script>
<?php
include("footer.inc.php");
?>