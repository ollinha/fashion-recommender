<?php
$site = 'http://master.yanenko.de/';

setcookie('user_id', '', time() - 3600);
setcookie('session_id', '', time() - 3600);

header('Location: '.$site.'index.php?quit=ok');
?>