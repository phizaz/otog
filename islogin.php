<?
session_start();
include('config.php');
$result = array();
if(isLogin() ) $result['islogin'] = true;
else $result['islogin'] = false;
echo json_encode($result);
?>