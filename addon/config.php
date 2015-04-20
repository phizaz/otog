<?php

include('mysql.php');

define('HOST',$mysql['host']);
define('USER',$mysql['user']);
define('PASS',$mysql['pass']);
define('DATA',$mysql['database']);

$sql = mysql_connect(HOST,USER,PASS);
mysql_select_db(DATA,$sql);
mysql_query("SET NAMES UTF8");

?>