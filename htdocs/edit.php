<?php 
require_once("include/config.php");
require_once("include/session_check.php");
$id = $_GET["id"];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Notes-IF330</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="vendors/feather/feather.css">
  <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <link rel="stylesheet" href="vendors/datatables.net-bs4/dataTables.bootstrap4.css">
  <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" type="text/css" href="js/select.dataTables.min.css">
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="css/vertical-layout-light/style.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="images/favicon.png" />
</head>

<body>
  <div class="container-scroller">
    <?php require_once("include/navbar.php"); ?>
    <div class="container-fluid page-body-wrapper">
      <?php require_once("include/sidebar.php"); ?>

      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-12 grid-margin">
              <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                  <h2 class="font-weight-bold">Edit Note</h2>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6 stretch-card grid-margin">
              <div class=" card">
                <div class="card-body">
                  <?php require_once("include/alert.php"); ?>
                  <?php
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
                    
                    $title = $result['title'];
                    $content = $result['content'];
                    $is_public = $result['is_public'];

                    $sql = "SELECT * FROM attachments WHERE note_id = ?";
                    $result = $db->prepare($sql);
                    $result->execute([$id]);

                    $attachments = $result->fetchAll(PDO::FETCH_ASSOC);
                    if (count($attachments) > 0) {
                      $attachment = true;
                    } else {
                      $attachment = false;
                    }

                    $sql = "SELECT * FROM shared_notes JOIN user ON shared_notes.shared_to = user.id WHERE note_id = ?";
                    $result = $db->prepare($sql);
                    $result->execute([$id]);

                    $shared_to = [];
                    while($share = $result->fetch(PDO::FETCH_ASSOC)) {
                      array_push($shared_to, $share);
                    }
                    if (count($shared_to) > 0) {
                      $sharing = true;
                    } else {
                      $sharing = false;
                    }
                  ?>
                  <form action="edit_process.php?id=<?= $id ?>" method="post" enctype="multipart/form-data">
                    <div class=" form-group">
                      <label>Title</label>
                      <input type="text" class="form-control" name="title" value="<?= $title ?>" placeholder="Title">
                    </div>
                    <div class="form-group">
                      <label>Content</label>
                      <textarea class="form-control" name="content" rows="8"><?= $content ?></textarea>
                    </div>
                    <div class="form-group">
                      <label>Attachment(s)</label><br />
                      <input type="file" name="attachment[]" multiple>
                    </div>
                    <div class="form-group">
                      <label>Share this note with: </label>
                      <input type="text" class="form-control" name="sharing" placeholder="Seperate usernames with ,"
                        value="<?php
                          if ($sharing) {
                            for ($i = 0; $i < count($shared_to); $i++) {
                              echo $shared_to[$i]['username'] . ". ";
                            }
                          }
                      ?>">
                    </div>
                    <div class="form-check">
                      <label class="form-check-label text-muted">
                        <input type="checkbox" class="form-check-input" name="is_public" value="public"
                          <?= $is_public ? "checked" : ""; ?>>
                        Set as public note
                      </label>
                    </div>
                    <?php
                    if($attachment){
                    ?>
                    <div class="form-check">
                      <label class="form-check-label text-muted">
                        <input type="checkbox" class="form-check-input" name="attachment" value="attachment"
                          <?= $attachment ? "checked" : ""; ?>>
                        Keep attachments
                      </label>
                    </div>
                    <?php
                    }
                    ?>
                    <!-- <div class="form-group">
                      <label for="color">Color</label>
                      <input type="color" class="form-control" id="color" name="color" placeholder="Color">
                    </div> -->
                    <div align="right">
                      <button type="submit" class="btn btn-primary mr-2">Save</button>
                      <button type="submit" class="btn btn-danger mr-2"
                        formaction="note_delete.php?id=<?= $id ?>">Delete</button>
                      <a class="btn btn-light" href="index.php">Cancel</a>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
        <?php require_once("include/footer.php"); ?>
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

  <!-- plugins:js -->
  <script src="vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <script src="vendors/chart.js/Chart.min.js"></script>
  <script src="vendors/datatables.net/jquery.dataTables.js"></script>
  <script src="vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>
  <script src="js/dataTables.select.min.js"></script>

  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="js/off-canvas.js"></script>
  <script src="js/hoverable-collapse.js"></script>
  <script src="js/template.js"></script>
  <script src="js/settings.js"></script>
  <script src="js/todolist.js"></script>
  <!-- endinject -->
  <!-- Custom js for this page-->
  <script src="js/dashboard.js"></script>
  <script src="js/Chart.roundedBarCharts.js"></script>
  <!-- End custom js for this page-->
</body>

</html>