<?php
if(session_id() == '') {
    session_start();
}
require_once("config.php");
?>

<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
  <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
    <a class="navbar-brand brand-logo mr-5" href="index.php"><img src="images/icon.png" class="mr-2" alt="logo" /></a>
    <a class="navbar-brand brand-logo-mini" href="index.php"><img src="images/icon-mini.png" alt="logo" /></a>
  </div>
  <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
    <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
      <span class="icon-menu"></span>
    </button>
    <form action="../search.php" method="get">
      <ul class="navbar-nav mr-lg-4">
        <li class="nav-item nav-search d-none d-lg-block">
          <div class="input-group">
            <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
              <span class="input-group-text" id="search">
                <i class="icon-search"></i>
              </span>
            </div>
            <input type="text" class="form-control" id="navbar-search-input" placeholder="Search notes" name="search">
          </div>
        </li>
      </ul>
    </form>
    <ul class=" navbar-nav navbar-nav-right">
      <li class="nav-item nav-profile dropdown">
        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown"
          style="margin-right:20px">Welcome, 
          <?php
            if(!$_SESSION['username']){
              echo "Guest!";
            }
            else echo $_SESSION['username']
            ?></a>
        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
          <?php
            $sql = "SELECT foto FROM user WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$_SESSION['user_id']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!ISSET($result['foto'])) {
              $image = "images/default-profile.jpg";
            } else {
              $image = $result['foto'];
            }
          ?>
          <img src="<?= $image ?>" alt="<?= $_SESSION['username'] ?>" />
        </a>
        <?php
          if($_SESSION['username'] && $_SESSION['username'] != 'Guest'){
          echo '<div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
            <a class="dropdown-item" href="./account.php">
              <i class="ti-settings text-primary"></i>
              My Account
            </a>
            <a class="dropdown-item" href="./logout.php">
              <i class="ti-power-off text-primary"></i>
              Logout
            </a>
          </div>';
          }else{
            echo '<div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
            <a class="dropdown-item" href="./login.php">
              <i class="ti-settings text-primary"></i>
              Login
            </a>
          </div>';
          }
        ?>
      </li>
    </ul>
    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
      data-toggle="offcanvas">
      <span class="icon-menu"></span>
    </button>
  </div>
</nav>