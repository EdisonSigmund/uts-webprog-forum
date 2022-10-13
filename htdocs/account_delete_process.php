<?php
require_once("include/config.php");
require_once("include/session_check.php");

if (!isset($_POST['username']) || $_POST['username'] == "") {
  $_SESSION['error'] = "Please type in your username to confirm";
  header('location: account_delete.php');
  return;
}

if (isset($_POST['delete_notes']) && $_POST['delete_notes'] != "") {
  $delete = true;
} else {
  $delete = false;
}

$uid = $_SESSION['user_id'];

if ($delete) {
  $sql = "SELECT * FROM notes WHERE owner = ?";
  $stmt = $db->prepare($sql);
  $stmt->execute([$uid]);
  
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $query = "DELETE FROM attachments WHERE note_id = ?;
            DELETE FROM shared_notes WHERE note_id = ?;
            DELETE FROM notes WHERE id = ?";
    $delete = $db->prepare($query);
    $delete->execute([$row['id'], $row['id'], $row['id']]);
  }
} else {
  $sql = "SELECT * FROM notes WHERE owner = ? AND is_public = false AND is_shared = false";
  $stmt = $db->prepare($sql);
  $stmt->execute([$uid]);

  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $query = "DELETE FROM attachments WHERE note_id = ?;
            DELETE FROM notes WHERE id = ?";
    $delete = $db->prepare($query);
    $delete->execute([$row['id'], $row['id']]);
  }
}

$sql = "DELETE FROM username_changes WHERE user_id = ?;
        DELETE FROM user WHERE id = ?;";  
$stmt = $db->prepare($sql);
$stmt->execute([$uid, $uid]);

header('location: logout.php');
?>