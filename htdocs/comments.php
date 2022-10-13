<?php
include 'config.php';
try {
    $pdo = new PDO('mysql:host=' . db_host . ';dbname=' . db_name . ';charset=' . db_cahrset, db_user, db_pass);
    $pdo->setAttribute(PDO::ATTTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch (PDOException $exception) {
    exit('failed to connect to database');
}
function time_elapsed_string($datetime, $full = false){
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
}

?>