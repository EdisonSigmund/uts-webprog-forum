<?php
require_once("include/session_check.php");
require_once("include/config.php");

$username = $_POST['username'];
$email = $_POST['email'];
$fullname = $_POST['fullname'];

$password = $_POST['password'];

if (
	!isset($_POST['fullname']) || $_POST['fullname'] == "" ||
	!isset($_POST['username']) || $_POST['username'] == "" ||
	!isset($_POST['email']) || $_POST['email'] == ""
) {
	$_SESSION['error'] = "Please fill in all the fields.";
	header('location: account.php');
	return;
}

if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
  $filename = $_FILES['foto']['name'];
  $temp_file = $_FILES['foto']['tmp_name'];

  $file_ext = explode(".", $filename);
  $file_ext = end($file_ext);
  $file_ext = strtolower($file_ext);

  switch ($file_ext) {
    case 'jpg':
    case 'jpeg':
    case 'png':
    case 'svg':
    case 'webp':
    case 'bmp':
    case 'gif':
      $foto = "images/profile/" . time() . "_" . $filename;
      move_uploaded_file($temp_file, $foto);

      $sql = "UPDATE user
      SET foto = ?
      WHERE id = ?";

      $result = $db->prepare($sql);
      $result->execute([$foto, $_SESSION['user_id']]);
      break;
    default:
      $_SESSION['error'] = "Please upload a valid picture.";
      header('location: account.php');
      return;
  }
}

$query = "SELECT * FROM user WHERE id = ?";
$search = $db->prepare($query);
$search->execute([$_SESSION['user_id']]);
$original = $search->fetch(PDO::FETCH_ASSOC);

$query = "SELECT * FROM user WHERE username = ?";
$taken_username = $db->prepare($query);
$taken_username->execute([$username]);
$taken_username = $taken_username->fetch(PDO::FETCH_ASSOC);

$query = "SELECT * FROM user WHERE email = ?";
$taken_email = $db->prepare($query);
$taken_email->execute([$email]);
$taken_email = $taken_email->fetch(PDO::FETCH_ASSOC);

if ($taken_username && $username != $original['username']) {
  $_SESSION['error'] = "Username has been taken. Please use another username.";
  header('location: account.php');
  return;
} else if ($taken_email && $email != $original['email']) {
  $_SESSION['error'] = "Email has been taken. Please use another email.";
  header('location: account.php');
  return;
}

if ($username != $original['username']) {
  $query = "SELECT * FROM username_changes WHERE user_id = ?";
  $result = $db->prepare($query);
  $result->execute([$_SESSION['user_id']]);
  $changes = $result->rowCount();
  
  if ($changes >= 2) {
    $_SESSION['error'] = "You have reached the maximum number of username changes.";
    header('location: account.php');
    return;
  } else if ($changes >= 1) {
    header('location: account_username.php?username=' . $username);
    return;
  }
  
  $query = "INSERT INTO username_changes (user_id, username_old, username_new) VALUES (?, ?, ?)";
  $result = $db->prepare($query);
  $result->execute([$_SESSION['user_id'], $original['username'], $username]);
}

// if($_POST['password'] != $_POST['confirm_password']){
//   $_SESSION['error'] = "Password does not match.";
//   header('location: register.php');
//   return;
// }

if (isset($_POST['password']) && $_POST['password'] != "") {
  $en_pass = password_hash($password, PASSWORD_BCRYPT);

  $sql = "UPDATE user
  SET password = ?
  WHERE id = ?";

  $result = $db->prepare($sql);
  $result->execute([$en_pass, $_SESSION['user_id']]);
}


$sql = "UPDATE user
SET fullname = ?, username = ?, email = ?
WHERE id = ?";

$result = $db->prepare($sql);
$result->execute([$fullname, $username, $email, $_SESSION['user_id']]);

$_SESSION['success'] = "Successfully updated account data";
$_SESSION['username'] = $username;
header('location: account.php');
?>