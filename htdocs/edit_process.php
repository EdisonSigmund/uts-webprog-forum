<?php
require_once("include/config.php");
require_once("include/session_check.php");
$id = $_GET["id"];

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

if (
  !isset($_POST['title']) || $_POST['title'] == "" ||
	!isset($_POST['content']) || $_POST['content'] == ""
) {
	$_SESSION['error'] = "Please fill in the title & content.";
	header('location: edit.php?id=' . $id);
	return;
} else {
  // Data from Form
  $title = $_POST['title'];
  $content = $_POST['content'];

  $sharing = $_POST['sharing'];
  if (!isset($sharing) || $sharing == "") {
    $is_shared = false;
  } else {
    // echo "File is shared" . "<br />";
    $is_shared = true;
  }

  if (!isset($_POST['is_public'])) {
    $is_public = false;
    $token = null;
  } else {
    // echo "File is public" . "<br />";
    $is_public = true;
    $token = bin2hex(random_bytes(20));
  }

  if (!isset($_POST['attachment'])) {
    $sql = "SELECT * FROM attachments WHERE note_id = ?";
    $result = $db->prepare($sql);
    $result->execute([$id]);

    $attachments = $result->fetchAll(PDO::FETCH_ASSOC);
    if (count($attachments) > 0) {
      $sql = "DELETE FROM attachments WHERE note_id = ?";
      $result = $db->prepare($sql);
      $result->execute([$id]);
    }
  }

  // if (!isset($_POST['sharing'])) {
    $sql = "SELECT * FROM shared_notes WHERE note_id = ?";
    $result = $db->prepare($sql);
    $result->execute([$id]);

    $shared_to = $result->fetchAll(PDO::FETCH_ASSOC);
    if (count($shared_to) > 0) {
      $sql = "DELETE FROM shared_notes WHERE note_id = ?";
      $result = $db->prepare($sql);
      $result->execute([$id]);
    }
  // }
  
  if (isset($_FILES['attachment'])) {
    $attachment = $_FILES['attachment'];
    $fileCount = count($attachment["name"]);

    for ($i = 0; $i < $fileCount; $i++) {
      $filename = $attachment["name"][$i];
      // echo "File name: " . $filename . "<br />";
      // echo "File type: " . $attachment["type"][$i] . "<br />";
      // echo "Size: " . $attachment["size"][$i] . "<br />";
      // echo "<br />";

      if ($attachment["size"][$i] > 0) {
        $temp_file = $attachment['tmp_name'][$i];
        $file = "attachments/" . time() . "_" . $filename;
        move_uploaded_file($temp_file, $file);

        $sql = "INSERT INTO attachments (file, note_id)
        VALUES(?, ?)";

        $result = $db->prepare($sql);
        $result->execute([$file, $id]);
      }
    }
  }

  if ($is_shared) {
    $shared_to = explode(",", $sharing);
    for ($i = 0; $i < count($shared_to); $i++) {
      $shared_to[$i] = trim($shared_to[$i]);

      $sql = "SELECT * FROM user WHERE username = ?";

      $result = $db->prepare($sql);
      $result->execute([$shared_to[$i]]);
      $row = $result->fetch(PDO::FETCH_ASSOC);

      if(!$row) {
        $_SESSION['error'] = "Failed to share note. User {$shared_to[$i]} not found.";
        header('location: edit.php?id=' . $id);
        return;
      } else {
        $shared_to_id = $row['id'];

        $sql = "INSERT INTO shared_notes (shared_to, note_id)
        VALUES(?, ?)";

        $result = $db->prepare($sql);
        $result->execute([$shared_to_id, $id]);
      }
    }
  }
  
	$sql = "UPDATE notes SET title=?, content=?, owner=?, is_public=?, is_shared=?, token=?
  WHERE id = ?";

	$result = $db->prepare($sql);
	$result->execute([$title, $content, $_SESSION['user_id'], $is_public, $is_shared, $token, $id]);
  $_SESSION['success'] = "Successfully edited message";
	header('location: index.php');
}