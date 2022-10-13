<?php

$userAcces = include __DIR__.DIRECTORY_SEPARATOR.'user.php';
return array_merge($userAcces, array(
    'admin',
    'user'
));