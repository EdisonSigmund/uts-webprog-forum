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
      <?php require_once("include/sidebar.php"); ?>

      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-12 grid-margin">
              <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                  <h2 class="font-weight-bold">My Account</h2>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-3"></div>

            <div class="col-md-5 stretch-card grid-margin">
              <div class=" card">
                <div class="card-body">
                  <?php require_once("include/alert.php"); ?>
                  <h4 class="font-weight-bold">Do you want to delete your account?</h4>
                  <?php
                    $sql = "SELECT * FROM user
                        WHERE id = ?";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([$_SESSION['user_id']]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                  ?>
                  <form action="account_delete_process.php" method="post">
                    <div class="form-group">
                      <label>Type your username to confirm</label>
                      <div>
                        <input type="text" class="form-control form-control-lg" name="username"
                          placeholder="<?= $result['username'] ?>">
                      </div>
                    </div>
                    <div class="form-check">
                      <label class="form-check-label text-muted">
                        <input type="checkbox" class="form-check-input" name="delete_note" value="delete">
                        Delete all public & shared notes
                      </label>
                    </div>
                    <div align="right">
                      <button type="submit" class="btn btn-danger mr-2">Yes, delete my account</button>
                      <button type="submit" class="btn btn-light mr-2" formaction="account.php">Cancel</button>
                    </div>
                  </form>
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