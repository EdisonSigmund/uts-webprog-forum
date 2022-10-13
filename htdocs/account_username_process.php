<?php
  require_once("include/session_check.php");
  require_once("include/config.php");

  $username = $_GET['username'];
  
  $query = "SELECT * FROM user WHERE id = ?";
  $search = $db->prepare($query);
  $search->execute([$_SESSION['user_id']]);
  $original = $search->fetch(PDO::FETCH_ASSOC);

  $query = "INSERT INTO username_changes (user_id, username_old, username_new) VALUES (?, ?, ?)";
  $result = $db->prepare($query);
  $result->execute([$_SESSION['user_id'], $original['username'], $username]);
  
  $sql = "UPDATE user
  SET username = ?
  WHERE id = ?";

  $result = $db->prepare($sql);
  $result->execute([$username, $_SESSION['user_id']]);

  $_SESSION['success'] = "Successfully updated account data";
  $_SESSION['username'] = $username;
  header('location: account.php');
?>