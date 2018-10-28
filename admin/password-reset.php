<?php

require_once 'inc/function.php';
get_current_userx();
function passwordReset (){
  global $current_user;
  if (empty($_POST['old'])||empty($_POST['password'])||empty($_POST['confirm'])) {
    $GLOBALS['error_message'] = '密码为空！';
    return;
  }
  $old = $_POST['old'];
  if ($current_user['password']!= md5($old)) {
    $GLOBALS['error_message'] = '密码错误！';
    return;
  }
  $password = $_POST['password'];
  $passwordReset = $_POST['confirm'];
  if ($password !== $passwordReset) {
    $GLOBALS['error_message'] = '新密码输入不一致！';
    return;
  }
  $password = md5($password);
  $sql = "update users set password = '{$password}', where id = {$current_user['id']}";
  $rows = xiu_execute($sql);
  if ($rows <=0) {
     $GLOBALS['error_message'] = '密码修改失败';
     return;
  }
  session_start();
  unset($_SESSION['current_login_user_id']);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  passwordReset();
}
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Password reset &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'inc/navbar.php'; ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>修改密码</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
        <strong>错误！</strong><?php echo $error_message; ?>
        </div>
      <?php endif ?>
      <form class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <div class="form-group">
          <label for="old" class="col-sm-3 control-label">旧密码</label>
          <div class="col-sm-7">
            <input id="old" class="form-control" type="password" name="old" placeholder="旧密码">
          </div>
        </div>
        <div class="form-group">
          <label for="password" class="col-sm-3 control-label">新密码</label>
          <div class="col-sm-7">
            <input id="password" class="form-control" type="password" name="password" placeholder="新密码">
          </div>
        </div>
        <div class="form-group">
          <label for="confirm" class="col-sm-3 control-label">确认新密码</label>
          <div class="col-sm-7">
            <input id="confirm" class="form-control" type="password" name="confirm" placeholder="确认新密码">
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-7">
            <button type="submit" class="btn btn-primary">修改密码</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <!-- <?php $current_page = 'password-reset'; ?> -->
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
</body>
</html>
