<?php
if($_SESSION['error']) {
?>
<div class="alert alert-danger" role="alert">
  <?= $_SESSION['error'] ?>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<?php
} else if($_SESSION['success']) {
?>
<div class="alert alert-success" role="alert">
  <?= $_SESSION['success'] ?>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<?php
}
$_SESSION['success'] = null;
$_SESSION['error'] = null;
?>