<?php 
require_once("include/config.php");
require_once("include/session_check.php");
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
      
      if (!isset($_GET["id"]) || $_GET["id"] == "") {
        $id = "";
      } else {
        $id = $_GET["id"];
      }

      $sql = "SELECT * FROM user WHERE id = ?";
      $stmt = $db->prepare($sql);
      $stmt->execute([$id]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if (!$user) {
        header('location: index.php');
        return;
      }

      if (!$user['foto']) {
        $image = "images/default-profile.jpg";
      } else {
        $image = $user['foto'];
      }
      ?>

      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-12 grid-margin">
              <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                  <div class="row">
                    <div class="col-md-2 stretch-card" style="margin-bottom:20px">
                      <img class="profile-pic" src="<?= $image ?>" alt="<?= $user['username'] ?>" width="100px"
                        height="100px">
                    </div>
                    <div class="col-md-10">
                      <h2 class="font-weight-bold"><?= $user['fullname'] ?></h2>
                      <h3 class="font-weight-bold"><?= $user['username'] ?></h3>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <h3 class="font-weight-bold">Public & Shared Notes</h3>
          <hr />
          <?php
          $link = 'http://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
          $url = explode('&', $link);
          $url = $url[0];

          if (!isset($_GET["page"]) || $_GET["page"] == "") {
            $page = 1;
          } else {
            $page = $_GET["page"];
          }
          
          $per_page = 6;
          $result = [];

          $sql = "SELECT * FROM notes
              WHERE is_public = ? AND owner = ?";
          $stmt = $db->prepare($sql);
          $stmt->execute([true, $id]);
          while($notes = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($result, $notes);
          }

          $sql = "SELECT * FROM shared_notes
                  JOIN notes ON shared_notes.note_id = notes.id
              WHERE shared_notes.shared_to = ? AND notes.owner = ?";
          $stmt = $db->prepare($sql);
          $stmt->execute([$_SESSION['user_id'], $id]);
          while($shared_notes = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($result, $shared_notes);
          }

          $total_page = ceil(count($result)/$per_page);

          if(count($result) == 0) {
            echo "This user has no public notes/notes shared with you";
            return;
          } else {
              $index = ($page-1) * $per_page;
              for ($i = $index; $i < $index + $per_page; $i++) {
                if ($i < count($result)) {
                  $sql = "SELECT * FROM attachments WHERE note_id = ?";
                  $att = $db->prepare($sql);
                  $att->execute([$result[$i]['id']]);

                  $attachments = $att->fetchAll(PDO::FETCH_ASSOC);
                  if (count($attachments) > 0) {
                    $attachment = true;
                  } else {
                    $attachment = false;
                  }
                  
                if ($result[$i]['is_public']) {
                  $icon = "icon-globe";
                  $type = "Public";
                } else if ($result[$i]['is_shared']) {
                  $icon = "icon-share";
                  $type = "Shared";
                } else {
                  $icon = "icon-lock";
                  $type = "Private";
                }
            if ($i % 2 == 0) {
              echo '<div class="row">';
            }
            ?>
          <!-- <div class="col-md-12 grid-margin stretch-card"> -->
          <div class="col-md-6 stretch-card" style="margin-bottom:20px">
            <div class="card">
              <div class="card-body">
                <?php require_once("include/alert.php"); ?>
                <div>
                  <a class="<?= $icon ?> menu-icon card-title"
                    href="view.php?id=<?= $result[$i]['id'] ?><?= $result[$i]['is_public'] ? "&token=" . $result[$i]['token'] : "" ?>"
                    title="Click to view note">
                    <span class="font-weight-500"><?= $result[$i]['title'] ?></span>
                  </a>
                </div>
                <p class="card-description"><?= $result[$i]['date'] ?></p>
                <p class="font-weight-500">
                <pre><?= substr(htmlspecialchars($result[$i]['content']), 0, 256); if(strlen($result[$i]['content']) > 256) echo "..." ?></pre>
                </p>
                <?php if($attachment) { ?>
                <p class="card-description"><?= count($attachments) ?> Attachment(s)</p>
                <?php } ?>
              </div>
            </div>
          </div>
          <?php
            if ($i % 2 == 1 || $i == count($result) - 1) {
              echo '</div>';
            }
          }
        }
        }

        if(count($result) > $per_page) {
          ?>
          <hr />
          <div class="btn-toolbar" role="toolbar">
            <div>
              <ul class="pagination">
                <li class="page-item">
                  <a class="page-link" href="<?= $url ?>&page=1" aria-label="Previous">
                    <span aria-hidden="true">
                      << </span>
                        <span class="sr-only">First</span>
                  </a>
                </li>
                <li class="page-item">
                  <a class="page-link" href="<?= $url ?>&page=<?= $page-1 < 1 ? $page : $page-1 ?>"
                    aria-label="Previous">
                    <span aria-hidden="true">
                      < </span>
                        <span class="sr-only">Previous</span>
                  </a>
                </li>
                <?php
              $j = 0;
               for ($i = 0; $i < count($result); $i+=$per_page) {
              ?>
                <li class="page-item <?= ++$j == $page ? 'active' : '' ?>"><a class="page-link"
                    href="<?= $url ?>&page=<?= $j ?>"><?= $j ?></a>
                </li>
                <?php
               }
              ?>
                <li class="page-item">
                  <a class="page-link" href="<?= $url ?>&page=<?= $page+1 > $total_page ? $page : $page+1 ?>"
                    aria-label="Next">
                    <span aria-hidden="true">></span>
                    <span class="sr-only">Next</span>
                  </a>
                </li>
                <li class="page-item">
                  <a class="page-link" href="#<?= $url ?>&page=<?= $total_page ?>" aria-label="Next">
                    <span aria-hidden="true">>></span>
                    <span class="sr-only">Last</span>
                  </a>
                </li>
              </ul>
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