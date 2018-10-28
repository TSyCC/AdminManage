<?php 

require_once '../inc/function.php';

get_current_user();

// if (empty($_GET['slug'])) {
// 	exit('请传入必要参数');
// }
$id = $current_user['id'];
if (empty($_GET['slug'])||$_GET['slug'] == $current_user['slug']) {
	echo 0;
}
if (!empty($_GET['slug'])&&$_GET['slug'] != $current_user['slug']) {
	$slug = $_GET['slug'];
	$slugrows = xiu_fetch_one('select $slug from users where id = '.$id.';');
	echo $slugrows;
}

if (!empty($_GET['nickname'])) {
	$nicknamerows = true;
	$nickname = $_GET['nickname'];
	if (strlen($_POST['nickname'])<2 || strlen($_POST['nickname'])>16) {
	$nicknamerows = false;
	}
	echo $nicknamerows;
}


