<?php

 require_once 'inc/function.php';
get_current_userx();
$users = xiu_fetch_one("select * from users where id = {$current_user['id']} limit 1");
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Dashboard &laquo; Admin</title>
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
        <h1>我的个人资料</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <form class="form-horizontal" action="upload.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
          <label class="col-sm-3 control-label">头像</label>
          <div class="col-sm-6">
            <label class="form-image">
              <input id="avatar" type="file">
              <img src="<?php echo isset($users['avatar'])?$users['avatar'] : '/static/assets/img/default.png';?>">
              <input type="hidden" name="avatar" value="<?php echo isset($users['avatar'])?$users['avatar'] : '/static/assets/img/default.png'; ?>">
              <i class="mask fa fa-upload"></i>
            </label>
          </div>
        </div>
        <div class="form-group">
          <label for="email" class="col-sm-3 control-label">邮箱</label>
          <div class="col-sm-6">
            <input id="email" class="form-control" name="email" type="type" value="<?php echo $users['email']; ?>" placeholder="邮箱" readonly>
            <p class="help-block">登录邮箱不允许修改</p>
          </div>
        </div>
        <div class="form-group">
          <label for="slug" class="col-sm-3 control-label">别名</label>
          <div class="col-sm-6">
            <input id="slug" class="form-control" name="slug" type="type" value="<?php echo $users['slug']; ?>" placeholder="slug">
            <strong class="alert-danger one" style="display: none;">*别名不合法</strong>
          </div>
        </div>
        <div class="form-group">
          <label for="nickname" class="col-sm-3 control-label">昵称</label>
          <div class="col-sm-6">
            <input id="nickname" class="form-control" name="nickname" type="type" value="<?php echo $users['nickname']; ?>" placeholder="昵称">
            <p class="help-block">限制在 2-16 个字符</p>
            <strong class="alert-danger two" style="display: none;">*昵称不合法</strong>
          </div>
        </div>
        <div class="form-group">
          <label for="bio" class="col-sm-3 control-label">简介</label>
          <div class="col-sm-6">
            <textarea id="bio" class="form-control" name="contents" placeholder="Bio" cols="30" rows="6"><?php echo isset($users['bio'])?$users['bio']:''; ?></textarea>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-6">
            <button type="submit" class="btn btn-primary">更新</button>
            <a class="btn btn-link" href="password-reset.php">修改密码</a>
          </div>
        </div>
      </form>
    </div>
  </div>

  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script type="text/javascript">
    $('#avatar').on('change',function(){
      //当文件选择状态变化会执行这个事件处理函数
      //判断是否选中了文件
      var $that = $(this)
      var files = $that.prop('files')
      if (!files.length) return

      var file = files[0]
    //formdata 是 html中新增的成员，配合ajax操作，用于传递二进制数据
      var data = new FormData()
      data.append('avatar',file)

      var xhr = new XMLHttpRequest()
      xhr.open('POST','/admin/api/upload.php')
      xhr.send(data)//借助于form data传递二进制
      xhr.onload = function(){
        $that.siblings('img').attr('src',this.responseText)
        $that.siblings('input').val(this.responseText)
      }
    })


    //客户端实时校验

    // var regFormat = /^[a-zA-Z0-9\u4e00-\u9fa5]{2,16}$/gm
    // var $slug = $('#slug');
    // var $nickname = $('#nickname');
    // $slug.on('blur',function(){
    //   var value = $(this).val();
    //   if (!value || !regFormat.test(value)) return;
    //     $.get('/admin/api/user-message.php',{ slug:value },function(res){
    //       if(res) {
    //         $('strong.one').fadeIn()
            
    //       }else{
    //         $('strong.one').fadeOut()
    //       } 
    //     })
    // })
    // $nickname.on('blur',function(){
    //   var value = $(this).val();
    //   if (!value || !regFormat.test(value)) return;
    //     $.get('/admin/api/user-message.php',{ nickname:value },function(res){
    //       if(res) return;
    //       $('strong.two')[1].css('display','block')
    //     })
    // })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
