<?php 
include 'setting/system.php';
include 'theme/head.php';
// Start the session

// Redirect if already logged in
if (isset($_SESSION['USER_ID']) && isset($_SESSION['USER_NAME']) && isset($_SESSION['USER_EMAIL'])) {
  echo "<script>window.location.href = 'index.php?page=product';</script>";
  exit();
} elseif (isset($_SESSION['id']) && isset($_SESSION['name']) && isset($_SESSION['user'])) {
  echo "<script>window.location.href = 'dashboard.php';</script>";
  exit();
}
?>

<div class="container">
	<div class="row" style="margin-top: 10%">
		<h1 class="text-center"><?php echo NAME_X; ?></h1><br>
		<div class="col-md-2 col-md-offset-2">
			<img src="img/pig.png" class="img img-responsive">
		</div>
		<div class="col-md-4">
			<form method="post" autocomplete="off">
				<div class="form-group">
					<label class="control-label">Email</label>
					<input type="email" name="username" class="form-control input-sm" required>
				</div>
				<div class="form-group">
					<label class="control-label">Password</label>
					<div style="position: relative;">
						<input type="password" name="password" class="form-control input-sm" required>
						<i class="fa fa-eye" id="showPass" style="position: absolute; top: 7px; right:10px; cursor: pointer;"></i>
					</div>
				</div>
				<div style="display: flex; gap: 20px; justify-content: space-between; align-items: center;">
					<button name="submit" type="submit" class="btn btn-md btn-dark">Log in</button>
					<a href="forgot.php">Forgot Password</a>
				</div>
				<p style="margin-top: 20px;">Don't have an account? <a href="signup.php">Sign Up</a></p>
			</form>

			<?php
			if (isset($_POST['submit'])) {
				$username = htmlspecialchars(stripslashes(trim($_POST['username'])));
				$password = htmlspecialchars(stripslashes(trim($_POST['password'])));

				// Check admin table first
				$get_admin = $db->prepare("SELECT * FROM admin WHERE username = :uname");
				$get_admin->bindParam(':uname', $username, PDO::PARAM_STR);
				$get_admin->execute();

				if ($get_admin->rowCount() > 0) {
					$row = $get_admin->fetch(PDO::FETCH_OBJ);
					if (password_verify($password, $row->password)) {
						$_SESSION['id'] = $row->id;
						$_SESSION['name'] = $row->name;
						$_SESSION['user'] = $row->username;
						echo "<script>
							Swal.fire({ icon: 'success', title: 'Account signed in successfully', timer: 1500 }).then(() => {
								window.location.href = 'dashboard.php';
							});
						</script>";
					} else {
						echo "<script>Swal.fire({ icon: 'error', title: 'Incorrect password' });</script>";
					}
				} else {
					// Check users table
					$get_user = $db->prepare("SELECT * FROM users WHERE email = :email");
					$get_user->bindParam(':email', $username, PDO::PARAM_STR);
					$get_user->execute();

					if ($get_user->rowCount() > 0) {
						$row = $get_user->fetch(PDO::FETCH_OBJ);
						if (password_verify($password, $row->password)) {
							$_SESSION['USER_ID'] = $row->id;
							$_SESSION['USER_NAME'] = $row->name;
							$_SESSION['USER_EMAIL'] = $row->email;
							echo "<script>
								Swal.fire({ icon: 'success', title: 'Account signed in successfully', timer: 1500 }).then(() => {
									window.location.href = 'index.php?page=product';
								});
							</script>";
						} else {
							echo "<script>Swal.fire({ icon: 'error', title: 'Incorrect password' });</script>";
						}
					} else {
						echo "<script>Swal.fire({ icon: 'error', title: 'User not found' });</script>";
					}
				}
			}
			?>
		</div>
	</div>
</div>

<script>
  let password = document.querySelector("input[name='password']");
  let showPass = document.getElementById("showPass");

  showPass.onclick = () => {
    if (password.getAttribute("type") == 'password') {
      password.setAttribute("type", "text");
      showPass.classList.replace("fa-eye", "fa-eye-slash");
    } else {
      password.setAttribute("type", "password");
      showPass.classList.replace("fa-eye-slash", "fa-eye");
    }
  }
</script>

<?php include 'theme/foot.php'; ?>
