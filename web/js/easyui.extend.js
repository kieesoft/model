/**
 * Created by fangrenfu@126.com on 13-11-6.
 * easyui 的扩展函数定义
 */

$.extend($.fn.datagrid.defaults,{
    editIndex:undefined,
    editField:undefined
});
//拓展定义了datagrid方法
$.extend($.fn.datagrid.methods, {
    editIndex: function(jq){
        return $.data(jq[0], 'datagrid').editIndex;
    },
    editField: function(jq){
        return $.data(jq[0], 'datagrid').editField;
    },
    nextEditor:function(jq,type){
        return jq.each(function(){
            var fields = $(this).datagrid('getColumnFields', true).concat($(this).datagrid('getColumnFields'));
            var field =$(this).datagrid('editField');
            var index= $(this).datagrid('editIndex');
            var length = $(this).datagrid('getRows').length;
            //如果是要行切换
            if (type == 'col') {
                index++;
            }
            else {
                for (var i = 0; i < fields.length; i++) {
                    if (fields[i] == field) {
                        if (i + 1 < fields.length) {
                            field = fields[i + 1];
                        }
                        else {
                            field = fields[0];
                            index++;
                        }
                        break;
                    }
                }
            }
            if (index >= length)
                $(this).datagrid('endEditing');
            else {
                var col = $(this).datagrid('getColumnOption', field);
                if (col.editor == null)
                    $(this).datagrid('nextEditor',type);
                else {
                    $(this).datagrid('startEditing', {index:index,field:field});
                }
            }
        });
    },
    //editCell方法
    editCell: function(jq,obj){
        return jq.each(function(){
            var opts = $(this).datagrid('options');
            //获取所有冻结的与非冻结的字段
            var fields = $(this).datagrid('getColumnFields',true).concat($(this).datagrid('getColumnFields'));

            for(var i=0; i<fields.length; i++){
                var col = $(this).datagrid('getColumnOption', fields[i]);
               //临时保存起来，就剩下当前点的这个
                col.editor1 = col.editor;
                if (fields[i] != obj.field){
                    col.editor = null;
                }
            }
            //开始编辑行，由于其它的都已经被放成null，只会开始编辑当前这个。
            $(this).datagrid('beginEdit', obj.index);
            //再把editor恢复回去。
            for(var i=0; i<fields.length; i++){
                var col = $(this).datagrid('getColumnOption', fields[i]);
                col.editor = col.editor1;
            }
            $.data(this, 'datagrid').editIndex=obj.index;
            $.data(this, 'datagrid').editField=obj.field;
        });
    },
    //startEditing方法，进入编辑状态
    startEditing:function(jq,obj){
        return jq.each(function(){
            $(this).datagrid('endEditing');
            var editIndex=  $(this).datagrid('editIndex');
            if (editIndex==undefined){
                $(this).datagrid('selectRow', obj.index)
                    .datagrid('editCell',{index:obj.index,field:obj.field});
                var ed = $(this).datagrid('getEditor', {index:obj.index,field:obj.field});
                try{$(ed.target).select().focus();}catch(exception){}
            }
        });
    },
    endEditing:function(jq){
        return jq.each(function(){
            var editIndex=$.data(this, 'datagrid').editIndex;
            var editField=$.data(this, 'datagrid').editField;
            if ( editIndex== undefined){return}
            var fields=$(this).datagrid('getColumnFields', true).concat($(this).datagrid('getColumnFields'));
            for(var i=0; i<fields.length; i++) {
                var field= fields[i];
                $(this).datagrid('selectRow',editIndex).datagrid('editCell', {index:editIndex, field:field});
                var ed = $(this).datagrid('getEditor', {index:editIndex,field:field});
                if (!$(this).datagrid('validateRow', editIndex)) {
                    try{$(ed.target).select().focus();}catch(exception){}
                    return;
                }
                else{
                    $(this).datagrid('endEdit',editIndex);
                }
            }
            $.data(this, 'datagrid').editIndex = undefined;
            $.data(this, 'datagrid').editField=undefined;
        });
    },
    //创建表头菜单，使用对象的cmenu
    createColumnMenu:function(jq,cmenu_obj){
        return jq.each(function(){ //自定义的时候必须将jq.each用上，其实我也不知道为什么
            cmenu_obj.cmenu = $('<div/>').appendTo('body');
            tt=$(this);//定义一个，在onclick事件中用
            cmenu_obj.cmenu.menu({
                onClick: function(item){
                    if (item.iconCls == 'icon-ok'){
                        tt.datagrid('hideColumn', item.name);
                        cmenu_obj.cmenu.menu('setIcon', {
                            target: item.target,
                            iconCls: 'icon-empty'
                        });
                    } else {
                        tt.datagrid('showColumn', item.name);
                        cmenu_obj.cmenu.menu('setIcon', {
                            target: item.target,
                            iconCls: 'icon-ok'
                        });
                    }
                }
            });
            var fields = $(this).datagrid('getColumnFields');
            for(var i=0; i<fields.length; i++){
                var field = fields[i];
                var col = $(this).datagrid('getColumnOption', field);
                cmenu_obj.cmenu.menu('appendItem', {
                    text: col.title,
                    name: field,
                    iconCls: 'icon-ok'
                });
            }
        });
    }
});

function endEditingTree(tt,obj){
    if (obj.editIndex == undefined){return true}
    if (tt.treegrid('validateRow',obj.editIndex)){
        tt.treegrid('endEdit', obj.editIndex);
        obj.editIndex = undefined;
        return true;
    } else {
        return false;
    }
}
//拓展定义了treegrid
$.extend($.fn.treegrid.methods, {
    editIndex: function(jq){
        return $.data(jq[0], 'treegrid').editIndex;
    },
    editField: function(jq){
        return $.data(jq[0], 'treegrid').editField;
    },
    nextEditor:function(jq,type){
        return jq.each(function(){
            var fields = $(this).treegrid('getColumnFields', true).concat($(this).treegrid('getColumnFields'));
            var field =$(this).treegrid('editField');
            var index= $(this).treegrid('editIndex');
            var length = $(this).treegrid('getRows').length;
            //如果是要行切换
            if (type == 'col') {
                index++;
            }
            else {
                for (var i = 0; i < fields.length; i++) {
                    if (fields[i] == field) {
                        if (i + 1 < fields.length) {
                            field = fields[i + 1];
                        }
                        else {
                            field = fields[0];
                            index++;
                        }
                        break;
                    }
                }
            }
            if (index >= length)
                $(this).treegrid('endEditing');
            else {
                var col = $(this).treegrid('getColumnOption', field);
                if (col.editor == null)
                    $(this).treegrid('nextEditor',type);
                else {
                    $(this).treegrid('startEditing', {index:index,field:field});
                }
            }
        });
    },
    //editCell方法
    editCell: function(jq,obj){
        return jq.each(function(){
            var opts = $(this).treegrid('options');
            //获取所有冻结的与非冻结的字段
            var fields = $(this).treegrid('getColumnFields',true).concat($(this).treegrid('getColumnFields'));

            for(var i=0; i<fields.length; i++){
                var col = $(this).treegrid('getColumnOption', fields[i]);
                //临时保存起来，就剩下当前点的这个
                col.editor1 = col.editor;
                if (fields[i] != obj.field){
                    col.editor = null;
                }
            }
            //开始编辑行，由于其它的都已经被放成null，只会开始编辑当前这个。
            $(this).treegrid('beginEdit', obj.index);
            //再把editor恢复回去。
            for(var i=0; i<fields.length; i++){
                var col = $(this).treegrid('getColumnOption', fields[i]);
                col.editor = col.editor1;
            }
            $.data(this, 'treegrid').editIndex=obj.index;
            $.data(this, 'treegrid').editField=obj.field;
        });
    },
    //startEditing方法，进入编辑状态
    startEditing:function(jq,obj){
        return jq.each(function(){
            $(this).treegrid('endEditing');
            var editIndex=  $(this).treegrid('editIndex');
            if (editIndex==undefined){
                $(this).treegrid('selectRow', obj.index)
                    .treegrid('editCell',{index:obj.index,field:obj.field});
                var ed = $(this).treegrid('getEditor', {index:obj.index,field:obj.field});
                try{$(ed.target).select().focus();}catch(exception){}
            }
        });
    },
    //endEditing方法，结束编辑状态

    endEditing:function(jq){
        return jq.each(function(){
            var editIndex=$.data(this, 'treegrid').editIndex;
            var editField=$.data(this, 'treegrid').editField;
            if ( editIndex== undefined){return}
            var fields=$(this).treegrid('getColumnFields', true).concat($(this).treegrid('getColumnFields'));
            for(var i=0; i<fields.length; i++) {
                var field= fields[i];
                $(this).treegrid('selectRow',editIndex).treegrid('editCell', {index:editIndex, field:field});
                var ed = $(this).treegrid('getEditor', {index:editIndex,field:field});
                if (!$(this).treegrid('validateRow', editIndex)) {
                    try{$(ed.target).select().focus();}catch(exception){}
                    return;
                }
                else{
                    $(this).treegrid('endEdit',editIndex);
                }
            }
            $.data(this, 'treegrid').editIndex = undefined;
            $.data(this, 'treegrid').editField=undefined;
        });
    },
    //创建表头菜单，使用对象的cmenu
    createColumnMenu:function(jq,cmenu_obj){
        return jq.each(function(){ //自定义的时候必须将jq.each用上，其实我也不知道为什么
            cmenu_obj.cmenu = $('<div/>').appendTo('body');
            tt=$(this);//定义一个，在onclick事件中用
            cmenu_obj.cmenu.menu({
                onClick: function(item){
                    if (item.iconCls == 'icon-ok'){
                        tt.treegrid('hideColumn', item.name);
                        cmenu_obj.cmenu.menu('setIcon', {
                            target: item.target,
                            iconCls: 'icon-empty'
                        });
                    } else {
                        tt.treegrid('showColumn', item.name);
                        cmenu_obj.cmenu.menu('setIcon', {
                            target: item.target,
                            iconCls: 'icon-ok'
                        });
                    }
                }
            });
            var fields = $(this).treegrid('getColumnFields');
            for(var i=0; i<fields.length; i++){
                var field = fields[i];
                var col = $(this).treegrid('getColumnOption', field);
                cmenu_obj.cmenu.menu('appendItem', {
                    text: col.title,
                    name: field,
                    iconCls: 'icon-ok'
                });
            }
        });
    }
});

/**
 * linkbutton方法扩展
 * @param {Object} jq
 */
$.extend($.fn.linkbutton.methods, {
    /**
     * 激活选项（覆盖重写）
     * @param {Object} jq
     */
    enable: function(jq){
        return jq.each(function(){
            var state = $.data(this, 'linkbutton');
            if ($(this).hasClass('l-btn-disabled')) {
                var itemData = state._eventsStore;
                //恢复超链接
                if (itemData.href) {
                    $(this).attr("href", itemData.href);
                }
                //回复点击事件
                if (itemData.onclicks) {
                    for (var j = 0; j < itemData.onclicks.length; j++) {
                        $(this).bind('click', itemData.onclicks[j]);
                    }
                }
                //设置target为null，清空存储的事件处理程序
                itemData.target = null;
                itemData.onclicks = [];
                $(this).removeClass('l-btn-disabled');
            }
        });
    },
    /**
     * 禁用选项（覆盖重写）
     * @param {Object} jq
     */
    disable: function(jq){
        return jq.each(function(){
            var state = $.data(this, 'linkbutton');

            if (!state._eventsStore)
                state._eventsStore = {};
            if (!$(this).hasClass('l-btn-disabled')) {
                var eventsStore = {};
                eventsStore.target = this;
                eventsStore.onclicks = [];
                //处理超链接
                var strHref = $(this).attr("href");
                if (strHref) {
                    eventsStore.href = strHref;
                    $(this).attr("href", "javascript:void(0)");
                }
                //处理直接耦合绑定到onclick属性上的事件
                var onclickStr = $(this).attr("onclick");
                if (onclickStr && onclickStr != "") {
                    eventsStore.onclicks[eventsStore.onclicks.length] = new Function(onclickStr);
                    $(this).attr("onclick", "");
                }
                //处理使用jquery绑定的事件
                var eventDatas = $(this).data("events") || $._data(this, 'events');
                if (eventDatas["click"]) {
                    var eventData = eventDatas["click"];
                    for (var i = 0; i < eventData.length; i++) {
                        if (eventData[i].namespace != "menu") {
                            eventsStore.onclicks[eventsStore.onclicks.length] = eventData[i]["handler"];
                            $(this).unbind('click', eventData[i]["handler"]);
                            i--;
                        }
                    }
                }
                state._eventsStore = eventsStore;
                $(this).addClass('l-btn-disabled');
            }
        });
    }
});
//拓展定义datagrid的editors控件
