<?php

require_once 'inc/function.php';
get_current_userx();
$posts_count = xiu_fetch_one('select count(1) as num from posts;');
var_dump($posts_count);
$posts_count_draft = xiu_fetch_one("select count(1) as num from posts where status = 'drafted';");
//var_dump($posts_count_draft);
$categories_count = xiu_fetch_one('select count(1) as num from categories;');
$comments_count = xiu_fetch_one('select count(1) as num from comments;');
$comments_count_held = xiu_fetch_one("select count(1) as num from comments where status = 'held';");
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
      <div class="jumbotron text-center">
        <h1>One Belt, One Road</h1>
        <p>Thoughts, stories and ideas.</p>
        <p><a class="btn btn-primary btn-lg" href="post-add.php" role="button">写文章</a></p>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">站点内容统计：</h3>
            </div>
            <ul class="list-group">
              <li class="list-group-item"><strong><?php echo $posts_count['num']; ?></strong>篇文章（<strong><?php echo $posts_count_draft['num']; ?></strong>篇草稿）</li>
              <li class="list-group-item"><strong><?php echo $categories_count['num']; ?></strong>个分类</li>
              <li class="list-group-item"><strong><?php echo $comments_count['num']; ?></strong>条评论（<strong><?php echo $comments_count_held['num']; ?></strong>条待审核）</li>
            </ul>
          </div>
        </div>
        <div class="col-md-4"></div>
        <div class="col-md-4"></div>
      </div>
    </div>
  </div>

  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
</body>
</html>
