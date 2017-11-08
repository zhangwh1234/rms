<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <title><?php echo (L("welcome")); ?></title>
    <link REL="SHORTCUT ICON" HREF=".__PUBLIC__/Images/lhkc/favicon.ico">
    <link rel="stylesheet" type="text/css" href=".__PUBLIC__/Css/icons.css"/>
    <link rel="stylesheet" type="text/css" href=".__PUBLIC__/Css/themes/default/easyui.css" title="default"/>

    <link rel="stylesheet" type="text/css" href=".__PUBLIC__/Css/style.css" />
    <script type="text/javascript" src=".__PUBLIC__/Js/clipboard.min.js"></script>
    <script type="text/javascript" src=".__PUBLIC__/Js/jquery.min.js"></script>
    <script type="text/javascript" src=".__PUBLIC__/Js/jquery.cookie.js"></script>
    <script type="text/javascript" src=".__PUBLIC__/Js/jquery.json.min.js"></script>
    <script type="text/javascript" src=".__PUBLIC__/Js/jquery.easyui.min.1.4.1.js"></script>

    <script type="text/javascript" src=".__PUBLIC__/Js/easyui/locale/easyui-lang-zh_CN.js"></script>
    <script type="text/javascript" src=".__PUBLIC__/Js/easyui/plugins/jquery.portal.js"></script>
    <script type="text/javascript" src=".__PUBLIC__/Js/jquery.app.js"></script>
    <script type="text/javascript" src=".__PUBLIC__/Js/datagrid-detailview.js"></script>
    <script type="text/javascript" src=".__PUBLIC__/Js/mousetrap.js"></script>

    <script language="JavaScript">
        //指定当前组模块URL地址
        var APP = '__APP__';
        var PUBLIC = '__PUBLIC__';
        var companyRegion = {};
    </script>

    <script>
    var cpe_id; //来电参数
    //定义一个来电地址显示的全局显示对象
    var teladdressObj;
    var teladdressObjId;  //地址的当前ID；缓存用
    var telphoneHeigth = 0;  //定义来电条高度，在indexModel需要计算
</script>
<?php if($TelphoneOn == '开启'): ?><script>
        //来电条高度赋值
        telphoneHeigth =  30;
    </script>
    <!-- 开启来电显示，才启动百度地址帮助 -->
    <!-- <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=jLheOGOPRm4rZtnkii8ZOy7C"></script> -->
    <?php if($TelphoneType == 'yeahdone'): ?>
        <OBJECT ID="YeahDone1" WIDTH=7 HEIGHT=3 CLASSID="CLSID:68E13FA4-E8E5-4719-986B-DE6A6476BF44"
                CODEBASE="FR60.OCX"></OBJECT>
        <SCRIPT LANGUAGE="JavaScript" FOR="window" EVENT="onLoad()">
            //Form加载事件
            if(YeahDone1.OpenCPE()>0)//取连接的设备个数
            {
                cpe_id = YeahDone1.GetPhoneHandle(0);        //取对设备的操作句柄
                YeahDone1.RecordChannels = 1;
                YeahDone1.RecordSample = 8000;
                YeahDone1.RecordBits = 8;

                //指定终端中的设备序列号；12字节长度
                //100605000018:10是,06是月,05是日,00001是生产序列号,8是设备类型号:8/9/A/B
            }
            else
            {
                  alert("找不到指定的设备！rn");
            }
        </SCRIPT>
        <script LANGUAGE="JavaScript" FOR="YeahDone1" EVENT="OnPhoneIncoming(cpe_id, IncomingNum)">
            //在界面上显示号码
            $('#telphoneNumber').val(IncomingNum);
            //查询电话地址
            localStorage.telphoneNumber = IncomingNum;  //缓存来电号码
            //显示来电历史
            $.ajax({
                type: "POST",
                url: "__APP__/Telcustomer/getAddressByPhone/telphone/" + IncomingNum,
                dataType: "json",
                success: function (data) {
                    if (data.teladdress.length == 0) return;
                    teladdressObj = data.teladdress; //缓存来电地址数据
                    teladdressObjId = data.teladdress[0].teladdressid;     //缓存第一个来电的ID
                    var teladdress = new Array();
                    $.each(data.teladdress, function (key, value) {
                        teladdress.push({
                            'id': value.teladdressid,
                            'teladdress': value.company + '  |  ' + value.address
                        });
                    })
                    $('#telphoneAddress').combobox('loadData', teladdress);
                    $("#telphoneAddress").combobox('showPanel');
                    $(document).unbind(".combo").bind("mousedown.combo", function (e) {
                    });  //固定Panle

                    if (data.telhistory) {
                        var telhistoryHtml = '';  //开始组装来电历史记录
                        $.each(data.telhistory, function (key, value) {
                            telhistoryHtml += value.teltime + ' ' + value.teltask + "<br>";
                        })
                        $('#telhistoryWin').html(telhistoryHtml);
                        $('#telhistoryWin').window('open');
                    } else {
                        $('#telhistoryWin').window('close');
                    }
                }
            });
        </script><?php endif; ?>


    
    <?php if($TelphoneType == 'CCLinkServer'): ?><object classid="clsid:4CFBD1C3-7492-4F9D-92BF-4001D006387E" id="CCLink" width="0" height="0"></object>
        <script language="javascript" type="text/javascript">
            var LinkHost = localStorage.cclinkHost;
            var LinkPort = localStorage.cclinkPort;
            var LinkExtCode = localStorage.cclinkExtCode;

            function Link_ConnectServer() {
                if (LinkExtCode != "") {
                    var Err = CCLink.Link_ConnectServer(LinkHost, LinkPort, 1); //建立客户端和服务器端连接
                    if (Err != "") {
                        alert("连接呼叫中心服务器失败!");
                        return;
                    }
                    Err = CCLink.Ext_Assign(LinkExtCode, LinkExtCode); //绑定到分机设备
                    if (Err != "") {
                        alert('绑定分机失败,' + Err);
                        return;
                    }
                    CCLink.Ext_SetExtName(LinkExtCode, '<?php echo ($UserName); ?>');
                    //CCLink.Ext_SetExtWorkNo(Com,Com);
                    CCLink.Ext_CheckInQueue(LinkExtCode); //将分机签入队列
                }
                return;
            }

            function Link_DisconnectServer() {
                CCLink.Link_DisConnectServer(); //断开客户端和服务器端连接
                return;
            }
            //连接CTI服务器
            Link_ConnectServer();
        </script>

        <script LANGUAGE="JavaScript" FOR="CCLink" Event="LinkEvent_OnDisconnected()">//与呼叫中心断开
        alert("与呼叫中心服务器断开!");
        </script>

        <script LANGUAGE="JavaScript" FOR="CCLink"
                Event="ExtEvent_OnDoNotDisturbChange(ExtCode,DoNotDisturb)">//分机免打扰标志改变事件
        if (DoNotDisturb == 0) {
            alert("分机" + ExtCode + "致闲!");
        }
        else {
            alert("分机" + ExtCode + "致忙!");
        }
        </script>

        <script LANGUAGE="JavaScript" FOR="CCLink"
                Event="ExtEvent_OnCallIn(ExtCode,OtherCode,OtherRole,TransferCode,TransferRole,CallData)">//分机呼入事件
        //alert("分机"+ExtCode+"电话呼入:"+OtherCode);
        //在界面上显示号码
        $('#telphoneNumber').val(OtherCode);
        //查询电话地址
        localStorage.telphoneNumber = OtherCode;  //缓存来电号码
        //显示来电历史
        $.ajax({
            type: "POST",
            url: "__APP__/Telcustomer/getAddressByPhone/telphone/" + OtherCode,
            dataType: "json",
            success: function (data) {
                if (data.teladdress.length == 0) {
                    $('#telphoneAddress').combobox('loadData', []);
                    $("#telphoneAddress").combobox('hidePanel');
                }
                teladdressObj = data.teladdress; //缓存来电地址数据
                teladdressObjId = data.teladdress[0].teladdressid;     //缓存第一个来电的ID
                var teladdress = new Array();
                $.each(data.teladdress, function (key, value) {
                    teladdress.push({
                        'id': value.teladdressid,
                        'teladdress': value.company + '  |  ' + value.address
                    });
                })
                $('#telphoneAddress').combobox('loadData', teladdress);
                $("#telphoneAddress").combobox('showPanel');
                $(document).unbind(".combo").bind("mousedown.combo", function (e) {
                });  //固定Panle

                if (data.telhistory) {
                    var telhistoryHtml = '';  //开始组装来电历史记录
                    $.each(data.telhistory, function (key, value) {
                        telhistoryHtml += value.teltime + ' ' + value.teltask + "<br>";
                    })
                    $('#telhistoryWin').html(telhistoryHtml);
                    $('#telhistoryWin').window('open');
                } else {
                    $('#telhistoryWin').window('close');
                }
            }
        });
        </script>

        <script LANGUAGE="JavaScript" FOR="CCLink"
                Event="ExtEvent_OnConnected(ExtCode,OtherCode,OtherRole,TransferCode,TransferRole,CallData)">//分机通话事件
        //alert("分机"+ExtCode+"电话接通:"+OtherCode+"；录音文件："+Ext_GetCallInfo_RecordFile(ExtCode));
        //在界面上显示号码
        </script>

        <script LANGUAGE="JavaScript" FOR="CCLink" Event="ExtEvent_OnDisconnected(ExtCode)">//分机挂机事件
        </script><?php endif; ?>

    
    <?php if($TelphoneType == 'CCLink2008'): ?><script type="text/javascript" src=".__PUBLIC__/Js/CCLink2008.js"></script>
        <script>

            var LinkHost = localStorage.cclinkHost;
            var LinkPort = localStorage.cclinkPort;
            var LinkExtCode = localStorage.cclinkExtCode;

            function Link_ConnectServer() {
                CCLink.Link_ConnectServer(LinkHost, LinkPort, 1); //建立客户端和服务器端连接
            }

            CCLink.LinkEvent_OnConnected = function () {

                var Err = CCLink.Ext_Assign(LinkExtCode, LinkExtCode); //绑定到分机设备
                if (Err != "") {
                    alert('绑定分机失败,' + Err);
                    return;
                }


                CCLink.Ext_CheckInQueue(LinkExtCode); //将分机签入队列
                CCLink.Ext_SetExtName(LinkExtCode, '<?php echo ($UserName); ?>');
                //CCLink.Other_ExtensionToolBar(LinkExtCode, 0xFFFF, 1);
                //CCLink.Ext_SetExtWorkNo(LinkExtCode,'1001');
            }

            function Link_DisconnectServer() {
                CCLink.Link_DisConnectServer(); //断开客户端和服务器端连接
                return;
            }


            //连接CTI服务器
            Link_ConnectServer();

            CCLink.ExtEvent_OnCallIn = function (ExtCode, OtherCode, OtherRole, TransferCode, TransferRole, CallData) {
                $('#telphoneNumber').val(OtherCode);
                //查询电话地址
                localStorage.telphoneNumber = OtherCode;  //缓存来电号码
                //显示来电历史
                $.ajax({
                    type: "POST",
                    url: "__APP__/Telcustomer/getAddressByPhone/telphone/" + OtherCode,
                    dataType: "json",
                    success: function (data) {
                        if (data.teladdress.length == 0) {
                            $('#telphoneAddress').combobox('loadData', []);
                            $('#telphoneAddress').combobox('setValue','');
                            $("#telphoneAddress").combobox('hidePanel');
                            teladdressObj = {};
                            return false;
                        }
                        teladdressObj = data.teladdress; //缓存来电地址数据
                        teladdressObjId = data.teladdress[0].teladdressid;     //缓存第一个来电的ID
                        var teladdress = new Array();
                        $.each(data.teladdress, function (key, value) {
                            teladdress.push({
                                'id': value.teladdressid,
                                'teladdress': value.address
                            });
                        })
                        $('#telphoneAddress').combobox('loadData', teladdress);
                        $('#telphoneAddress').combobox('setValue',data.teladdress[0].address);
                        $("#telphoneAddress").combobox('showPanel');
                        //$(document).unbind(".combo").bind("mousedown.combo", function (e)  });  //固定Panle

                        //来电历史的处理
                        if (data.telhistory) {
                            var telhistoryHtml = new Array();  //开始组装来电历史记录
                            $.each(data.telhistory, function (key, value) {
                                telhistoryHtml.push({
                                    'id':value.telhistoryid,
                                    'telhistory':value.teltime + ' ' + value.teltask
                                })
                            });
                            $('#telphoneHistory').combobox('loadData',telhistoryHtml);
                            $("#telphoneHistory").combobox('showPanel');
                        }
                    }
                });
            }
        </script><?php endif; ?>

    
    <?php if($TelphoneType == 'BJCCLink2019'): ?><script type="text/javascript" src=".__PUBLIC__/Js/BjCCLink2008.js"></script>
        <script>

            var LinkHost = localStorage.cclinkHost;
            var LinkPort = localStorage.cclinkPort;
            var LinkExtCode = localStorage.cclinkExtCode;

            function Link_ConnectServer() {
                CCLink.Link_ConnectServer(LinkHost, LinkPort, 1); //建立客户端和服务器端连接
            }

            CCLink.LinkEvent_OnConnected = function () {

                var Err = CCLink.Ext_Assign(LinkExtCode, LinkExtCode); //绑定到分机设备
                if (Err != "") {
                    alert('绑定分机失败,' + Err);
                    return;
                }


                CCLink.Ext_CheckInQueue(LinkExtCode); //将分机签入队列
                CCLink.Ext_SetExtName(LinkExtCode, '<?php echo ($UserName); ?>');
                //CCLink.Other_ExtensionToolBar(LinkExtCode, 0xFFFF, 1);
                //CCLink.Ext_SetExtWorkNo(LinkExtCode,'1001');
            }

            function Link_DisconnectServer() {
                CCLink.Link_DisConnectServer(); //断开客户端和服务器端连接
                return;
            }


            //连接CTI服务器
            Link_ConnectServer();

            CCLink.ExtEvent_OnCallIn = function (ExtCode, OtherCode, OtherRole, TransferCode, TransferRole, CallData) {
                $('#telphoneNumber').val(OtherCode);
                //查询电话地址
                localStorage.telphoneNumber = OtherCode;  //缓存来电号码
                //显示来电历史
                $.ajax({
                    type: "POST",
                    url: "__APP__/Telcustomer/getAddressByPhone/telphone/" + OtherCode,
                    dataType: "json",
                    success: function (data) {
                        if (data.teladdress.length == 0) {
                            $('#telphoneAddress').combobox('loadData', []);
                            $('#telphoneAddress').combobox('setValue', '');
                            $("#telphoneAddress").combobox('hidePanel');
                            teladdressObj = {};
                            return false;
                        }
                        teladdressObj = data.teladdress; //缓存来电地址数据
                        teladdressObjId = data.teladdress[0].teladdressid;     //缓存第一个来电的ID
                        var teladdress = new Array();
                        $.each(data.teladdress, function (key, value) {
                            teladdress.push({
                                'id': value.teladdressid,
                                'teladdress': value.address
                            });
                        })
                        $('#telphoneAddress').combobox('loadData', teladdress);
                        $('#telphoneAddress').combobox('setValue', data.teladdress[0].address);
                        $("#telphoneAddress").combobox('showPanel');
                        //$(document).unbind(".combo").bind("mousedown.combo", function (e)  });  //固定Panle

                        //来电历史的处理
                        if (data.telhistory) {
                            var telhistoryHtml = new Array();  //开始组装来电历史记录
                            $.each(data.telhistory, function (key, value) {
                                telhistoryHtml.push({
                                    'id': value.telhistoryid,
                                    'telhistory': value.teltime + ' ' + value.teltask
                                })
                            });
                            $('#telphoneHistory').combobox('loadData', telhistoryHtml);
                            $("#telphoneHistory").combobox('showPanel');
                        }
                    }
                });
            }
        </script><?php endif; ?>

    <!--将分公司的地图参数:送餐范围载入内存 -->
    <script type="text/javascript">
        var companyRegion = {};
        $.ajax({
            type: "POST",
            url: "__APP__/CompanyMgr/getRegion",
            dataType: "json",
            success: function (data) {
                companyRegion = data;
            }
        });

    </script><?php endif; ?>



    
<?php if($PrinterOn == '开启'): ?><script type="text/javascript" src=".__PUBLIC__/Js/LodopFuncs.js"></script>
    <object id="LODOP_OB" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" width=0 height=0>
        <embed id="LODOP_EM" type="application/x-print-lodop" width=0 height=0></embed>
    </object>
    <script type="text/javascript">
        var LODOP;
        setTimeout(function(){
            LODOP = getLodop(document.getElementById('LODOP_OB'), document.getElementById('LODOP_EM'));
            //显示所先的打印机名称
            LODOP.SET_LICENSES("北京龙城丽华快餐有限公司", "653625970697469919278901905623", "", "");
        },100);
    </script><?php endif; ?>

    <script type="text/javascript" src=".__PUBLIC__/Js/general.js"></script>
</head>

<body class="easyui-layout" id="main">

<div id="header" data-options="region:'north',href:'__APP__/Header',border:false" style="height:79px;overflow: hidden;">
</div>

<div id="center" data-options="region:'center',border:false" style="overflow: visible;">
    <div id="operation" class="easyui-tabs" data-options="">
        <?php if(is_array($startModule)): foreach($startModule as $key=>$moduleName): ?><div title="<?php echo (L("$moduleName")); ?>" href='__APP__/<?php echo ($moduleName); ?>' data-options="cache:true"></div><?php endforeach; endif; ?>
    </div>
</div>

<div id="footer" data-options="region:'south',href:'__APP__/Footer'" style="height:20px;overflow: hidden;">
</div>

<!-- 公共部分 -->
<div id="globel-dialog-div" class="word-wrap" style="line-height:1.5"></div>
<div id="globel-dialog2-div" class="word-wrap" style="line-height:1.5"></div>
<!-- 特殊情况可能需要弹出第2个弹出层 -->


<div id="telhistoryWin" class="easyui-window"
     data-options="modal:false,closed:true,iconCls:'icon-save',title:'来电历史',collapsible:false,minimizable:false,maximizable:false,border:false"
     style="top:1px;left:1000px;width:500px;height:130px;padding:0px;display: none;">

</div>
<div style="visibility:hidden;" id="map"></div>
<script language="JavaScript">

    function initialize() {

        var mp = new BMap.Map('map');
        mp.centerAndZoom(new BMap.Point(121.491, 31.233), 11);

        //启用滚轮放大缩小
        mp.enableScrollWheelZoom(true);
        //禁用地图拖拽
        mp.disableDragging(true);
        //禁用滚轮放大缩小
        mp.disableScrollWheelZoom(true);
        //启用键盘操作
        mp.enableKeyboard(true);

    }

    function loadScript() {
        var script = document.createElement("script");
        script.src = "http://api.map.baidu.com/api?v=2.0&ak=jLheOGOPRm4rZtnkii8ZOy7C&callback=initialize";
        document.body.appendChild(script);
    }

    window.onload = loadScript;

</script>

<!--
<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=jLheOGOPRm4rZtnkii8ZOy7C"></script>
<script type="text/javascript" src=".__PUBLIC__/Js/GeoUtil.js"></script>
-->


<script type="text/javascript">
    //定义页面基础工作类
    window.IndexIndexModule = {
        dialog: '#globel-dialog-div',
        operationHeight: 0,
        //头部高度
        topHeight: 28,
        //导航菜单高度
        navmenuHeight:50,
        //来电条高度
        telphoneHeight:30,
        //底部高度
        footerHeight: 20,
        //列表菜单高度
        viewMenuHeight: 28,
        //操作区高度
        operationHeight:0,
        //表格区可以显示行数的变量
        gridRowsNumber:0,
        //定义消息对象，可以停止对象
        messageObj: 0,
        sendnameMessageObj:0,

        getOperationHeight: function () {
            var bodyHeight = $(document).height();
            var centerHeight = bodyHeight - this.topHeight - this.navmenuHeight - this.viewMenuHeight - this.footerHeight -40;
            //如果启动来电条需要减去来电条的高度
            centerHeight = centerHeight - telphoneHeigth;
            return centerHeight;
        },

        //初始化
        init: function () {
            this.operationHeight = this.getOperationHeight();
            this.calculateMaxRows();
            //this.messages();
            //this.checkmessages();
        },


        //计算maxrow的数量，并形成session
        calculateMaxRows : function(){
            var operationHeight = this.getOperationHeight();
            var maxRows = parseInt(operationHeight / 33) ;
            this.gridRowsNumber = maxRows;  //保存在全局变量中
            $.cookie('listMaxRows',maxRows);   //cooks缓存
        },

        // 开启一个操作tab页
        openOperateTab: function (url, title) {
            if ($('#operation').tabs('exists', title)) {
                $('#operation').tabs('select', title);
                var tab = $('#operation').tabs('getSelected');  // get selected panel
                $('#operation').tabs('update', {
                    tab: tab,
                    options: {
                        title: title,
                        href:  url  // the new content URL
                    }
                });
            } else {
                $('#operation').tabs('add', {title: title, href: url, closable: true, cache: true});
            }

        },

        // 关闭一个操作tab页
        closeOperateTab: function () {
            // 返回选项卡
            var tab = $('#operation').tabs('getSelected');
            // 返回选项卡的index
            var index = $('#operation').tabs('getTabIndex', tab);
            // 关闭选项卡
            $('#operation').tabs('close', index);

        },

        //更新一个操作tab页
        updateOperateTab: function (url) {
            // 返回选项卡
            var tab = $('#operation').tabs('getSelected');
            // 更新一个选项卡面板
            $('#operation').tabs('update', {
                tab: tab,
                options: {href: url}
            });
        },

        //通用查询功能
        search: function (moduleName, title) {
            var that = this;
            $(that.dialog).dialog({
                title: '查询',
                iconCls: 'icons-application-application_add',
                width: 500,
                height: 140,
                cache: false,
                href: APP + '/' + moduleName + '/searchInput',
                modal: true,
                collapsible: false,
                minimizable: false,
                resizable: false,
                maximizable: false,
                buttons: [{
                    text: '确定',
                    iconCls: 'icons-other-tick',
                    handler: function () {
                        $(that.dialog).find('form').eq(0).form('submit', {
                            onSubmit: function () {
                                var isValid = $(this).form('validate');
                                if (!isValid) return false;
                                var searchText = $(that.dialog).find("form input[name='searchText']").val();
                                var url = APP + '/' + moduleName + '/listview/searchText/' + searchText;
                                IndexIndexModule.updateOperateTab(url);
                                $(that.dialog).dialog('close');
                                return false;
                            }
                        });
                    }
                }, {
                    text: '取消',
                    iconCls: 'icons-arrow-cross',
                    handler: function () {
                        $(that.dialog).dialog('close');
                    }
                }]
            });


        },

        //启动消息群发功能
        messages: function () {
          this.messageObj =  setInterval(function () {
                $.get("<?php echo U('Messages/getMessages');?>", function (data) {
                    if (data) {
                        $.messager.show({
                            title: '消息提示',
                            msg: data,
                            showType: 'show',
                            timeout: 0

                        });
                    }
                }, 'json');

            }, 15000);
        },

        //启动送餐员监测消息功能
        checkmessages: function () {
            this.sendnameMessageObj = setInterval(function () {
                $.get("<?php echo U('CheckSendname/getMessages');?>", function (data) {
                    if (data) {
                        $.messager.show({
                            title: '消息提示',
                            msg: data,
                            showType: 'show',
                            height: 150,
                            timeout:5000,
                            style:{
                                right:'',
                                left:0,
                                top:document.body.scrollTop+document.documentElement.scrollTop
                            }
                        });
                    }
                }, 'json');

            }, 15000);
        },

        //修改用户密码
        changeCode:function(){
            var that = this;
            $(that.dialog).dialog({
                title: '修改用户密码',
                iconCls: 'icons-application-application_add',
                width: 300,
                height: 160,
                cache: false,
                href:APP + '/User/changeCodeView',
                modal: true,
                collapsible: false,
                minimizable: false,
                resizable: false,
                maximizable: false,
                buttons: [{
                    text: '确定',
                    iconCls: 'icons-other-tick',
                    handler: function () {
                        $(that.dialog).find('form').eq(0).form('submit', {
                            onSubmit: function () {
                                var password = $(that.dialog).find("form input[name='firstcodechangecode']").val();
                                var passtwo = $(that.dialog).find("form input[name='secondcodechangecode']").val();
                                var record = $(that.dialog).find("form input[name='recordchangecode']").val();
                                if(password != passtwo){
                                    alert('重复输入的密码需要相同!');
                                    return;
                                }
                                if(password.length < 3){
                                    alert('密码小于3位,请输入密码大于3位的密码!');
                                    return;
                                }
                                var url = APP + '/User/changeCode/password/'+password + '/record/'+record;
                                $.get(url, {}, function (res) {
                                    $.messager.progress('close');
                                    if (!res.status) {
                                        $.app.method.tip('提示信息', res.info, 'error');
                                    } else {
                                        $.app.method.tip('提示信息', res.info, 'info');
                                        that.refresh();
                                    };
                                }, 'json');
                                $(that.dialog).dialog('close');
                                return false;
                            }
                        });
                    }
                }, {
                    text: '取消',
                    iconCls: 'icons-arrow-cross',
                    handler: function () {
                        $(that.dialog).dialog('close');
                    }
                }]
            });
        }


    };

    $(function () {
        IndexIndexModule.init();

        $('#operation').tabs({
            onSelect : function(title,index){
                    //初始化快键键
                     initializeKeyboard();
                    var ActionName = '#'+title_module(title)+'Action';
                    var Action = $(ActionName).val();
                    //如果网页还没有建立，就不执行快捷键脚本
                    if(typeof Action == 'undefined') return;
                    var ActionModule = title_module(title)+Action+'Module';

                    var ObjAction = eval("("+ActionModule+")"); //建立对象变量

                    if(typeof ObjAction === 'object'){
                            //执行快捷键
                            ObjAction.quickKeyboardAction();
                    }

            }
        })


    });



    //定义Tab选择事件继承类
    window.selectTabModule = {
        action : new Array(),
        key : 0,

        //加入选择代码
        addSelectTabFun:function(fun){
            this.action[this.key] = fun;
            this.key += 1;
        },

        //执行代码
        selectTab:function(title,index){
            for(var i=0;i< this.action.length;++i){
                this.action[i](title,index);
            }
        }
    };

    setTimeout(function () {
        var script = document.createElement("script");
        script.src = ".__PUBLIC__/Js/GeoUtil.js";
        document.body.appendChild(script);
    },2000);

</script>

<!--将分公司的地图参数:送餐范围载入内存 -->
<script type="text/javascript">
    $.ajax({
        type: "POST",
        url: "__APP__/CompanyMgr/getRegion",
        dataType: "json",
        success: function (data) {
            companyRegion = data;
        }
    });

</script>