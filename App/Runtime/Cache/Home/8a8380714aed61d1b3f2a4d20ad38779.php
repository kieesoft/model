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
      style="width:250px;height:80px;padding:10px;">数据保存中，请勿刷新页面！
 </div>

<script>
        $(function () {
            //密码框回车提交
            $('#pwd').keydown(function(e){
                if(e.keyCode==13){
                    login_click();
                }
            });
        });
        function login_click(){
            var username=$('#username').val();
            var pwd=$('#pwd').val();
            if(!$("#loginForm").form('validate')||username==''||pwd=='') return;
            pwd=hex_md5(hex_md5(pwd)+'<?php echo ($randguid); ?>');

            $.post('/Home/Login/checklogin',{username:username,pwd:pwd},function(result){
                if (result.status==1){
                    location="/Home/Index/index";
                }
                else if(result.status==2){
                    location="/Home/Index/student";
                }
                else {
                    $.messager.alert("错误",result.info,"error",pwdselect);
                }
            },'json');
        }
        //提示密码错误后选中密码输入框
        function pwdselect()
        {
            var a=$('#pwd');
            a.select();
            a.focus();
        }
    </script>
    <style type="text/css">
        body
        {
            margin: 0;
            padding: 0;
            background: url('/img/logback.png') repeat;
        }
        #head
        {
            margin: 0 auto;
            width: 705px;
            height: 90px;
            margin-top: 20px;
        }
        #content
        {
            position: relative;
            margin: 30px auto;
            width: 760px;
            min-height: 360px;
            height: auto;
            background: url(/img/dl.png) no-repeat;
            background-size: cover;
        }
        #cn_bg
        {
            overflow: hidden;
            display: inline-block;
            margin-top: 100px;
            margin-left: 10px;
        }
        #password
        {
            margin-top: 25px;
        }
        #user_name{
            margin-top: 40px;
            position: relative;
        }
        #password, #user_name
        {
            margin-left: 534px;
        }
        #login
        {
            display: block;
            background: url(/img/loginbtn_bg.gif);
            line-height: 42px;
            width: 112px;
            height: 42px;
            color: #FFF;
            font-family: "微软雅黑";
            text-decoration: none;
            position:absolute;
            right:54px;
            top: 260px;
            text-indent: 25px;
        }
        input
        {
            height: 25px;
            width: 130px;
            line-height: 25px;
            border: 1px solid #999;
        }
        input:focus
        {
            border: 1px solid #294C8E;
        }
        #footer
        {
            width: 810px;
            margin: 0 auto;
            padding-top: 0px;
        }
        .label
        {
            width: 100px;
        }
    </style>
<form action="" method="post" name="loginForm" id="loginForm">
    <div id="head"> </div>
    <div id="content">
        <div id="cn_bg">
            <div id="user_name">
                <label for="username" class="label">
                    账号：</label>
                <input type="text" name="username" id="username" class="easyui-validatebox" data-options="iconCls:'icon icon-shield',validType:'minmaxLength[2,20]'" placeholder="教师号或者学号"/>
            </div>
            <div id="password">
                <label for="pwd" class="label">
                    密码：</label>
                <input type="password" name="pwd" id="pwd" class="easyui-validatebox" data-options="validType:'minLength[6]'" placeholder="输入密码" />
            </div>
            <a id="login" href="javascript:login_click()">登 录</a>
        </div>
    </div>
    <div id="footer">
        <img src="/img/footer_bg.png" width="809" height="73" alt="" /></div>
</form>
</body>
</html>

 </body>
</html>