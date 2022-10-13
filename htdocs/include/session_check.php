<?php

if(session_id() == '') {
    session_start();
}
if(!isset($_SESSION['username']) && !isset($_SESSION['user_id'])) {
  /* $_SESSION['error'] = "Please login first!";
  header('location: login.php'); */
  $_SESSION['username'] = "Guest";
  $_SESSION['user_id'] = "Guest";
}