<?php

require_once 'inc/function.php';
get_current_userx();
//处理筛选
$arr_status = array('published','drafted','trashed' );
$arr_categories = xiu_fetch_all('select * from categories;');
//$arr_categories_id = [];
foreach ($arr_categories as $item) {
  $arr_categories_id[] = $item['id'];
}
// var_dump($arr_categories_id);
// if (!in_array($_GET['status'], $arr_status) {
//   exit();
// }
$where = '1 = 1';
$search = '';
if(!empty($_GET['category'])&&in_array($_GET['category'], $arr_categories_id)){
  $where .= ' and posts.category_id = '.$_GET['category'];
  $search.= '&category='.$_GET['category'];
}
if(!empty($_GET['status'])&&in_array($_GET['status'], $arr_status)){
  $where .= ' and posts.`status` = '.'\''.$_GET['status'].'\'';
  $search.= '&status='.$_GET['status'];
}
//处理分页参数
$size = 20;
$xiu_count = (int)ceil((int)xiu_fetch_one('select count(1) as num from posts
  inner join categories on posts.category_id = categories.id
  inner join users on posts.user_id = users.id
  where '.$where.';')['num']/$size);
$xiu_count = $xiu_count == 0? 1 : $xiu_count;
//var_dump($xiu_count);
//$xiu_count = $xiu_count%20 == 0? $xiu_count/20 : floor($xiu_count/20) +1;
$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
if ($page<1) {
  header('Location:/admin/posts.php?page=1'.$search);
}
if ($page>$xiu_count) {
  header('Location:/admin/posts.php?page='.$xiu_count.$search);
}
$skip = ($page - 1) * $size;
//获取全部数据
$posts = xiu_fetch_all('select posts.id,posts.title,users.nickname,categories.`name` as categories_name,posts.created,posts.`status`
from posts inner join categories on posts.category_id = categories.id
inner join users on posts.user_id = users.id
where '.$where.'
order by posts.created desc
limit '.$skip.','.$size.';');
if (empty($posts)) {
  $GLOBALS['error_message'] = '未查询到数据，请稍后再试';
  //header('Location:/admin/posts.php');
}

//处理分页页码
$visiables = 5;//可显示页码
$region = ($visiables-1)/2;//左右区间->2
$begin = $page - $region;//开始页码
$end = $page + $region;//结束页码
//可能出现的不合理情况
//$begin 必须大于0
if($begin < 1){
  $end = $visiables;
  //$begin == 0 ? $end + 1: $end + 2;
  $begin = 1;
}
//$end 必须小于等于最大页数
if($end > $xiu_count){
  $end = $xiu_count;
  $begin = $xiu_count - $visiables + 1;
  if($begin < 1){
  $begin = 1;
}
}
//处理数据转换逻辑
function convert_status ($status) {
  $dict = array('published' => '已发布','drafted' => '草稿', 'trashed' => '回收站');
  return isset($dict[$status]) ? $dict[$status] : '未知状态';
}
function convert_created ($created){
  $timestamp = strtotime($created);
  return date('Y年m月d日<b\r>H:i:s',$timestamp);
}
// function get_category ($category_id){
//   return xiu_fetch_one("select name from categories where id = {$category_id}")['name'];
// }
// function get_user ($user_id){
//   return xiu_fetch_one("select nickname from users where id = {$user_id}")['nickname'];
// }



?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
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
        <h1>所有文章</h1>
        <a href="post-add.php" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($error_message)): ?>
      <div class="alert alert-danger">
        <strong>错误！</strong><?php echo $error_message; ?>
      </div>
      <?php endif ?>     
      <div class="page-action">
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF']; ?>">
          <select name="category" class="form-control input-sm">
            <option value="all">所有分类</option>
            <?php foreach ($arr_categories as $item): ?>
              <option value="<?php echo $item['id']; ?>"<?php echo isset($_GET['category'])&&$_GET['category'] === $item['id']?' selected':''; ?>><?php echo $item['name']; ?></option>
            <?php endforeach ?>            
          </select>
          <select name="status" class="form-control input-sm">
            <option value="all">所有状态</option>
            <?php foreach ($arr_status as $item): ?>
              <option value="<?php echo $item; ?>"<?php echo isset($_GET['status'])&&$_GET['status']=== $item?' selected':''; ?>><?php echo convert_status($item); ?></option>
            <?php endforeach ?> 
          </select>
          <button class="btn btn-default btn-sm">筛选</button>
        </form>
        <!-- show when multiple checked -->
        <a class="btn btn-danger btn-sm" id="btn_delete" href="/admin/api/posts-delete.php" style="display: none">批量删除</a>
        <ul class="pagination pagination-sm pull-right">
          <?php if ($page>1): ?>
            <li><a href="?page=<?php echo $page-1; ?>">上一页</a></li>
          <?php endif ?>         
          <?php for($i = $begin;$i<=$end;$i++): ?>
          <li<?php echo $i === $page?' class="active"':''; ?>>
          <a href="?page=<?php echo $i.$search ?>"><?php echo $i; ?></a></li>
          <?php endfor ?>
          <?php if ($page<$xiu_count): ?>
            <li><a href="?page=<?php echo $page + 1; ?>">下一页</a></li>
          <?php endif ?>
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($posts)): ?>
           <?php foreach ($posts as $item): ?>
            <tr>
            <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id']; ?>"></td>
            <td><?php echo $item['title']; ?></td>
            <td><?php echo $item['nickname']; ?></td>
            <td><?php echo $item['categories_name']; ?></td><!--get_category($item['category_id'])-->
            <td class="text-center"><?php echo convert_created($item['created']); ?></td>
            <td class="text-center"><?php echo convert_status($item['status']); ?></td>
            <td class="text-center">
              <a href="/admin/post-add.php?id=<?php echo $item['id']; ?>" class="btn btn-default btn-xs">编辑</a>
              <a href="/admin/api/posts-delete.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr>
          <?php endforeach ?> 
          <?php endif ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script type="text/javascript">
    $(function($){
      // if ($isdis == 'block') {
      //   setTimeout(function(){
      //     $('#success').css('display':'none')
      //   },1000);
      // }
        var $tbodycheckboxs = $('tbody input')
        var $theadcheckbox = $('thead input')
        var $btndelete = $('#btn_delete')
        //console.log($tbodycheckboxs.length)
          var allCheckeds = []
          $tbodycheckboxs.on('change',function(){
          var id = $(this).data('id')
          if($(this).prop('checked')){
            allCheckeds.push(id)
          }else{
            allCheckeds.splice(allCheckeds.indexOf(id),1)
          }
          allCheckeds.length>1?$btndelete.fadeIn():$btndelete.fadeOut()
          $btndelete.prop('search','?id='+allCheckeds)
        })
        
        $theadcheckbox.on('change',function(){
          if($(this).prop('checked')){
            allCheckeds = []
            $tbodycheckboxs.prop('checked',true).trigger('change');           
            // $tbodycheckboxs.each(function(i,item){
            //   var id = $(item).data('id');
            //   //console.log($(item))
            //   allCheckeds.push(id)              
            // })
            // allCheckeds.length>1?$btndelete.fadeIn():$btndelete.fadeOut()
            // $btndelete.prop('search','?id='+allCheckeds)
          }else{
            $tbodycheckboxs.prop('checked',false)
            allCheckeds = []
            //console.log($allCheckeds.length)
            allCheckeds.length>1?$btndelete.fadeIn():$btndelete.fadeOut()
            $btndelete.prop('search','?id='+allCheckeds) 
          }
          //getdelete($tbodycheckboxs)
        })
    })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
