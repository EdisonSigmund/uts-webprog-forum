<?php
if(session_id() == '') {
    session_start();
}
require_once('include/config.php');

if (
	!isset($_POST['login']) || $_POST['login'] == "" ||
	!isset($_POST['password']) || $_POST['password'] == ""
){
	$_SESSION['error'] = "Please fill in all the fields.";
	header('location: login.php');
	return;
}

$login = $_POST['login'];
$password = $_POST['password'];

$sql = "SELECT * FROM user
		WHERE username = ?";
$username = $db->prepare($sql);
$username->execute([$login]);
$match_username = $username->fetch(PDO::FETCH_ASSOC);

$sql = "SELECT * FROM user
		WHERE email = ?";
$email = $db->prepare($sql);
$email->execute([$login]);
$match_email = $email->fetch(PDO::FETCH_ASSOC);

if(!$match_username && !$match_email) {
	$_SESSION['error'] = "Username/email not found.";
	header('location: login.php');
	return;
} else {
	if ($match_username) {
		$row = $match_username;
	} else if ($match_email) {
		$row = $match_email;
	}

	if(!password_verify($password, $row['password'])){
		$_SESSION['error'] = "Wrong password. Please try again.";
		header('location: login.php');
		return;
	} else {
		$_SESSION['user_id'] = $row['id'];
		$_SESSION['username'] = $row['username'];
		header('location: index.php');
	}
}