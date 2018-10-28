<?php
/**
 * 修改评论状态
 * POST 方式请求
 * - id 参数在 URL 中
 * - status 参数在 form-data 中
 * 两种参数混着用
 */

require_once 'inc/function.php';
get_current_userx();

// 设置响应类型为 JSON
header('Content-Type: application/json');

if (empty($_GET['id']) || empty($_POST['status'])) {
  // 缺少参数
  exit('请传入参数');
}
$id = $_GET['id'];
$arr_id = mb_split(',', $id);
	foreach ($arr_id as $item) {
		if (!is_numeric($item)) {
			exit('不要乱来');
		}
	}

// 拼接 SQL 并执行
$affected_rows = xiu_execute(sprintf("update comments set status = '%s' where id in (%s)", $_POST['status'], $_GET['id']));

// 输出结果
echo json_encode($affected_rows > 0);
