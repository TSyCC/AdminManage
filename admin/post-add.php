<?php

 require_once 'inc/function.php';
get_current_userx();
//添加逻辑函数
function add_post () {
  //判断是否为空
  global $current_user;
  if (empty($_POST['title'])
    ||empty($_POST['content'])
    ||empty($_POST['slug'])
    ||empty($_POST['created'])
    ||empty($_POST['status'])
    ||empty($_POST['category'])) {
    $GLOBALS['error_message'] = '请完整填写表单';
    return;
  }
  $slug = $_POST['slug'];
  if (xiu_fetch_one("select count(1) as num from posts where slug = '{$slug}';")['num']>0) {
    $GLOBALS['error_message'] = '别名重复';
    return;
  }
  //var_dump($_FILES['feature']['error']);
  if (empty($_FILES['feature']['error'])) {
    if ($_FILES['feature']['size'] > 10*1024*1024){
      $GLOBALS['error_message'] = '上传文件超出大小限制';
      return;
      }
    $tmp_file = $_FILES['feature']['tmp_name'];
    $target_file = '../static/uploads/'.uniqid().$_FILES['feature']['name'];
    if (move_uploaded_file($tmp_name, $target_file)) {
      $image_file = substr($target_file, 2);
    }
  }
  //接收数据
    $GLOBALS['created_message'] = ($_POST['created']);
    $title = $_POST['title'];
    $feature = isset($image_file) ? $image_file : '';
    $created = $_POST['created'];
    $content = $_POST['content'];
    $status = $_POST['status'];
    $user_id = $current_user['id'];
    $category_id = $_POST['category'];

    //定义查询语句
    $sql = sprintf(
      "insert into posts values (null, '%s', '%s', '%s', '%s', '%s', 0, 0, '%s', %d, %d)",
      $slug,
      $title,
      $feature,
      $created,
      $content,
      $status,
      $user_id,
      $category_id
    );
    $rows = xiu_execute($sql);
    if ($rows<=0) {
      $GLOBALS['error_message'] = '保存失败';
      return;
    }
    header('Location:/admin/posts.php');
}

//编辑逻辑……
if (!empty($_GET['id'])) {
  $current_edit_posts = xiu_fetch_one('select * from posts where id = '.$_GET['id']);
  $timestamp = strtotime($current_edit_posts['created']);
  $created = date('Y-m-d\TH:i',$timestamp);
}
function edit_post () {
  global $current_edit_posts;
  global $created;

  $slug = empty($_POST['slug'])?$current_edit_posts['slug']:$_POST['slug'];
  if (xiu_fetch_one("select count(1) as num from posts where slug = '{$slug}';")['num']>0 && $slug!=$current_edit_posts['slug']) {
    $GLOBALS['error_message'] = '别名重复';
    return;
  }
  //var_dump($_FILES['feature']['error']);
  if (empty($_FILES['feature']['error'])) {
    if ($_FILES['feature']['size'] > 10*1024*1024){
      $GLOBALS['error_message'] = '上传文件超出大小限制';
      return;
      }
    $tmp_file = $_FILES['feature']['tmp_name'];
    $target_file = '../static/uploads/'.uniqid().$_FILES['feature']['name'];
    if (move_uploaded_file($tmp_name, $target_file)) {
      $image_file = substr($target_file, 2);
    }
  }

    $title = empty($_POST['title'])?$current_edit_posts['title']:$_POST['title'];
    $feaurl = isset($image_file) ? $image_file : '';
    $feature = empty($_FILES['feature'])?$current_edit_posts['feature'] : $feaurl;
    $created = empty($_POST['created'])?$created : $_POST['created'];
    $content = empty($_POST['content'])?$current_edit_posts['content'] : $_POST['content'];
    $status = $_POST['status'];
    //不允许修改用户$user_id = $current_user['id'];
    $category_id = $_POST['category'];

     $sql = sprintf(
      "uodate posts set slug = '%s',title = '%s',feature = '%s',created = '%s',content = '%s',status = '%s',category_id = '%s' where id = %d",
      $slug,
      $title,
      $feature,
      $created,
      $content,
      $status,
      $category_id,
      $current_edit_posts['id']
    );
    $rows = xiu_execute($sql);
    if ($rows!=1) {
      $GLOBALS['error_message'] = '编辑失败';
      return;
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (empty($_GET['id'])) {
    //执行添加逻辑
    add_post();
  }else{
    //执行编辑逻辑
    edit_post();
  }
}
//var_dump($user_id);
$categories = xiu_fetch_all('select * from categories');
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Add new post &laquo; Admin</title>
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
        <h1>写文章</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
        <strong>错误！</strong><?php echo $error_message; ?>
      </div>
      <?php endif ?>
      <form class="row" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?><?php echo isset($current_edit_posts)?'?id='.$current_edit_posts['id']:''; ?>" method="post" enctype="multipart/form-data">
        <div class="col-md-9">
          <div class="form-group">
            <label for="title">标题</label>
            <input id="title" class="form-control input-lg" name="title" value="<?php echo isset($_POST['title'])? $_POST['title']:''; ?><?php echo isset($current_edit_posts['title']) ? $current_edit_posts['title']:''; ?>" type="text" placeholder="文章标题">
          </div>
          <div class="form-group">
            <label for="container">内容</label>
            <script id="container" name="content" type="text/plain"><?php echo isset($_POST['content'])? $_POST['content']:'' ?><?php echo isset($current_edit_posts['content']) ? $current_edit_posts['content']:''; ?></script>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="slug">别名</label>
            <input id="slug" class="form-control" name="slug" value="<?php echo isset($_POST['slug'])? $_POST['slug']:''; ?><?php echo isset($current_edit_posts['slug']) ? $current_edit_posts['slug']:''; ?>" type="text" placeholder="slug">
          </div>
          <div class="form-group">
            <label for="feature">特色图像</label>
            <!-- show when image chose -->
            <img class="help-block thumbnail" style="display: none">
            <input id="feature" class="form-control" name="feature" type="file">
          </div>
          <div class="form-group">
            <label for="category">所属分类</label>
            <select id="category" class="form-control" name="category">
              <?php foreach ($categories as $item): ?>
                <option value="<?php echo $item['id'] ?>"<?php echo isset($current_edit_posts['category_id'])&&$current_edit_posts['category_id'] == $item['id'] || isset($_POST['category'])&&$_POST['category'] == $item['id'] ? ' selected':''; ?>><?php echo $item['name'] ?></option>
              <?php endforeach ?>
            </select>
          </div>
          <div class="form-group">
            <label for="created">发布时间</label>
            <input id="created" class="form-control" name="created" type="datetime-local" value="<?php echo isset($_POST['created'])? $_POST['created']:''; ?><?php echo isset($created) ? $created:''; ?>">
          </div>
          <div class="form-group">
            <label for="status">状态</label>
            <select id="status" class="form-control" name="status">
              <option value="drafted"<?php echo isset($_POST['status'])&&$_POST['status'] == 'drafted' || isset($current_edit_posts['status'])&&$current_edit_posts['status'] == 'drafted'?' selected':''; ?>>草稿</option>
              <option value="published"<?php echo isset($_POST['status'])&&$_POST['status'] == 'published' || isset($current_edit_posts['status'])&&$current_edit_posts['status'] == 'published'?' selected':''; ?>>已发布</option>
              <option value="trashed"<?php echo isset($_POST['status'])&&$_POST['status'] == 'trashed' || isset($current_edit_posts['status'])&&$current_edit_posts['status'] == 'trashed'?' selected':''; ?>>回收站</option>
            </select>
          </div>
          <div class="form-group">
            <button class="btn btn-primary" type="submit">保存</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/ueditor/ueditor.config.js"></script>
  <script src="/static/assets/vendors/ueditor/ueditor.all.js"></script>
  <script type="text/javascript">
    var ue = UE.getEditor('container');
  </script>
  <script>NProgress.done()</script>
</body>
</html>