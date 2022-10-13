<?php 
require_once("include/config.php");
// require_once("include/session_check.php");
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
      <?php require_once("include/sidebar.php");
          if (!isset($_GET['id']) || $_GET['id'] == "") {
            header("location: index.php");
          } else {
            $id = $_GET['id'];
          }
          
          if (!isset($_GET["token"]) || $_GET["token"] == "") {
            $token = null;
          } else {
            $token = $_GET["token"];
          } 

          $sql = "SELECT * FROM notes WHERE id = ?";
          $stmt = $db->prepare($sql);
          $stmt->execute([$id]);
          $result = $stmt->fetch(PDO::FETCH_ASSOC);

          if (!$result) {
            header('location: index.php');
            return;
          }

          if ($result['is_public']) {
              $sql = "SELECT * FROM notes WHERE id = ?";
              $stmt = $db->prepare($sql);
              $stmt->execute([$id]);
              $result = $stmt->fetch(PDO::FETCH_ASSOC);
              if ($result['token'] == $token) {
                // echo "Token is valid" . "<br />";
              } else {
                header("Location: index.php");
                return;
              }
          } else if ($result['owner'] != $_SESSION['user_id']) {
              if($result['is_shared']) {
              $sql = "SELECT * FROM shared_notes WHERE note_id = ? AND shared_to = ?";
              $stmt = $db->prepare($sql);
              $stmt->execute([$id, $_SESSION['user_id']]);
              $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
              if ($result) {
                // echo "Shared notes found" . "<br />";
              } else {
                header("Location: index.php");
                return;
              }
            }
          }
      
          $sql = "SELECT * FROM notes
              WHERE id = ?";
          $stmt = $db->prepare($sql);
          $stmt->execute([$id]);
          $result = $stmt->fetch(PDO::FETCH_ASSOC);

          if (!$result) {
            header("location: index.php");
            return;
          }
          $query = "SELECT * FROM user
              WHERE id = ?";
          $stmt = $db->prepare($query);
          $stmt->execute([$result['owner']]);
          $user = $stmt->fetch(PDO::FETCH_ASSOC);

          if (!$user) {
            $username = "anonymous";
            $uid = "";
          } else {
            $username = $user['username'];
            $uid = $user['id'];
          }

          $query = "SELECT * FROM attachments
              WHERE note_id = ?";
          $stmt = $db->prepare($query);
          $stmt->execute([$id]);
          $files = [];
          while ($attachment = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($files ,$attachment['file']);
          }

          $query = "SELECT * FROM shared_notes
              WHERE note_id = ?";
          $stmt = $db->prepare($query);
          $stmt->execute([$id]);
          $shared_to = [];
          while ($sharing = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $select = "SELECT * FROM user
              WHERE id = ?";
            $search = $db->prepare($select);
            $search->execute([$sharing['shared_to']]);
            $user = $search->fetch(PDO::FETCH_ASSOC);
            
            array_push($shared_to ,$user);
          }

          if ($result['owner'] == $_SESSION['user_id']) {
            $is_owned = true;
          } else {
            $is_owned = false;
          }
      ?>

      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-12 grid-margin">
              <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                  <h2 class="font-weight-bold"><?= $result['title'] ?></h2>
                  <a href="user.php?id=<?= $uid ?>">
                    <h3 class="font-weight-normal mb-0">by <?= $username ?></h3>
                  </a>
                </div>
              </div>
            </div>
          </div>
          <?php
          if (!$result) {
            header('location: index.php');
          } else {
              if ($result['is_public']) {
                $icon = "icon-globe";
                $type = "Public";
              } else if ($result['is_shared']) {
                $icon = "icon-share";
                $type = "Shared";
              } else {
                $icon = "icon-lock";
                $type = "Private";
              }
            ?>

          <div class="row">
            <!-- <div class="col-md-12 grid-margin stretch-card"> -->
            <div class="col-md-12 stretch-card" style="margin-bottom:20px">
              <div class="card">
                <div class="card-body">
                  <div>
                    <i class="<?= $icon ?> menu-icon card-description"></i><span class="card-description">
                      <?= $type ?>
                      Note</span>
                  </div>
                  <p class="card-description"><?= $result['date'] ?></p>
                  <p class="font-weight-500">
                  <pre><?= htmlspecialchars($result['content']) ?></pre>
                  </p>
                  <hr />
                  <p>
                    <?php
                    if (count($files) > 1) {
                      echo "Attachments: ";
                    } else if (count($files) == 1) {
                      echo "Attachment: ";
                    }
                    for ($i = 0; $i < count($files); $i++) {
                  ?>
                    <a href="<?= $files[$i] ?>">Attachment <?= $i+1 ?></a>.
                    <?php
                    }
                  ?>
                  </p>
                  <p>
                    <?php
                      if (count($shared_to) > 0) {
                        echo "Shared to: ";
                      }
                      for ($i = 0; $i < count($shared_to); $i++) {
                    ?>
                    <a href="user.php?id=<?= $shared_to[$i]['id'] ?>"><?= $shared_to[$i]['username'] ?></a>.
                    <?php
                      }
                    ?>
                  </p>
                  <p>
                    <?php
                      if ($result['is_public']) {
                        echo "Public Link : ";
                    ?>
                    <a href=""><?= 'http://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ?></a>
                    <?php
                      }
                    ?>
                  </p>
                  <?php
                  if ($is_owned) {
                  ?>
                  <div align="left">
                    <a href="edit.php?id=<?= $result['id'] ?>">
                      <button class="btn btn-primary" style="margin-bottom:20px">Edit Note</button>
                    </a>
                  </div>
                  <?php
                  }
                  ?>
                </div>
              </div>
            </div>
          </div>
          <?php
        }
          ?>
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