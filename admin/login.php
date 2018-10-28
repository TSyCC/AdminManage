<?php 
//载入配置文件
require_once '../config.php';

//给用户找个黑箱子
function login (){
  //1.接收并校验
  //2.持久化
  //3.响应
  
  if (empty($_POST['email'])||empty($_POST['password'])) {
    $GLOBALS['error_message'] = '用户名或密码为空！';
    return;
  }
  $email = $_POST['email'];
  $password = $_POST['password'];
  // if ($_POST['email'] !='1310851368@qq.com'||$_POST['password'] !='404524979') {
  //   $GLOBALS['error_message'] = '用户名或密码错误！';
  //   return;
  // }
  
  $conn = mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
  if (!isset($conn)) {
    exit('数据库连接失败');
  }
  //echo "string";
  $query = mysqli_query($conn,"select * from users where email = '{$email}' limit 1;");
  if (!$query) {
    $GLOBALS['error_message'] = '登录失败';
    return;
  }
  $user = mysqli_fetch_assoc($query);
  if (empty($user)){
    //用户名不存在
    $GLOBALS['error_message'] = '用户名或密码不正确！';
    return;
  }
  if ($user['slug'] != 'admin') {
    $GLOBALS['error_message'] = '警告！仅管理员登录';
    return;
  }
  if ($user['password'] !=md5($password)) {
    $GLOBALS['error_message'] = '用户名与密码不匹配';
    return;
  }
  session_start();
  $_SESSION['current_login_user_id'] = $user['id'];
  header('Location:http:/admin/index.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  login();
  //echo $error_message;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'logout') {
  session_start();
  unset($_SESSION['current_login_user_id']);
}
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <link rel="stylesheet" type="text/css" href="/static/assets/vendors/animate/animate.css">
</head>
<body>
  <div class="login">
    <!--通过添加novalidate 关闭客户端自动校验，autocomplete关闭客户端自动完成功能-->
    <form class="login-wrap<?php echo isset($error_message)?' shake animated':''; ?>" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" novalidate autocomplete="off">
      <img class="avatar" src="/static/assets/img/default.png">
      <!-- 有错误信息时展示 -->
      <?php if (isset($error_message)): ?>
      <div class="alert alert-danger">
        <strong>错误！</strong> <?php echo $error_message; ?>
      </div>
      <?php endif ?>
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" type="email" class="form-control" name="email" placeholder="邮箱" autofocus value="<?php echo empty($_POST['email'])?'':$_POST['email']; ?>">
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="密码">
      </div>
      <button class="btn btn-primary btn-block">登录</button>
      <!--<a class="btn btn-primary btn-block" href="/admin/index.php">登 录</a>-->
    </form>
  </div>
</body>
<script type="text/javascript" src="/static/assets/vendors/jquery/jquery.js"></script>
<script type="text/javascript">
  $(function($){
    var emailFormat = /[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[a-zA-Z0-9]+$/
    $('#email').on('blur',function(){
      var value = $(this).val();
      if (!value || !emailFormat.test(value)) return;
        $.get('/admin/api/avatar.php',{ email:value },function(res){
          if(!res){
            $('.avatar').fadeOut(function(){
            $(this).on('load',function(){
              $(this).fadeIn()
            }).attr('src','/static/assets/img/default.png')
          })
            return;
          }
          $('.avatar').fadeOut(function(){
            $(this).on('load',function(){
              $(this).fadeIn()
            }).attr('src',res)
          })
        })
    })
    
  })
</script>
</html>
