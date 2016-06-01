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
    var editNode=undefined;
    $(function(){
        $('#dg').treegrid({
            idField:'id',treeField:'description', striped:'true',rownumbers:true,singleSelect:true,
            animate: true,lines:true,
            url:'/Admin/Action/query',toolbar:'#toolbar',
            columns: [[
                {field:'description',title:'描述*',width:300,editor:{type:'validatebox',options:{validType:'maxLength[45]'}}},
                {field:'action',title:'动作*',width:250,editor:{type:'validatebox',options:{validType:'maxLength[45]'}}},
                {field:'shortname',title:'简称*',width:50,align:'center',editor:{type:'validatebox',options:{validType:'maxLength[10]'}}},
                {field:'id',title:'ID值',width:50,align:'center'},
                {field:'image',title:'图标*',width:100, editor:{type:'combobox', options:{url:'/All/Option/icon',valueField:'name',textField:'name',panelWidth:120,panelHeight:150,required:true,
                formatter:function(row){
                    return '<span class="icon '+row.name+'">&nbsp;</span>'+row.name;
                }}},
                    formatter:function(val,rec){
                        return '<span class="icon '+val+'">&nbsp;</span>'+val;
                    }},
                {field:'ismenu',title:'目录*',width:50,align:'center',editor:{type:'checkbox',options:{on:1,off:0}},
                formatter:function(val,rec){
                    if(val==1)
                        return '√';
                }},
                {field:'rank',title:'排序*',width:50,align:'center'}
            ]],
            onClickRow:function(row){
                var tt=$('#dg2');
                if(row.id!=editNode) {
                    $('#dg').treegrid('endEdit', editNode);
                    editNode=undefined;
                }
                $('#dg').treegrid('select',row.id);
                tt.datagrid('loadData',{total:0,rows:[]});
                tt.datagrid('getPanel').panel('setTitle','"'+row.action+'"以下角色拥有权限：');
                tt.datagrid('load',{ id:row.id});
            },
            onContextMenu:function(e,row){
                var tt=$('#dg');
                tt.treegrid('endEdit', editNode);
                tt.treegrid('select',row.id);
                e.preventDefault();
                $('#menu').menu('show',{
                    left: e.pageX,
                    top: e.pageY
                });
            },
            onAfterEdit:function(row,change){
                if($.toJSON(change)=='{}')
                    return;
                var tt=$('#dg');
                tt.treegrid('select',editNode);
                var selected=tt.treegrid('getSelected');
                var node = jQuery.extend({},selected);
                node.children=undefined;
                $.post('/Admin/Action/update',{updated:node},function(result){
                    if (result.status==1){
                        $.messager.show({	// show error message
                            title: '成功',
                            msg: result.info
                        });
                    } else {
                        $.messager.alert(result.status,result.info,'error');
                    }
                },'json');
                var parent=tt.treegrid('getParent',selected.id);
                tt.treegrid('reload',parent.id);
            }
        });
        //分配部分数据表格
        $('#dg2').datagrid({
            title:'角色分配',idField:'id', striped:'true',pagination:'true',rownumbers:true,singleSelect:true,
            url:'/Admin/Action/role',toolbar:'#toolbar2',pageSize:5,pageList:[5,10],
            columns: [[
                {field:'role',title:'代码',width:30,align:'center'},
                {field:'name',title:'角色*',width:100,align:'center', editor:{type:'combobox', options:{url:'/All/Option/role',valueField:'name',textField:'name',panelHeight:0,required:true,
                    onSelect:function(rec){
                        var tt=$('#dg2');
                        tt.datagrid('getSelected').role=rec.role;
                    }
                }}},
                {field:'exe',title:'执*',width:30,align:'center',editor:{type:'checkbox',options:{on:16,off:0}},formatter:function(val,rec){if(val==16) return '√';}},
                {field:'ins',title:'增*',width:30,align:'center',editor:{type:'checkbox',options:{on:8,off:0}},formatter:function(val,rec){if(val==8) return '√';}},
                {field:'del',title:'删*',width:30,align:'center',editor:{type:'checkbox',options:{on:4,off:0}},formatter:function(val,rec){if(val==4)return '√';}},
                {field:'modi',title:'改*',width:30,align:'center',editor:{type:'checkbox',options:{on:2,off:0}},formatter:function(val,rec){if(val==2)return '√';}},
                {field:'que',title:'查*',width:30,align:'center',editor:{type:'checkbox',options:{on:1,off:0}},formatter:function(val,rec){if(val==1)return '√';}}
            ]],
            //点击单元格时候的事件
            onClickCell:function(index,field){
                var tt=$('#dg2');
                tt.datagrid('startEditing',{index:index,field:field});
                current_datagrid=tt;
            },
            //数据行上右键菜单
            onRowContextMenu:function(e,rowindex,row){
                var tt=$('#dg2');
                tt.datagrid('endEditing');
                if(tt.datagrid('editIndex')!=undefined) return;
                e.preventDefault();  //该方法将通知 Web 浏览器不要执行与事件关联的默认动作（如果存在这样的动作）
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
            tt.treegrid('endEdit',editNode);
            var selectedNode=tt.treegrid('getSelected');
            if(selectedNode) {
                tt.treegrid('append', {
                    parent: selectedNode.id,  // the node has a 'id' value that defined through 'idField' property
                    data: [{
                        id: 0,
                        action: '',
                        description: '',
                        parentid: selectedNode.id,
                        ismenu: 0,
                        image: 'icon-blank',
                        'state':'open',
                        rank: 0,
                        _parentId: selectedNode.id
                    }]
                });
            }
            else {
                tt.treegrid('append', {
                    parent:null,
                    data: [{
                        id: 0,
                        action: '',
                        description: '',
                        'state':'open',
                        parentid:1,
                        ismenu: 0,
                        image: 'icon-blank',
                        rank: 0
                    }]
                });
            }
            editNode=0;
            tt.treegrid('select',editNode);
            tt.treegrid('beginEdit', editNode);
            var ed=tt.treegrid('getEditor',{id:editNode,field:'description'});
            try{$(ed.target).select().focus();}catch(exception){}
        });
        //取消更改
        $("#cancel,#menu_cancel").click(function(){
            var tt= $('#dg');
            tt.treegrid('cancelEdit',editNode);
            if(editNode==0)
                tt.treegrid('remove',editNode);
            editNode=undefined;
        });
        $("#edit,#menu_edit").click(function(){
            var tt=$('#dg');
            var row=tt.treegrid('getSelected');
            editNode = row.id;
            tt.treegrid('beginEdit', editNode);
            var ed=tt.treegrid('getEditor',{id:editNode,field:'description'});
            try{$(ed.target).select().focus();}catch(exception){};
        });
        //绑定点击保存按钮事件
        $("#save,#menu_save").click(function(){
            var tt=$('#dg');
            tt.treegrid('endEdit', editNode);
        });
        //右键菜单删除按钮
        $("#menu_delete,#delete").click(function(){
            var tt=$('#dg');
            var row = tt.treegrid('getSelected');
            if(row.state=='closed'||tt.treegrid('getChildren',row.id)!='') {
                $.messager.alert('错误', '该节点有子节点，无法直接删除！', 'error');
                return;
            }
            if (row) {
                $.messager.confirm('确认', '你确定删除此记录么?', function(r){
                    if (r){
                        tt.treegrid('remove',row.id);
                        var node={};
                        node.id=row.id;
                        $.post('/Admin/Action/update',{deleted:node},function(result){
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
            }
            else{
                $.messager.alert('错误','请先选中一条记录','error')
            }
        });
        //详细表部分，绑定事件
        $("#insert2,#menu2_insert").click(function(){
            var tt=$('#dg2');
            var tt2=$('#dg');
            tt.datagrid('endEditing');
            if(tt.datagrid('editIndex')!=undefined) return;
            if(!tt2.treegrid('getSelected'))
                $.messager.alert('错误','请先在左边列表中选中一条记录','error')
            tt.datagrid('insertRow',{
                index: 0,
                row: {
                    actionid:tt2.datagrid('getSelected').id,
                    que:1
                }
            });
            tt.datagrid('startEditing',{index:0,field:'name'});
            current_datagrid=tt;
        });
        //取消更改
        $("#cancel2,#menu2_cancel").click(function(){
            var tt= $('#dg2');
            tt.datagrid('rejectChanges');
            current_datagrid=null;
        });
        //绑定点击保存按钮事件
        $("#save2,#menu2_save").click(function(){
            var tt=$('#dg2');
            tt.datagrid('endEditing');
            if(tt.datagrid('editIndex')!=undefined) return;
            //获取更改的数据行内容
            var rows=tt.datagrid('getChanges','updated');
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

            $.post('/Admin/Action/updaterole',effectRow,function(result){
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
        $("#menu2_delete,#delete2").click(function(){
            tt=$('#dg2');
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
        //右键菜单删除按钮
        $("#search").click(function(){
            var tt=$('#dg');
            tt.treegrid('loadData',{total:0,rows:[]});
            tt.treegrid('load', {
                searchid:$('#id').val(),
                action:$('#action').val(),
                description:$('#description').val()
            });
        });
    });
</script>
<div class="container" style="width:1250px">
    <div id="menu" class="easyui-menu" style="width:100px;">
        <div id='menu_insert' data-options="iconCls:'icon icon-add'">新增</div>
        <div id='menu_edit' data-options="iconCls:'icon icon-pencil'">编辑本条目</div>
        <div id='menu_delete' data-options="iconCls:'icon icon-remove'">删除本条目</div>
        <div id='menu_save' data-options="iconCls:'icon icon-save'">保存分配</div>
        <div id='menu_cancel' data-options="iconCls:'icon icon-cancel'">取消更改</div>
    </div>
    <div id="menu2" class="easyui-menu" style="width:100px;">
        <div id='menu2_insert' data-options="iconCls:'icon icon-add'">新增</div>
        <div id='menu2_delete' data-options="iconCls:'icon icon-remove'">删除此角色</div>
        <div id='menu2_save' data-options="iconCls:'icon icon-save'">保存分配</div>
        <div id='menu2_cancel' data-options="iconCls:'icon icon-cancel'">取消更改</div>
    </div>
    <div id="left" style="width:890px;float:left">
        <div id="toolbar">
            <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon icon-add',plain:'true'"  id="insert">新增</a>
            <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon icon-pencil',plain:'true'"  id="edit">编辑</a>
            <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon icon-remove',plain:'true'"  id="delete">删除</a>
            <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon icon-save',plain:'true'" id="save">保存</a>
            <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon icon-cancel',plain:'true'" id="cancel">取消</a> |
            <label for="id">ID值：</label><input id="id" class="easyui-validatebox" size="5"/>
            <label for="action">动作：</label><input id="action" class="easyui-validatebox" size="12" value="%"/>
            <label for="action">描述：</label><input id="description" class="easyui-validatebox" size="10" value="%"/>
            <a href="javascript:void(0)" class="easyui-linkbutton"  data-options="iconCls:'icon icon-search',plain:'true'" id="search">筛选</a>
        </div>
        <table id="dg"></table>
    </div>
    <div id="right" style="width:350px;float:left;padding-left:5px;">
    <div id="toolbar2">
        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon icon-add',plain:'true'"  id="insert2">新增</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon icon-remove',plain:'true'"  id="delete2">删除</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon icon-save',plain:'true'" id="save2">保存</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon icon-cancel',plain:'true'" id="cancel2">取消</a>
    </div>
    <table id="dg2"></table>
    </div>
    <div class="information" style="clear:both">
        <ol>说明：
            <li>有*标注的为可编辑单元，双击击后可以修改内容。</li>
        </ol>
    </div>
</div>
 </body>
</html>