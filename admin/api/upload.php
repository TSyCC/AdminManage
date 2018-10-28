<?php 

require_once '../inc/function.php';
get_current_userx();
if (empty($_FILES['avatar'])) {
	exit('必须上传文件');
}

$avatar = $_FILES['avatar'];

if ($avatar['error'] != UPLOAD_ERR_OK) {
	exit('上传失败');
}
//校验文件大小

//移动到指定路径
$ext = pathinfo($avatar['name'], PATHINFO_EXTENSION);
$target = '../../static/uploads/img-'.uniqid().'.'.$ext;

if (!move_uploaded_file($avatar['tmp_name'], $target)) {
	exit('上传失败');
}

echo substr($target, 5);


