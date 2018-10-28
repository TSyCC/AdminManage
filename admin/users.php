<?php

 require_once 'inc/function.php';
get_current_userx();


//添加逻辑
function add_cate (){
  $GLOBALS['success'] = false;
  if (empty($_POST['email'])
    ||empty($_POST['slug'])
    ||empty($_POST['nickname'])
    ||empty($_POST['password'])) {
    $GLOBALS['error_message'] = '请完整填写表单';
    return;
  }
  $emailFormat = '/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/';
  if (!preg_match($emailFormat,$_POST['email'])) {
    $GLOBALS['error_message'] = '邮箱格式错误';
    return;
  }
  $slug = $_POST['slug'];
   if (xiu_fetch_one("select count(1) as num from users where slug = '{$slug}';")['num']>0) {
     $GLOBALS['error_message'] = '别名重复';
      return;
   }
  $email = $_POST['email'];
   if (xiu_fetch_one("select count(1) as num from users where email = '{$email}';")['num']>0) {
     $GLOBALS['error_message'] = '邮箱名重复';
      return;
   }
  if ($_POST['password']!=$_POST['repassword']) {
    $GLOBALS['error_message'] = '两次密码输入不一致';
    return;
  }

  $nickname = $_POST['nickname'];
  $password = md5($_POST['password']);
  $rows = xiu_execute("insert into users values (null,'{$slug}','{$email}','{$password}','{$nickname}',null,null,'unactivated');");
  $GLOBALS['success'] = $rows >0;
  $GLOBALS['error_message'] = $rows <=0?'添加失败':'添加成功';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (empty($_GET['id'])) {
    add_cate();
  }
  
}



 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Users &laquo; Admin</title>
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
        <h1>用户</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($error_message)): ?>
      <?php if ($success == true): ?>
        <div id="success" class="alert alert-success">
        <strong>添加成功！</strong>
        </div>
      <?php else: ?>
        <div class="alert alert-danger">
        <strong>错误！</strong><?php echo $error_message ?>
        </div>
      <?php endif ?>
      <?php endif ?>
      <div class="row">
        <div class="col-md-4">
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <h2>添加新用户</h2>
            <div class="form-group">
              <label for="email">邮箱</label>
              <input id="email" class="form-control" name="email" type="email" placeholder="邮箱">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
            </div>
            <div class="form-group">
              <label for="nickname">昵称</label>
              <input id="nickname" class="form-control" name="nickname" type="text" placeholder="昵称">
            </div>
            <div class="form-group">
              <label for="password">密码</label>
              <input id="password" class="form-control" name="password" type="text" placeholder="请输入密码">
            </div>
            <div class="form-group">
              <label for="repassword">密码</label>
              <input id="repassword" class="form-control" name="repassword" type="text" placeholder="请再次输入密码">
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
            </div>
          </form>
        </div>
        <div class="col-md-8">
          <div class="page-action form-inline">
            <select name="status" class="form-control input-sm">
                <option value="all">所有状态</option>
                <option value="activated">已激活</option>
                <option value="unactivated">未激活</option>
        </select>
        <button id="select_Btn" class="btn btn-default btn-sm">筛选</button>
            <!-- show when multiple checked -->
        <div class="btn-batch" style="display: none">
          <button class="btn btn-info btn-sm">批量激活</button>
          <button class="btn btn-danger btn-sm">批量删除</button>
        </div>
            <ul class="pagination pagination-sm pull-right">
        </ul>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
               <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th class="text-center" width="80">头像</th>
                <th>邮箱</th>
                <th>别名</th>
                <th>昵称</th>
                <th>状态</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

 <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/jsrender/jsrender.js"></script>
  <script src="/static/assets/vendors/twbs-pagination/jquery.twbsPagination.js"></script>
  <script type="text/x-jsrender" id="users_tmpl">
    {{for users}}
    <tr{{if status == 'unactivated'}} class="warning"{{else status == 'activated'}} class="success"{{/if}} data-id="{{:id}}">
      <td class="text-center"><input type="checkbox"></td>
      <td>
      <div class="profile">
        <img class="avatar" src="{{if avatar != null}}{{:avatar}}{{else}}/static/assets/img/default.png{{/if}}">
      </div>
      </td>
      <td>{{:email}}</td>
      <td>{{:slug}}</td>
      <td>{{:nickname}}</td>
      <td>{{:status}}</td>
      <td class="text-center">
        {{if status == 'activated'}}
        <a href="javascript:;" class="btn btn-danger btn-xs delete-btn">删除</a>
        {{else}}
        <a href="javascript:;" class="btn btn-success btn-xs btn-edit" data-status="activated">激活</a>
        <a href="javascript:;" class="btn btn-danger btn-xs delete-btn">删除</a>
        {{/if}}
      </td>
    </tr>
    {{/for}}
  </script>
  <script type="text/javascript">
    var $selected = $('select')
    //console.log($selected.val());
    var $selectBtn = $('#select_Btn')
    var $tbodyselect = $('tbody')
    var currentPage = 1
    var currentStatus = 'all'
    var $btnBatch = $('.btn-batch')
    var checkedItems = []
  function loadPageData(page,status){
      
      $.get('/admin/api/users-list.php',{ page:page,status:status },function(res){
      $('.pagination').twbsPagination({
      first:'首页',
      last:'尾页',
      prev:'上一页',
      next:'下一页',
      totalPages: res.total_Pages,
      visiablePages: 5,
      initiateStartPageClick: false,
      onPageClick:function(e,page){
        loadPageData(page,res.status);
        currentPage = page;
        currentStatus = res.status;
      }
    })
    var html = $('#users_tmpl').render({users: res.users})
    $tbodyselect.fadeOut(function(){
      $(this).html(html).fadeIn()
    })
    })
  }
    loadPageData(currentPage,currentStatus);
//筛选逻辑
    $selectBtn.on('click',function(){
      var $status = $selected.val()
      loadPageData(1,$status);
    })


    // $('.delete-btn').on('click',function(){
    //   console.log(11)
    // })
    //删除功能逻辑
    $tbodyselect.on('click','.delete-btn',function(){
      var $tr = $(this).parent().parent()
        var id = parseInt($tr.data('id'))
        $.get('/admin/users-delete.php', { id: id }, function (res) {
          //console.log(res)
          if (!res) return
          loadPageData(currentPage,currentStatus)
          //res.success && loadData()
        })
    })
    //修改用户状态状态逻辑
    $tbodyselect.on('click', '.btn-edit', function () {
        var id = parseInt($(this).parent().parent().data('id'))
        var status = $(this).data('status')
        $.post('/admin/users-status.php?id=' + id, { status: status }, function (res) {
          if (!res) return
          loadPageData(currentPage,currentStatus)
        })
      })

    //批量操作显示
    $tbodyselect.on('change', 'td > input[type=checkbox]', function () {
        var id = parseInt($(this).parent().parent().data('id'))
        if ($(this).prop('checked')) {
          checkedItems.push(id)
        } else {
          checkedItems.splice(checkedItems.indexOf(id), 1)
        }
        checkedItems.length>1 ? $btnBatch.fadeIn() : $btnBatch.fadeOut()
      })

      // 全选 / 全不选
      $('th > input[type=checkbox]').on('change', function () {
        var checked = $(this).prop('checked')
        $('td > input[type=checkbox]').prop('checked', checked).trigger('change')
      })

      // 批量操作
      $btnBatch
        // 激活
        .on('click', '.btn-info', function () {
          $.post('/admin/users-status.php?id=' + checkedItems.join(','), { status: 'activated' }, function (res) {
            if (!res) return
          loadPageData(currentPage,currentStatus)
          })
        })
        // 删除
        .on('click', '.btn-danger', function () {
          $.get('/admin/users-delete.php', { id: checkedItems.join(',') }, function (res) {
            if (!res) return
          loadPageData(currentPage,currentStatus)
          })
        })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
