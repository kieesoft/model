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

<body class="easyui-layout"   fit="true"   scroll="no" >
<script type="text/javascript">
    var menuTabs = null;
    $(function() {
        _menus = eval('(' + '<?php echo ($menu); ?>' + ')');
        menuTabs = $("#west").cwebsFrame(_menus, "欢迎使用");

        $('#loading-mask').fadeOut(); //关闭遮罩
        //绑定退出事件
        $('#loginOut').click(function() {
            $.messager.confirm('系统提示', '您确定要退出本次登录吗?', function(r) {
                if (r) {
                    location.href = '/Home/Login/logout';
                }
            });
        });
        $('#changePassword').click(function() {
            var option=$('#changePassword').linkbutton('options');
            menuTabs.addTab('修改密码','/Home/Index/changepwd',option['iconCls']);
        });
    });
</script>
<noscript>
    抱歉，请开启脚本支持！
</noscript>
<!-- 正在加载窗口 -->
<div id="loading-mask" >
    <div id="pageloading">
        <img src="/img/loading.gif" align="absmiddle" /> 正在加载中,请稍候...
    </div>
</div>
<!-- 头部 -->
<div id="top" region="north" split="false" border="false" >
    <span style="float:right; padding-right:30px;">
        <a href="#" class="easyui-linkbutton" data-options="plain:true,iconCls:'icon icon-role'"><?php echo ($userinfo); ?></a> |
        <a href="#"  class="easyui-linkbutton" data-options="plain:true,iconCls:'icon icon-shield'"  id="changePassword">修改密码</a>
        <a href="/"  class="easyui-linkbutton" data-options="plain:true,iconCls:'icon icon-home'">返回首页</a>
        <a href="#"  class="easyui-linkbutton" data-options="plain:true,iconCls:'icon icon-exit'" id="loginOut">退出</a>
    </span>
    <span style="padding-left:10px; font-size: 16px; "><img src="/img/logo_min.jpg" /></span>
</div>
<!-- 左侧菜单 -->
<div region="west" split="true"  title="功能导航" style="width:130px;" id="west"></div>
<!-- 初始内容页 -->
<div data-options="region:'center',border:'false'" style="overflow: hidden" scroll="no">
    <div class="easyui-tabs" name="__mainTabs__"  fit="true" border="false" style="overflow: hidden" scroll="no" >
        <div class="welcome">
            欢迎使用通用管理平台
        </div>
    </div>
</div>
<div id="footer" data-options="region:'south',border:false">
    版权所有：宁波城市职业技术学院 @2015    技术支持：方仁富  88221932
</div>
</body>
</html>
 </body>
</html>