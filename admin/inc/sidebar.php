<?php 
//require_once '../config.php';
// $conn = mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
//   if (!isset($conn)) {
//     exit('数据库连接失败');
//   }
//   //echo "string";
// $query = mysqli_query($conn,"select * from users where email = '{$email}' limit 1;");
//   if (!$query) {
//     $GLOBALS['error_message'] = '登录失败';
//     return;
//   }
require_once 'inc/function.php';

 
$user = get_current_userx();


$current_page = basename($_SERVER['PHP_SELF']);

 ?>
<div class="aside">
  <div class="profile">
    <img class="avatar" src="<?php echo isset($current_user['avatar'])?$current_user['avatar'] : '/static/assets/img/default.png';?>">
    <h3 class="name"><?php echo $user['nickname']; ?></h3>
  </div>
  <ul class="nav">
    <li<?php echo $current_page == 'index.php'?' class="active"':''; ?>>
      <a href="/admin/index.php"><i class="fa fa-dashboard"></i>仪表盘</a>
    </li>
    <?php $arr_menu =  array('posts.php','post-add.php','categories.php');?>
    <li<?php echo in_array($current_page, $arr_menu)?' class="active"':''; ?>>
      <a href="#menu-posts"<?php echo in_array($current_page, $arr_menu)?'':' class="collapsed"'; ?> data-toggle="collapse">
        <i class="fa fa-thumb-tack"></i>文章<i class="fa fa-angle-right"></i>
      </a>
      <ul id="menu-posts" class="collapse<?php echo in_array($current_page, $arr_menu)?' in':''; ?>">
        <li<?php echo $current_page == 'posts.php'?' class="active"':''; ?>><a href="/admin/posts.php">所有文章</a></li>
        <li<?php echo $current_page == 'post-add.php'?' class="active"':''; ?>><a href="/admin/post-add.php">写文章</a></li>
        <li<?php echo $current_page == 'categories.php'?' class="active"':''; ?>><a href="/admin/categories.php">分类目录</a></li>
      </ul>
    </li>
    <li<?php echo $current_page == 'comments.php'?' class="active"':''; ?>>
      <a href="/admin/comments.php"><i class="fa fa-comments"></i>评论</a>
    </li>
    <li<?php echo $current_page == 'users.php'?' class="active"':''; ?>>
      <a href="/admin/users.php"><i class="fa fa-users"></i>用户</a>
    </li>
    <?php $arr_settings =  array('nav-menus.php','slides.php','settings.php');?>
    <li<?php echo in_array($current_page, $arr_settings)?' class="active"':''; ?>>
      <a href="#menu-settings"<?php echo in_array($current_page, $arr_menu)?'':' class="collapsed"'; ?> data-toggle="collapse">
        <i class="fa fa-cogs"></i>设置<i class="fa fa-angle-right"></i>
      </a>
      <ul id="menu-settings" class="collapse<?php echo in_array($current_page, $arr_settings)?' in':''; ?>">
        <li<?php echo $current_page == 'nav-menus.php'?' class="active"':''; ?>><a href="/admin/nav-menus.php">导航菜单</a></li>
        <li<?php echo $current_page == 'slides.php'?' class="active"':''; ?>><a href="/admin/slides.php">图片轮播</a></li>
        <li<?php echo $current_page == 'settings.php'?' class="active"':''; ?>><a href="/admin/settings.php">网站设置</a></li>
      </ul>
    </li>
  </ul>
</div>