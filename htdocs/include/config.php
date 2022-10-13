<?php

define('DSN', 'mysql:host=localhost;dbname=notes');
define('DBUSER', 'root');
define('DBPASS', '');

$db = new PDO(DSN, DBUSER, DBPASS);