<?php 
require_once 'inc/function.php';
get_current_userx();
function xiu_delete (){
	if (empty($_GET['id'])) {
		exit('请传入参数');
	}

	//$id = (int)$_GET['id'];
	$id = $_GET['id'];
	$arr_id = mb_split(',', $id);
	foreach ($arr_id as $item) {
		if (!is_numeric($item)) {
			exit('不要乱来');
		}
	}
	// var_dump($di);
	$rows = xiu_execute('delete from users where id in ('.$id.');');
	header('Content_Type: application/json');
	echo json_encode($rows>0);
}

 if ($_SERVER['REQUEST_METHOD'] == 'GET') {
 	xiu_delete();
 }