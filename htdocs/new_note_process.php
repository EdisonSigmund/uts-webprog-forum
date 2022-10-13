<?php 
require_once("include/config.php");
require_once("include/session_check.php");

if (
  !isset($_POST['title']) || $_POST['title'] == "" ||
	!isset($_POST['content']) || $_POST['content'] == ""
) {
	$_SESSION['error'] = "Please fill in the title & content.";
	header('location: new_note.php?title=' . $title . '&content=' . $content . '&is_public=' . $is_public);
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

  $is_public = true;
  $token = bin2hex(random_bytes(20));
  /* if (!isset($_POST['is_public'])) {
    $is_public = false;
    $token = null;
  } else {
    // echo "File is public" . "<br />";
    
  } */

  if ($is_shared) {
    $shared_to = explode(",", $sharing);
    for ($i = 0; $i < count($shared_to); $i++) {
      $shared_to[$i] = trim($shared_to[$i]);

      $sql = "SELECT * FROM user WHERE username = ?";

      $result = $db->prepare($sql);
      $result->execute([$shared_to[$i]]);
      $share = $result->fetch(PDO::FETCH_ASSOC);

      if(!$share) {
        $_SESSION['error'] = "Failed to create note. User {$shared_to[$i]} not found.";
        header('location: new_note.php?title=' . $title . '&content=' . $content . '&is_public=' . $is_public);
        return;
      } else {
        $is_shared_success = true;
      }
    }
  }

  $sql = "INSERT INTO notes (title, content, owner, is_public, is_shared, token) VALUES(?, ?, ?, ?, ?, ?)";


	$result = $db->prepare($sql);
	$result->execute([$title, $content, $_SESSION['user_id'], $is_public, $is_shared, $token]);

  $sql = "SELECT * FROM notes ORDER BY id DESC LIMIT 1";
  $result = $db->prepare($sql);
  $result->execute([]);
  $row = $result->fetch(PDO::FETCH_ASSOC);
  $note_id = $row['id'];

  if ($is_shared_success) {
    $shared_to_id = $share['id'];
    $sql = "INSERT INTO shared_notes (shared_to, note_id)
    VALUES(?, ?)";

    $result = $db->prepare($sql);
    $result->execute([$shared_to_id, $note_id]);
  }

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
        $result->execute([$file, $note_id]);
      }
    }
  }

  if ($is_public) {
    $new_type = "public";
  } else if ($is_shared) {
    $new_type = "shared";
  } else {
    $new_type = "private";
  }
  
  $_SESSION['success'] = "Successfully created $new_type note";
	header('location: index.php');
}
