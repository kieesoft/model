<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>宁波城市学院教务管理系统</title>
    <link href="/widget/easyui-1.4.4/themes/default/easyui.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="/widget/easyui-1.4.4/themes/icon.css" />
    <script type="text/javascript" src="/widget/easyui-1.4.4/jquery.min.js"></script>
    <script type="text/javascript" src="/widget/easyui-1.4.4/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="/widget/easyui-1.4.4/locale/easyui-lang-zh_CN.js"></script>
    <link rel="stylesheet" type="text/css" href="/css/common.css" />
    <script type="text/javascript" src="/js/common.js"></script>
    <script type="text/javascript" src="/js/easyui.extend.js"></script>
    <script type="text/javascript" src="/js/easyui.validate.js"></script>
    <script type="text/javascript" src="/js/jquery.json.js"></script>
    <script type="text/javascript" src="/js/md5.js"></script>
    <script type="text/javascript">
        var current_datagrid=null;
        var current_treegrid=null;
        var cmenu_obj=new Object();
        cmenu_obj.cmenu=""; //标题行右键菜单
        $(function() {
            $(document).keydown(function (event) {
                if (current_datagrid != null) { //如果正在编辑
                    if (event.which == 9) {
                        event.preventDefault();
                        current_datagrid.datagrid('nextEditor');
                    }
                    else if (event.which == 13) {
                        event.preventDefault();
                        current_datagrid.datagrid('nextEditor', 'col');
                    }
                }
                if (current_treegrid != null) { //如果正在编辑
                    if (event.which == 9) {
                        event.preventDefault();
                        current_treegrid.treegrid('nextEditor');
                    }
                    else if (event.which == 13) {
                        event.preventDefault();
                        current_treegrid.datagrid('treeEditor', 'col');
                    }
                }
            });
        });
    </script>
</head>
 <body>
 <div id="w" class="easyui-window" title="请稍候..."
      data-options="modal:true,closed:true,closable:false,minimizable:false,maximizable:false,iconCls:'icon-save'"
      style="width:250px;height:80px;padding:10px;">数据操作中，请勿刷新页面！
 </div>

<script type="text/javascript">
    $(function() {
        $('#dg').datagrid({
            title:'操作日志',idField:'logid', striped:'true',pagination:'true',rownumbers:true,singleSelect:true,url:'/Admin/Log/query',toolbar:'#toolbar',pageSize:20,
            columns:[[
                {field:'userid',title:'账号'},
                {field:'name',title:'姓名'},
                {field:'role',title:'角色'},
                {field:'requesttime',title:'访问时间'},
                {field:'remoteip',title:'访问IP'},
                {field:'action',title:'执行操作'},
                {field:'success',title:'成功'},
                {field:'data',title:'提交数据'},
            ]]
        });
    });
    function destroy(){
       $.messager.confirm('确认','你确定要清空日志么？',function(r){
          if (r){
              $.post('/Admin/Log/delete',function(result){
                  if (result.status==1){
                       $('#dg').datagrid('reload');	// reload the user data
                       $.messager.show({	// show error message
                          title: '成功',
                          msg: result.info
                       });
                  }
                  else {
                      $.messager.alert(result.status,result.info,'error');
                   }
               },'json');
          }
       });
    }
    function search(){
        var tt=$('#dg');
        tt.datagrid('loadData',{total:0,rows:[]});
        tt.datagrid('load', {
            start: $("#searchstart").datebox("getValue"),
            end: $("#searchend").datebox("getValue"),
            userid:$('#searchid').val(),
            action:$('#searchaction').val(),
            success:$('#success').combobox('getValue')
        });
    }
</script>

<div class="container">
    <div id="toolbar">
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon icon-remove" plain="true" onclick="destroy()">清空日志</a> |
        从 <input id="searchstart" class="easyui-datetimebox" size="18"/> 到
        <input id="searchend" class="easyui-datetimebox" size="18"/>
        帐号:<input id="searchid" class="easyui-validatebox" onkeydown="if(event.keyCode==13) search();" size='8' value="%"/>
        动作：<input id="searchaction" class="easyui-validatebox" onkeydown="if(event.keyCode==13) search();" size="20" value="%"/>
        <label for="success">结果：</label>
        <select class="easyui-combobox" id="success" data-options="panelHeight:80" >
            <option value="">全部</option>
            <option selected="selected" value="0">失败</option>
            <option value="1">成功</option>
        </select>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon icon-search" plain="true" onclick="search()">筛选日志</a>
    </div>
    <table id="dg"></table>
</div>
 </body>
</html>