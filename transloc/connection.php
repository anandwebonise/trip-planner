<?php
$server = "127.0.0.1";
$user = 'root';
$password  = 'vijay6186';
$dbName = 'transloc';

if($_SERVER['SERVER_ADDR'] != '127.0.0.1'){
    $server = "localhost";
    $password  = 'webonise6186';
    $dbName = 'transloc';
}

$mysql_connection = mysql_connect($server, $user, $password);

if ($mysql_connection === false) die("<pre>".print_r(mysql_error(), true));

mysql_select_db($dbName);