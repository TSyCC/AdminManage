<?php
 require_once 'inc/function.php';
get_current_userx();
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Comments &laquo; Admin</title>
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
        <h1>所有评论</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action form-inline">
        <select name="status" class="form-control input-sm">
          <option value="all">所有状态</option>
          <option value="approved">已批准</option>
          <option value="held">待审核</option>
          <option value="rejected">已拒绝</option>
        </select>
        <button id="select_Btn" class="btn btn-default btn-sm">筛选</button>
        <!-- show when multiple checked -->
        <div class="btn-batch" style="display: none">
          <button class="btn btn-info btn-sm">批量批准</button>
          <button class="btn btn-warning btn-sm">批量拒绝</button>
          <button class="btn btn-danger btn-sm">批量删除</button>
        </div>
        <ul class="pagination pagination-sm pull-right">
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>作者</th>
            <th>评论</th>
            <th>评论在</th>
            <th>提交于</th>
            <th>状态</th>
            <th class="text-center" width="150">操作</th>
          </tr>
        </thead>
        <tbody>
          
        </tbody>
      </table>
    </div>
  </div>

  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/jsrender/jsrender.js"></script>
  <script src="/static/assets/vendors/twbs-pagination/jquery.twbsPagination.js"></script>
  <script type="text/x-jsrender" id="comments_tmpl">
    {{for comments}}
    <tr{{if status == 'held'}} class="warning"{{else status == 'rejected'}} class="danger"{{/if}} data-id="{{:id}}">
      <td class="text-center"><input type="checkbox"></td>
      <td>{{:author}}</td>
      <td>{{:content}}</td>
      <td>{{:posts_title}}</td>
      <td>{{:created}}</td>
      <td>{{:status}}</td>
      <td class="text-center">
        {{if status == 'held'}}
        <a href="javascript:;" class="btn btn-info btn-xs btn-edit" data-status="approved">批准</a>
        <a href="javascript:;" class="btn btn-warning btn-xs btn-edit" data-status="rejected">拒绝</a>
        <a href="javascript:;" class="btn btn-danger btn-xs delete-btn">删除</a>
        {{else status == 'rejected'}}
        <a href="javascript:;" class="btn btn-danger btn-xs delete-btn">删除</a>
        {{else}}
        <a href="javascript:;" class="btn btn-warning btn-xs btn-edit" data-status="rejected">拒绝</a>
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
      
      $.get('/admin/api/comments-list.php',{ page:page,status:status },function(res){
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
    var html = $('#comments_tmpl').render({comments: res.comments})
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
        $.get('/admin/comments-delete.php', { id: id }, function (res) {
          //console.log(res)
          if (!res) return
          loadPageData(currentPage,currentStatus)
          //res.success && loadData()
        })
    })
    //修改评论状态逻辑
    $tbodyselect.on('click', '.btn-edit', function () {
        var id = parseInt($(this).parent().parent().data('id'))
        var status = $(this).data('status')
        $.post('/admin/comments-status.php?id=' + id, { status: status }, function (res) {
          if (!res) return
          loadPageData(currentPage,currentStatus)
        })
      })

    //批量操作
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
        // 批准
        .on('click', '.btn-info', function () {
          $.post('/admin/comments-status.php?id=' + checkedItems.join(','), { status: 'approved' }, function (res) {
            if (!res) return
          loadPageData(currentPage,currentStatus)
          })
        })
        // 拒绝
        .on('click', '.btn-warning', function () {
          $.post('/admin/comments-status.php?id=' + checkedItems.join(','), { status: 'rejected' }, function (res) {
            if (!res) return
          loadPageData(currentPage,currentStatus)
          })
        })
        // 删除
        .on('click', '.btn-danger', function () {
          $.get('/admin/comments-delete.php', { id: checkedItems.join(',') }, function (res) {
            if (!res) return
          loadPageData(currentPage,currentStatus)
          })
        })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
