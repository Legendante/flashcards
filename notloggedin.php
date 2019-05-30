<?php
?>
<!-- <div class='row'><strong>Flash Card Magic</strong></div> -->
<div class='row'>
	<div class='col-md-7'>
		<div class='row'>
			<div class='col-md-12'>
			<h4>To login as a student:</h4>
			<strong>Usercode: </strong> 1<br>
			<strong>Username: </strong> test<br>
			<strong>Password: </strong> test<br>
			<h4>To login as an admin:</h4>
			<strong>Username: </strong> test<br>
			<strong>Password: </strong> test<br>
			</div>
		</div>
	</div>
	<div class='col-md-5'>
		<div class='row'><div class='col-md-12'><a class='btn btn-info btn-block' href='register.php'><span class='fa fa-sign-in'></span> Register</a></div></div>
		<div class='row'>
			<div class='col-md-12'>
				<form method='POST' action='studentlogin.php' id='studentForm'>
				<div class="row"><div class="col-md-12"><strong>Student Login</strong></div></div>
				<?php echo $ErrMsg2; ?>
				<div class="row"><div class="col-md-4">User code</div><div class="col-md-8"><input type='text' name='usercode' id='usercode' class='validate[required] form-control'></div></div>
				<div class="row"><div class="col-md-4">Username</div><div class="col-md-8"><input type='text' name='username' id='username' class='validate[required] form-control'></div></div>
				<div class="row"><div class="col-md-4">Password</div><div class="col-md-8"><input type='password' name='userpass' id='userpass' class='validate[required] form-control'></div></div>
				<div class="row"><div class="col-md-12"><button class='btn btn-success push-right'><span class='fa fa-sign-in'></span> Login</button></div></div>
				</form>
			</div>
		</div>
		<div class='row'>
			<div class='col-md-12'>
				<form method='POST' action='login.php' id='loginForm'>
				<div class="row"><div class="col-md-12"><strong>Login</strong></div></div>
				<?php echo $ErrMsg; ?>
				<div class="row"><div class="col-md-4">Username</div><div class="col-md-8"><input type='text' name='username' id='username' class='validate[required] form-control'></div></div>
				<div class="row"><div class="col-md-4">Password</div><div class="col-md-8"><input type='password' name='userpass' id='userpass' class='validate[required] form-control'></div></div>
				<div class="row"><div class="col-md-12"><button class='btn btn-success push-right'><span class='fa fa-sign-in'></span> Login</button></div></div>
				</form>
			</div>
		</div>
		<!-- <div class='row'>
			<div class='col-md-6'>Right column 5</div>
			<div class='col-md-6'>Right column 6</div>
		</div> -->
	</div>
</div>