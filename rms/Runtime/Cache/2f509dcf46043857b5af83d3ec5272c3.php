<?php if (!defined('THINK_PATH')) exit();?><style type="text/css">
    #sendnamelocationwatch{
        width: 100%;
        margin-top: 0px;
        padding: 0;
        font-size: 0;
        border: 1px solid black;
        clear: both;
    }

</style>
<div class="moduleMenu" id="listviewMenu" >
    <ul>
        <li><?php echo (L("$navName")); ?></li>
        <li><a href="javascript:void(0);"  onclick="IndexIndexModule.updateOperateTab('__URL__/listview/delsession/del');">&nbsp;&gt;<?php echo (L("$moduleName")); ?></a></li>
        <li>&nbsp;&gt;<a href="javascript:void(0);"  onclick="IndexIndexModule.updateOperateTab('__URL__/listview/delsession/del');">列表操作</a></li>
        <li style="width: 50px;">&nbsp;</li>

        <li style="float: right;margin-right: 60px;"><a href="javascript:void(0);"   onclick="IndexIndexModule.closeOperateTab();" >关闭</a></li>
        <li style="float:right;"><a href="javascript:void(0);" onclick="IndexIndexModule.closeOperateTab();sendnamemap=null;"><img src=".__PUBLIC__/Images/closeBtn.png" alt="" title="" border="0" height="25" style="margin-bottom: -10px;"></a></li>
    </ul>
</div>
<div id="sendnamelocationwatch">
</div>


<script type="text/javascript">

    //建立地图模型
    var sendnamemap = new BMap.Map("sendnamelocationwatch");

    var sendmanPosition = new Array();
    //显示送餐员的图，和坐标位置
    var sendmanIcon = new BMap.Icon(".__PUBLIC__/Images/lhkc/icon/sendman.png", new BMap.Size(28, 28));
    var label = '';
    var point;

    var areaCompany = new Array();
    //系统初始化
    $(function(){
        //设置div的高度
        $('#sendnamelocationwatch').height(IndexIndexModule.operationHeight);

        //初始化地图
        $.ajax({
            type: "GET",
            url: "__URL__/getCompany",
            dataType: "json",
            success: function(data){
                var point = new BMap.Point(data.longitude,data.latitude);
                sendnamemap.enableScrollWheelZoom(true);
                sendnamemap.enableContinuousZoom();  //启用连续缩放
                sendnamemap.addControl(new BMap.NavigationControl());
                sendnamemap.addControl(new BMap.ScaleControl());
                sendnamemap.addControl(new BMap.OverviewMapControl());
                var opts = {offset: new BMap.Size(0, 50)};  //调整位置
                sendnamemap.addControl(new BMap.MapTypeControl(opts));
                sendnamemap.centerAndZoom(point, 15); // 编写自定义函数，创建标注

                //设置分公司的配送范围
                var region = data.region;
                //拆成数组
                var regionArray = region.split(',');

                var reg = new Array();
                for(i=0;i<regionArray.length;i++){
                    reg.push(new BMap.Point(regionArray[i],regionArray[i+1]));
                    i = i + 1;
                }
                reg.push(new BMap.Point(regionArray[0],regionArray[1]));

                //画出送餐范围
                var polygon = new BMap.Polygon([],
                        {strokeColor: 'red', strokeWeight: 1, strokeOpacity: 1, FillOpacity: 0.5}
                );
                polygon.setPath(reg);
                polygon.setFillColor('blue');  //填充的颜色
                polygon.setFillOpacity(0.09);   //填充的透明度
                sendnamemap.addOverlay(polygon);

                var point = new BMap.Point(data.longitude, data.latitude);
                areaCompany[data.id] = new BMap.Marker(point);
                sendnamemap.addOverlay(areaCompany[data.id]);
                //设置位置
                areaCompany[data.id].setPosition(point);

                //显示分公司的图，和坐标位置 
                var companyIcon = new BMap.Icon(".__PUBLIC__/Images/lhkc/icon/company_white.png", new BMap.Size(38,38));
                var marker = new BMap.Marker(point,{icon:companyIcon}); // 创建标注
                sendnamemap.addOverlay(marker); // 将标注添加到地图中
                //更新图示信息
                areaCompany[data.id].setIcon(companyIcon);

                var label = new BMap.Label(data.name, {offset: new BMap.Size(20, -16)});
                areaCompany[data.id].setLabel(label);

                //添加标题
                addSystemTitle();

                //moveSendname();
            }
        });



        setInterval(moveSendname, 3000);
    })


    //获取送餐员的位置信息，并且显示送餐员的送餐情况
    function moveSendname() {
        $.ajax({
            type:"GET",
            url:"__URL__/getSendname",
            dataType:"json",
            success:function(data){
                $.each(data,function(key,sendname){
                    //动态显示送餐员的情况
                    if(typeof(sendmanPosition[sendname.id]) ==  'object'){
                        //获取label
                        label = sendmanPosition[sendname.id].getLabel();
                        if(sendname.nocomplete_order == 0){
                            sendname.nocomplete_order = '';
                        }else{
                            sendname.nocomplete_order =  "<span id='sendname_nocomplete_order' style='color: red'>"+sendname.nocomplete_order + "</span>"
                        }
                        if(sendname.noaccept_order == 0){
                            sendname.noaccept_order = '';
                        }else{
                            sendname.noaccept_order = "<span id='sendname_noaccept_order' style='color: black'>"+'/' + sendname.noaccept_order+"</span>";
                        }
                        //设置lable +sendname.nocomplete_order+sendname.noaccept_order
                        label.setContent(sendname.name);
                        //坐标，并设置坐标
                        point = new BMap.Point(sendname.longitude,sendname.latitude);
                        sendmanPosition[sendname.id].setPosition(point);
                    }else{
                        //初始化MARKER，并设置坐标
                        var point = new BMap.Point(sendname.longitude,sendname.latitude);
                        sendmanPosition[sendname.id] =  new BMap.Marker(point);
                        sendnamemap.addOverlay(sendmanPosition[sendname.id])
                        sendmanPosition[sendname.id].setIcon(sendmanIcon);  //改变图示
                        //计算label
                        if(sendname.nocomplete_order == 0){
                            sendname.nocomplete_order = '';
                        }else{
                            sendname.nocomplete_order =  "<span id='sendname_nocomplete_order' style='color: red'>"+sendname.nocomplete_order + "</span>"
                        }
                        if(sendname.noaccept_order == 0){
                            sendname.noaccept_order = '';
                        }else{
                            sendname.noaccept_order = "<span id='sendname_noaccept_order' style='color: black'>"+'/' + sendname.noaccept_order+"</span>";
                        }
                        //加入lable +sendname.nocomplete_order+sendname.noaccept_order
                        label = new BMap.Label(sendname.name, {offset: new BMap.Size(18, -16)});
                        sendmanPosition[sendname.id].setLabel(label);
                        //加入双击事件
                        sendmanPosition[sendname.id].addEventListener("dblclick", function(event){
                            openSendmanInofDiglog(sendname.id);
                        });

                    }

                });
            }
        });
    }

    //返回地区分公司的坐标和情况
    function getAreaCompany() {
        $.ajax({
            type: "GET",
            url: "__URL__/getCompany",
            dataType: "json",
            success: function (data) {

                $.each(data, function (key, company) {
                    //动态显示送餐员的情况
                    if (typeof(areaCompany[company.id]) == 'object') {
                        //获取label
                        label = areaCompany[company.id].getLabel();
                        if(company.total_order == 0){
                            company.total_order = '';
                        }else{
                            company.total_order =  "<span id='company_total_order' style='color: blue'>"+company.total_order + "</span>"
                        }
                        if(company.nodispatch_order == 0){
                            company.nodispatch_order = '';
                        }else{
                            company.nodispatch_order =  "<span id='company_nodispatch_order' style='color: red'>"+company.nodispatch_order + "</span>"
                        }
                        if(company.nocomplete_order == 0){
                            company.nocomplete_order = '';
                        }else{
                            company.nocomplete_order = "<span id='company_nocomplete_order' style='color: black'>"+ "/" + company.nocomplete_order+"</span>";
                        }
                        //设置lable
                        label.setContent(company.total_order+''+ company.name+company.nodispatch_order + company.nocomplete_order);

                    } else {
                        var point = new BMap.Point(company.longitude, company.latitude);
                        areaCompany[company.id] = new BMap.Marker(point);
                        map.addOverlay(areaCompany[company.id]);

                        //设置位置
                        areaCompany[company.id].setPosition(point);

                        //显示分公司的图，和坐标位置 
                        var companyIcon = new BMap.Icon("statics/images/icon/company_white.png", new BMap.Size(38,38));
                        var marker = new BMap.Marker(point,{icon:companyIcon}); // 创建标注
                        map.addOverlay(marker); // 将标注添加到地图中

                        //更新图示信息
                        areaCompany[company.id].setIcon(companyIcon);

                        //获取label
                        label = areaCompany[company.id].getLabel();
                        if(company.nodispatch_order == 0){
                            company.nodispatch_order = '';
                        }else{
                            company.nodispatch_order =  "<span id='company_nodispatch_order' style='color: red'>"+company.nodispatch_order + "</span>"
                        }
                        if(company.nocomplete_order == 0){
                            company.nocomplete_order = '';
                        }else{
                            company.nocomplete_order = "<span id='company_nocomplete_order' style='color: black'>"+'/' + company.nocomplete_order+"</span>";
                        }
                        var label = new BMap.Label(company.name +  company.nodispatch_order  + company.nocomplete_order , {offset: new BMap.Size(20, -16)});
                        areaCompany[company.id].setLabel(label);
                    }



                });
            }
        });
    }

    //getAreaCompany();
    //getAreaCompany();
    //setInterval(getAreaCompany, 3000);


    //开启对话框
    function openSendmanInofDiglog(sendname_id){
        url = "<?php echo U('Portal/Index/sendmanInfo');?>";
        url = url + "&sendnameid="+sendname_id;
        $('#globel-dialog-div').dialog({
            title: '查询',
            iconCls: 'icons-application-application_add',
            width: 700,
            height: 400,
            cache: false,
            href: url,
            modal: true,
            collapsible: false,
            minimizable: false,
            resizable: false,
            maximizable: false,
            buttons: [{
                text: '关闭',
                iconCls: 'icons-arrow-cross',
                handler: function () {
                    $('#globel-dialog-div').dialog('close');
                }
            }]
        });

    }

    //获取配送城市的视野，方便查看
    function getCompanyBoundary() {
        $.ajax({
            type: "GET",
            url: "__URL__/getCompany",
            dataType: "json",
            success: function (data) {
                //设置城市的视野范围
                var region = data.region;
                //拆成数组
                var regionArray = region.split(',');

                var reg = new Array();
                for (i = 0; i < regionArray.length; i++) {
                    reg.push(new BMap.Point(regionArray[i], regionArray[i + 1]));
                    i = i + 1;
                }
                reg.push(new BMap.Point(regionArray[0], regionArray[1]));
                color = 'white';
                //画出送餐范围
                var polygon = new BMap.Polygon([],
                        {strokeColor: color, strokeWeight: 1, strokeOpacity: 1, FillOpacity: 0.5}
                );
                polygon.setPath(reg);
                polygon.setFillColor(color);  //填充的颜色
                polygon.setFillOpacity(0.3);   //填充的透明度
                //map.addOverlay(polygon);
                sendnamemap.setViewport(reg);    //调整视野

            }
        });

    }

    //开始设置城市视野
    setTimeout(function () {
        getCompanyBoundary();
    }, 200);



    //程序标题
    function addSystemTitle(){
        // 定义一个控件类,即function
        function TitleControl(){
            // 默认停靠位置和偏移量
            this.defaultAnchor = BMAP_ANCHOR_TOP_LEFT;
            this.defaultOffset = new BMap.Size(580, 10);
        }
        // 通过JavaScript的prototype属性继承于BMap.Control
        TitleControl.prototype = new BMap.Control(opts);

        // 自定义控件必须实现自己的initialize方法,并且将控件的DOM元素返回
        // 在本方法中创建个div元素作为控件的容器,并将其添加到地图容器中
        TitleControl.prototype.initialize = function(map){
            // 创建一个DOM元素
            var div = document.createElement("div");
            // 添加文字说明
            div.appendChild(document.createTextNode("<?php echo ($company); ?>分公司配送区域图"));
            // 设置样式
            div.style.cursor = "pointer";
            div.style.border = "1px solid gray";
            div.style.backgroundColor = "white";
            div.style = "ba(0, 0, 0, 0.35); border-left: 1px solid rgb(139, 164, 220); border-top: 1px solid rgb(139, 164, 220); border-bottom: 1px solid rgb(139, 164, 220); background: rgb(142, 168, 224) none repeat scroll 0% 0%; padding: 2px 6px; font: bold 12px/1.3em arial,sans-serif; text-align: center; white-space: nowrap; border-radius: 3px 3px 3px 3px; color: #FFF68F;";
            //div.style ="-moz-user-select: none; box-shadow: 2px 2px 3px rgba(0, 0, 0, 0.35); border-left: 1px solid rgb(139, 164, 220); border-top: 1px solid rgb(139, 164, 220); border-bottom: 1px solid rgb(139, 164, 220); background: rgb(255, 255, 255) none repeat scroll 0% 0%; padding: 2px 6px; font-family: arial,sans-serif; font-style: normal; font-size: 12px; line-height: 1.3em; font-size-adjust: none; font-stretch: normal; font-feature-settings: normal; font-language-override: normal; font-kerning: auto; font-synthesis: weight style; font-variant: normal; text-align: center; white-space: nowrap; color: rgb(0, 0, 0);cursor:pointer";
            // 绑定事件,点击-moz-user-select: none; box-shadow: 2px 2px 3px rg一次放大两级
            div.onclick = function(e){
            }
            // 添加DOM元素到地图中
            map.getContainer().appendChild(div);
            // 将DOM元素返回
            return div;
        }

        var opts = {offset: new BMap.Size(0, 50)};  //调整位置
        // 创建控件
        var myTitleCtrl = new TitleControl();
        // 添加到地图当中
        sendnamemap.addControl(myTitleCtrl);
    }


    //
    //添加退出按钮程序
    function addLogoutButton(){
        // 定义一个控件类,即function
        function LogoutControl(){
            // 默认停靠位置和偏移量
            this.defaultAnchor = BMAP_ANCHOR_TOP_RIGHT;
            this.defaultOffset = new BMap.Size(10, 10);
        }
        // 通过JavaScript的prototype属性继承于BMap.Control
        LogoutControl.prototype = new BMap.Control();

        // 自定义控件必须实现自己的initialize方法,并且将控件的DOM元素返回
        // 在本方法中创建个div元素作为控件的容器,并将其添加到地图容器中
        LogoutControl.prototype.initialize = function(map){
            // 创建一个DOM元素
            var div = document.createElement("div");
            // 添加文字说明
            div.appendChild(document.createTextNode("退出"));
            // 设置样式
            div.style.cursor = "pointer";
            div.style.border = "1px solid gray";
            div.style.backgroundColor = "white";
            //div.style = "ba(0, 0, 0, 0.35); border-left: 1px solid rgb(139, 164, 220); border-top: 1px solid rgb(139, 164, 220); border-bottom: 1px solid rgb(139, 164, 220); background: rgb(142, 168, 224) none repeat scroll 0% 0%; padding: 2px 6px; font: bold 12px/1.3em arial,sans-serif; text-align: center; white-space: nowrap; border-radius: 3px 0px 0px 3px; color: rgb(255, 255, 255);";
            div.style ="-moz-user-select: none; box-shadow: 2px 2px 3px rgba(0, 0, 0, 0.35); border-left: 1px solid rgb(139, 164, 220); border-top: 1px solid rgb(139, 164, 220); border-bottom: 1px solid rgb(139, 164, 220); background: rgb(255, 255, 255) none repeat scroll 0% 0%; padding: 2px 6px; font-family: arial,sans-serif; font-style: normal; font-size: 12px; line-height: 1.3em; font-size-adjust: none; font-stretch: normal; font-feature-settings: normal; font-language-override: normal; font-kerning: auto; font-synthesis: weight style; font-variant: normal; text-align: center; white-space: nowrap; color: rgb(0, 0, 0);cursor:pointer";
            // 绑定事件,点击-moz-user-select: none; box-shadow: 2px 2px 3px rg一次放大两级
            div.onclick = function(e){
                location.href ="<?php echo U('Portal/Index/logout');?>";
            }
            // 添加DOM元素到地图中
            map.getContainer().appendChild(div);
            // 将DOM元素返回
            return div;
        }
        // 创建控件
        var myLogoutCtrl = new LogoutControl();
        // 添加到地图当中
        sendnamemap.addControl(myLogoutCtrl);
    }
</script>