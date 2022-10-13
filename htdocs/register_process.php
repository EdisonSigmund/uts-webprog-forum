<?php
if(session_id() == '') {
    session_start();
}
require_once('include/config.php');

// Data from Form
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$fullname = $_POST['fullname'];
$role = $_POST['role'];
if (
	!isset($_POST['fullname']) || $_POST['fullname'] == "" ||
	!isset($_POST['username']) || $_POST['username'] == "" ||
	!isset($_POST['email']) || $_POST['email'] == "" ||
	!isset($_POST['password']) || $_POST['password'] == "" ||
	!isset($_POST['confirm_password']) || $_POST['confirm_password'] == ""
) {
	$_SESSION['error'] = "Please fill in all the fields.";
	header('location: register.php');
	return;
} else {
	$query = "SELECT * FROM user WHERE username = ?";
	$taken_username = $db->prepare($query);
	$taken_username->execute([$username]);
	$taken_username = $taken_username->fetch(PDO::FETCH_ASSOC);

	$query = "SELECT * FROM user WHERE email = ?";
	$taken_email = $db->prepare($query);
	$taken_email->execute([$email]);
	$taken_email = $taken_email->fetch(PDO::FETCH_ASSOC);

	if ($taken_username) {
		$_SESSION['error'] = "Username has been taken. Please use another username.";
		header('location: register.php');
		return;
	} else if ($taken_email) {
		$_SESSION['error'] = "Email has been taken. Please use another email.";
		header('location: register.php');
		return;
	}


	if($_POST['password'] != $_POST['confirm_password']){
		$_SESSION['error'] = "Password does not match.";
		header('location: register.php');
		return;
	}

	$en_pass = password_hash($password, PASSWORD_BCRYPT);

	$sql = "INSERT INTO user (username, email, password, fullname, role)
			VALUES(?, ?, ?, ?, ?)";

	$result = $db->prepare($sql);
	$result->execute([$username, $email, $en_pass, $fullname, $role]);

	$_SESSION['success'] = "Successfully created account";
	header('location: login.php');
}