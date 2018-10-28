<?php
require_once 'D:/www/baixiu/config.php'; 

// function xiu_query ($sql){
// 	$conn = mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
// 	if (!isset($conn)) {
// 		exit('连接失败');
// 	}
// 	$query = mysqli_query($conn,$sql);
// 	if (!isset($query)) {
// 		return false;
// 	}
// 	return $query;
// }

function xiu_fetch_all ($sql){
	//xiu_query($sql);
	$conn = mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	if (!isset($conn)) {
		exit('连接失败');
	}
	$query = mysqli_query($conn,$sql);
	if (!isset($query)) {
		return false;
	}
	$result = [];
	while ($row = mysqli_fetch_assoc($query)){
		$result[] = $row;
	}
	mysqli_free_result($query);
	mysqli_close($conn);
	return $result;
}


function xiu_fetch_one ($sql){
	$res = xiu_fetch_all($sql);
	return isset($res[0])? $res[0]: null;
}

function xiu_execute ($sql){
	//xiu_query($sql);
	$conn = mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	if (!isset($conn)) {
		exit('连接失败');
	}
	$query = mysqli_query($conn,$sql);
	if (!isset($query)) {
		return false;
	}
	$affected_rows = mysqli_affected_rows($conn);
	mysqli_close($conn);
	return $affected_rows;
}


function get_current_userx () {
	if (isset($GLOBALS['current_user'])) {
    // 已经执行过了（重复调用导致）
    return $GLOBALS['current_user'];
  }
  	session_start();
	if (empty($_SESSION['current_login_user_id']) || !is_numeric($_SESSION['current_login_user_id'])){
    header('location:/admin/login.php');
    exit();
	}
	$GLOBALS['current_user'] = xiu_fetch_one(sprintf('select * from users where id = %d limit 1', intval($_SESSION['current_login_user_id'])));
	return $GLOBALS['current_user'];
}