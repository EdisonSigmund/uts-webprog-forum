<?php
require_once("include/config.php");
require_once("include/session_check.php");
$id = $_GET['id'];

$sql = "SELECT * FROM notes WHERE id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$result) {
  header('location: index.php');
  return;
}

if ($result['owner'] != $_SESSION['user_id']) {
  header("Location: index.php");
  return;
}

$sql = "DELETE FROM notes WHERE id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$id]);

$sql = "DELETE FROM attachments WHERE note_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$id]);

$sql = "DELETE FROM shared_notes WHERE note_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$id]);

$_SESSION['success'] = "Successfully deleted note";
header('location: index.php');
?>