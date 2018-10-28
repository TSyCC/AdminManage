<?php 

require_once 'inc/function.php';
//检查登录
get_current_userx();
//验证请求方式
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	exit('更新失败');
}
//验证数据
if (empty($_POST['avatar'])
	|| empty($_POST['email'])
	|| empty($_POST['slug'])
	|| empty($_POST['nickname'])
	|| empty($_POST['contents'])) {
	exit('请填写完整表单');
}

//验证数据是否合法
$slug = $_POST['slug'];
if (xiu_fetch_one("select count(1) as num from posts where slug = '{$slug}';")['num']>0 && $slug!=$current_user['slug']) {
	exit('别名重复');
}
if (strlen($_POST['nickname'])<2 || strlen($_POST['nickname'])>16) {
	exit('昵称不符合要求');
}
//接收数据
$avatar = $_POST['avatar'];
$nickname = $_POST['nickname'];
$bio = $_POST['contents'];
$sql = sprintf("update users set avatar = '%s',slug = '%s',nickname = '%s',bio = '%s' where id = %d",$avatar,$slug,$nickname,$bio,$current_user['id']);

$rows = xiu_execute($sql);
//echo $rows;
header('Location:/admin/profile.php');