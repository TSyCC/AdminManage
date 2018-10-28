<?php 
require_once '../../config.php';
 
//接口：根据用户邮箱获取用户头像
if (empty($_GET['email'])) {
	exit('获取失败');
}
$email = $_GET['email'];
//var_dump($email);
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
 if (!isset($conn)) {
    exit('数据库连接失败');
  }
  //echo "string";
  $query = mysqli_query($conn,"select avatar from users where email = '{$email}' limit 1;");
  if (!$query) {
    $GLOBALS['error_message'] = '登录失败';
    return;
  }
  $row = mysqli_fetch_assoc($query);
  echo $row['avatar'];