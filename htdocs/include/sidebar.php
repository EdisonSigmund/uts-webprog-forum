<?php
if(session_id() == '') {
    session_start();
}
require_once("config.php");
?>
<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item">
      <a class="nav-link" href="./index.php">
        <i class="icon-paper menu-icon"></i>
        <span class="menu-title">Forum</span>
      </a>
    </li>
    <!-- <li class="nav-item">
      <a class="nav-link" href="./shared.php">
        <i class="icon-share menu-icon"></i>
        <span class="menu-title">Shared with me</span>
      </a>
    </li> -->
    <?php
    if (!$_SESSION['username'] || $_SESSION['username'] == 'Guest'){
    }
    else {
      echo '<li class="nav-item">
      <a class="nav-link" href="./account.php">
        <i class="icon-head menu-icon"></i>
        <span class="menu-title">My Account</span>
      </a>
    </li>';
    }
    ?>
    
  </ul>
</nav>