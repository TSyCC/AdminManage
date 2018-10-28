<?php 

//接收客户端请求数据，做出回应

//载入封装的函数
require_once '../inc/function.php';
get_current_userx();
//判断筛选条件
$arr_status = array('approved','rejected','held');
$where = '1=1';
if (!empty($_GET['status'])&&in_array($_GET['status'], $arr_status)) {
	$where.= ' and comments.`status` = '.'\''.$_GET['status'].'\'';
}

$page = empty($_GET['page']) ? 1 : intval($_GET['page']);
$length = 30;
$offset = ($page - 1)*$length;
$sql = sprintf('select comments.*,posts.title as posts_title from comments inner join posts on comments.post_id = posts.id where %s
order by comments.created desc
limit %d,%d;',$where,$offset,$length);
//查询所有评论数据
$comments = xiu_fetch_all($sql);
//查询数据总数
$total_count = xiu_fetch_one('select count(1) as count from comments inner join posts on comments.post_id = posts.id;')['count'];
$total_pages = ceil($total_count/$length);

//使用json格式的字符串传递数据
$json = json_encode(array('total_pages' => $total_pages,'comments' => $comments,'status' => $_GET['status']));
header('Content-Type: application/json');
//响应给客户端
echo $json;