<?php
include('connect.php');

Header('Cache-Control: no-cache');
Header('Pragma: no-cache');

$site = 'http://master.yanenko.de/knowledge/';

if (isset($_POST['login'])) {
	if ($_POST['nickname'] == 'admin' && $_POST['password'] = 'casper6695') {
		setcookie('master_id', '0934222320017620106103', 0);
		header('Location: '.$site.'start.php');
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Shopping Experiment</title>
<link href="../testshop.css" rel="stylesheet" type="text/css" media="screen" />
</head>

<body>
<div id="page">
	<div id="head">
    	<div id="logo">
        	Experiment<br /><span>Login</span>
        </div>
        <div id="credit"></div>
        <div id="nav_main"></div>
        <div class="clearing">&nbsp;</div>
    </div>
    <div id="main">
    	<div id="cart"></div>
    	<div id="nav">&nbsp;</div>
        <div id="content">
            <table>
                <form action="index.php" method="POST">
                <tr>
                    <td class="title">Nickname: </td>
                    <td><input type="text" name="nickname" /></td>
                </tr>
                <tr>
                    <td class="title">Passwort: </td>
                    <td><input type="password" name="password" /></td>
                </tr>
                <tr>
                    <td class="title">&nbsp;</td>
                    <td><input type="submit" name="login" value="einloggen" /></td>
                </tr>
                </form>
            </table>
        </div>
        <div class="clearing">&nbsp;</div>
    </div>
</div>
</body>
</html>
