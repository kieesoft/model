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
    $(function(){
        $('#dg').datagrid({
            title:'用户列表',idField:'id', striped:'true',pagination:'true',rownumbers:true,singleSelect:true,
            url:'/Admin/User/query',toolbar:'#toolbar',
            columns:[[
            {field:'userid',title:'登录名*',width:100,align:'center',editor:{type:'validatebox',options:{validType:'maxLength[45]',required:true}}},
            {field:'teacherno',title:'教师号*',width:80,align:'center',editor:{type:'validatebox',options:{validType:'equalLength[6]',required:true}}},
            {field:'name',title:'姓名*',width:80,align:'center',editor:{type:'validatebox',options:{validType:'maxLength[40]',required:true}}},
            {field:'sex',title:'性别*',width:50,align:'center',editor:{type:'checkbox',options:{on:'M',off:'F'}},
                formatter:function(val,rec){if(val=='M') return '男'; else return '女';}},
            {field:'role',title:'角色*',width:100,align:'center',editor:{type:'validatebox'}},
            {field:'school',title:'学院*',width:100,align:'center', editor:{type:'combobox', options:{url:'/All/Option/school',valueField:'school',textField:'name',panelWidth:'120',required:true,
                onSelect:function(rec) {
                    var tt=$('#dg');
                    tt.datagrid('getSelected').schoolname=rec.name;
                }}},
            formatter:function(val,rec){
                return rec.schoolname;
            }},
            {field:'lock',title:'锁定*',width:40,align:'center',editor:{type:'checkbox',options:{on:1,off:0}}, formatter:function(val,rec){if(val==1)return '√';}},
            {field:'createdate',title:'创建时间',width:150,align:'center'},
            {field:'lastlogintime',title:'登录时间',width:150,align:'center'},
            {field:'lastloginip',title:'登录IP',width:90,align:'center'}
            ]],
            //标题行右键菜单
            onHeaderContextMenu: function(e, field){
                e.preventDefault();
                if (!cmenu_obj.cmenu)//没有的话创建一个
                    $('#dg').datagrid('createColumnMenu',cmenu_obj);
                cmenu_obj.cmenu.menu('show', {
                    left:e.pageX,
                    top:e.pageY
                });
            },
            //点击单元格时候的事件
            onClickCell:function(index, field){
                var tt=$('#dg');
                tt.datagrid('startEditing',{field:field,index:index});
                current_datagrid=tt;
            },
            //数据行上右键菜单
            onRowContextMenu:function(e,rowindex,row){
                var tt=$('#dg');
                tt.datagrid('endEditing');
                if(tt.datagrid('editIndex')!=undefined) return;
                e.preventDefault();  //该方法将通知 Web 浏览器不要执行与事件关联的默认动作（如果存在这样的动作）
                tt.datagrid('selectRow',rowindex);
                $('#menu').menu('show',{
                    left: e.pageX,
                    top: e.pageY
                });
            },
            onSelect:function(index,row){
                var tt=$('#dg2');
                tt.datagrid('uncheckAll');
                var allRole=tt.datagrid('getRows');
                var roles=row.role.split('');
                for(var i=0;i< roles.length;i++){
                    for(var j=0;j<allRole.length;j++)
                        if(roles[i]==allRole[j].role) {
                            tt.datagrid('checkRow',j);
                            break;
                        }
                }
                tt.datagrid('getPanel').panel('setTitle','"'+row.name+'"拥有以下角色：');
            }
        });
        //分配部分数据表格
        $('#dg2').datagrid({
            title:'分配角色',idField:'role', striped:'true',rownumbers:true,singleSelect:false,
            url:'/Admin/User/role',toolbar:'#toolbar2',
            columns:[[
                {field:'check',title:'选中',checkbox:true},
                {field:'role',title:'角色代码',width:80,align:'center'},
                {field:'name',title:'角色',width:100,align:'center'}
            ]],
            onSelect:function(index,row){
                if(!$('#dg').datagrid('getSelected')){
                    $.messager.alert('错误','请先选中一个用户','error');
                    $('#dg2').datagrid('unselectRow',index);
                    return false;
                }
            },
            onRowContextMenu:function(e,rowindex,row){
                var tt=$('#dg2');
                tt.datagrid('endEditing');
                if(tt.datagrid('editIndex')!=undefined) return;
                e.preventDefault();
                tt.datagrid('selectRow',rowindex);
                $('#menu2').menu('show',{
                    left: e.pageX,
                    top: e.pageY
                });
            }
        });
        //著作部分 绑定新建按钮事件
        $("#insert,#menu_insert").click(function(){
            var tt=$('#dg');

            tt.datagrid('endEditing');
            if(tt.datagrid('editIndex')!=undefined) return;
            tt.datagrid('insertRow',{
                index: 0,
                row: {
                    sex:'M',
                    name:'',
                    teacherno:'',
                    userid:'',
                    school:'01',
                    schoolname:'商贸学院',
                    role:'*',
                    id:0
                }
            });
            tt.datagrid('startEditing',{field:'userid',index:0});
            current_datagrid=tt;
        });
        //取消更改
        $("#cancel,#menu_cancel").click(function(){
            var tt= $('#dg');
            tt.datagrid('rejectChanges');
            current_datagrid=null;
        });
        //绑定点击保存按钮事件
        $("#save,#menu_save").click(function(){
            var tt=$('#dg');
            tt.datagrid('endEditing');
            if(tt.datagrid('editIndex')!=undefined) return;
            //获取更改的数据行内容
            var rows=tt.datagrid('getRows');
            rows=tt.datagrid('getChanges','updated');
            var count=0;
            var effectRow = new Object();
            if(rows.length>0){
                count+=rows.length;
                effectRow["updated"]=$.toJSON(rows);
            }
            //获取删除的行
            rows=tt.datagrid('getChanges','deleted');
            if(rows.length>0){
                count+=rows.length;
                effectRow["deleted"]=$.toJSON(rows);
            }
            //获取添加的行
            rows=tt.datagrid('getChanges','inserted');
            if(rows.length>0){
                count+=rows.length;
                effectRow["inserted"]=$.toJSON(rows);
            }
            if(count<=0) //如果没有任何更新，就退出
                return;
            $.post('/Admin/User/update',effectRow,function(result){
                if (result.status==1){
                    tt.datagrid('acceptChanges');
                    tt.datagrid('reload');
                    $.messager.show({	// show error message
                        title: '成功',
                        msg: result.info
                    });
                } else {
                    $.messager.alert(result.status,result.info,'error');
                }
            },'json');
        });
        //右键菜单删除按钮
        $("#menu_delete,#delete").click(function(){
            tt=$('#dg');
            var row = tt.datagrid('getSelected');
            if (row) {
                $.messager.confirm('确认', '你确定删除此记录么?', function(r){
                    if (r){
                        var rowIndex = tt.datagrid('getRowIndex', row);
                        tt.datagrid('deleteRow', rowIndex);
                    }
                });
            }
            else{
                $.messager.alert('错误','请先选中一条记录','error')
            }
        });

        //绑定点击保存按钮事件
        $("#save2,#menu2_save").click(function(){
            var tt=$('#dg2');
            var checkRows=tt.datagrid('getChecked');
            var roles="";
            for(var i=0;i<checkRows.length;i++)
                roles+=checkRows[i].role;
            var userId=$('#dg').datagrid('getSelected').id;
            $.post('/Admin/User/updaterole',{userid:userId,role:roles},function(result){
                if (result.status==1){
                    tt.datagrid('acceptChanges');
                    tt.datagrid('reload');
                    $.messager.show({	// show error message
                        title: '成功',
                        msg: result.info
                    });
                    $('#dg').datagrid('reload');
                } else {
                    $.messager.alert(result.status,result.info,'error');
                }
            },'json');
        });

        //检索用户
        $("#searchuser").click(function(){
            var tt=$('#dg');
            tt.datagrid('loadData',{total:0,rows:[]});
            tt.datagrid('load', {
                name: $('#name').val(),
                userid:$('#userid').val(),
                school:$('#school').combobox('getValue')
            });
        });
        //右键修改密码
        $("#menu_changepassword,#changepassword").click(function(){
            var row= tt=$('#dg').datagrid('getSelected');
            if(!row){
                $.messager.alert('错误','请先选择一个用户！','error');
                return;
            }
            $.messager.prompt('修改', '请输入新密码:', function(data){
                if (data){
                    $.post('/Admin/User/changepassword',{userid:row.userid,password:data},function(result){
                        if (result.status==1){
                            $.messager.show({	// show error message
                                title: '成功',
                                msg: result.info
                            });
                        } else {
                            $.messager.alert(result.status,result.info,'error');
                        }
                    },'json');
                }
            });
        });
        $('#school').combobox({
            url:'/All/Option/school',
            valueField:'school',
            textField:'name'
        });

    });
</script>
<div class="container" style="width:1250px">
    <div id="menu" class="easyui-menu" style="width:100px;">
        <div id='menu_insert' data-options="iconCls:'icon icon-add'">新增用户</div>
        <div id='menu_save' data-options="iconCls:'icon icon-save'">保存</div>
        <div id='menu_changepassword' data-options="iconCls:'icon icon-shield'">修改密码</div>
        <div class="menu-sep"></div>
        <div id='menu_delete' data-options="iconCls:'icon icon-remove'">删除</div>
        <div id='menu_cancel' data-options="iconCls:'icon icon-cancel'">取消</div>
    </div>
    <div id="menu2" class="easyui-menu" style="width:100px;">
        <div id='menu2_save' data-options="iconCls:'icon icon-save'">保存用户角色</div>
    </div>
    <div id="left" style="width:990px;float:left">
        <div id="toolbar">
            <div>
                <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon icon-add',plain:'true'"  id="insert">新增</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon icon-remove',plain:'true'"  id="delete">删除</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon icon-save',plain:'true'" id="save">保存</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon icon-cancel',plain:'true'" id="cancel">取消</a>
                <label for="userid">登录名：</label><input id="userid" class="easyui-validatebox" size="10" value="%"/>
                <label for="name">姓名：</label><input id="name" class="easyui-validatebox" size="10" value="%"/>
                <label for="school">学院：</label>
                <input id="school">
                <a href="javascript:void(0)" class="easyui-linkbutton"  data-options="iconCls:'icon icon-search',plain:'true'" id="searchuser">检索用户</a>
                <a href="javascript:void(0)" class="easyui-linkbutton"  data-options="iconCls:'icon icon-shield',plain:'true'" id="changepassword">修改密码</a>
            </div>
        </div>
        <table id="dg">
        </table>
    </div>
    <div id="right" style="width:250px;float:left;padding-left:5px;">
        <div id="toolbar2">
            <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon icon-save',plain:'true'" id="save2">保存角色配置</a>
        </div>
        <table id="dg2"></table>
    </div>

    <div class="information" style="clear:both">
        <ol>说明：
            <li>有*标注的为可编辑单元，点击后可以修改内容。</li>
            <li>教师号设置后无法修改</li>
        </ol>
    </div>
</div>
 </body>
</html>