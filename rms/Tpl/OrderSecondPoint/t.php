<load href="__PUBLIC__/Js/jquery.dataTables.js" />
<script type="text/javascript" charset="utf-8">

    //???嶩??????
    var orderform_arr = new Array();   //????
    var ordergoods_arr = new Array(4);  //????

    $(document).ready(function() {
        $('#example').dataTable( {
            "bPaginate": false,
            "bLengthChange": false,
            "bFilter": false,            
            "sScrollY" : '400px',  //??????ֱ????
            "bPaginate" : false,   //????ʾ??ҳ??
            "bScrollCollapse" : true,  //???޹�???
            'bSort' : false,     //????????????
            'bInfo' : false,    //?Ƿ???ʾ??????һЩ??Ϣ
            "bProcessing": true,
            "aaData": orderform_arr,
            "aoColumns": [
            { "sTitle": "??ַ" },
            { "sTitle": "????????" },
            { "sTitle": "?绰" },
            { "sTitle": "?Ͳ͵绰", "sClass": "center" },
            { "sTitle": "????", "sClass": "center" }
            ],
            "bAutoWidth": true 
        });


        $.ajax({
            type : "GET",
            url : "__URL__/alllistjson",
            dataType : "json",
            success : function(data){                 
                orderTemp = new Array();
                $.each(data,function(commentIndex,comment){
                    orderTemp = new Array();    
                    $.each(comment,function(key,value){
                        orderTemp.unshift(value);
                         //alert(value);
                        orderTemp.push("<input type='text'>");
                    })
                    orderform_arr.unshift(orderTemp);
                })
                //???¶???
                updateOrder();
                $('#example').dataTable().fnClearTable();
                $('#example').dataTable().fnAddData(orderform_arr);
            }
        })                        
    });

    function updateOrder(){
               $.ajax({
            type : "GET",
            url : "__URL__/updateorder?&order=11",
            dataType : "json",
            success : function(data){                 
                              
            }
        });                       
    
    }
    exit;
    //??ʱˢ?¶???
    setInterval(function(){
        //Ҫִ?еĴ??? 
        //alert("__URL__/listview");
        $.ajax({
            type : "GET",
            url : "__URL__/listjson",
            dataType : "json",
            success : function(data){
                orderTemp = new Array();
                $.each(data,function(commentIndex,comment){    
                    $.each(comment,function(key,value){
                        orderTemp.unshift(value);
                        // alert(value);
                        orderTemp.push("<input type='text'>");
                    })
                    orderform_arr.unshift(orderTemp);
                })
 
                $('#example').dataTable().fnClearTable();
                $('#example').dataTable().fnAddData(orderform_arr);
            }
        })                    
    },2000);



</script>

<style type="text/css">
    /* ??ҳ */
    .pagestop{
        border:0px solid red;
        margin-bottom: 1px ;
        margin-top: 0px;
        width:100%;
        height: 25px;
        background-color: #F4F4F4;
        border-top: 1px solid #EAEAEA;
        height: 27px;
        line-height: 27px;
        text-align: center;
    }
    .pagesbot{
        border:0px solid red;
        margin-bottom: 1px ;
        margin-top: 0px;
        width:100%;
        height: 25px;
        background-color: #F4F4F4;
        border-bottom: 1px solid #EAEAEA;
        height: 27px;
        line-height: 27px;
        text-align: center;
    }
</style>

<div class="pagestop">{$page}</div>
<div style="border: 1px solid red; width: 100%; height: 470px;">
    <table cellpadding="0" cellspacing="0" border="1" class="display" id="example" width="100%" align="center" >
        <thead>
            <tr>
                <th width="20%">??ַ</th>
                <th width="25%">????????</th>
                <th width="25%">?绰</th>
                <th width="15%">Ҫ??ʱ??</th>
                <th width="15%">????</th>
            </tr>
        </thead>
        <tbody>

        </tbody>

    </table>
</div>
<div class="pagestop">{$page}</div>


//测试listview
        public function listview1(){ 
            //取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName',$moduleName);   //模块名称   

            //启动当前模块
            $focus = D($moduleName);

            //取得对应的导航名称
            $tabName = $focus->getTabName($moduleName);
            $this->assign('tabName',$tabName);         //导航民

            //启动列表菜单            
            $this->listviewMenu();

            //生成list字段列表
            $listFields = $focus->listFields;
            //模块的ID
            $moduleId = $focus->getPk();

            //加入模块id到listHeader中
            $listHeader = $listFields;
            $this->assign("listHeader",$listHeader);   //列表头
            $this->assign('returnAction','listview');  //定义返回的方法


            //导入分页类
            import('ORG.Util.Page');// 导入分页类
            $total      = $focus->count();// 查询满足要求的总记录数   
            //查session取得page的firstRos和listRows
            if(isset($_SESSION[$moduleName.'firstRowlistview'])){
                $Page->firstRow = $_SESSION[$moduleName.'firstRowlistview'];
            }
            $listMaxRows = C('LIST_MAX_ROWS'); //定义显示的列表函数
            if(isset($listMaxRows)){
                $Page->listRows = $listMaxRows;
            }else{
                $listMaxRows = 15;
            } 

            $Page = new Page($total,$listMaxRows);
            $show = $Page->show();



            //查询模块的数据 
            $selectFields = $listFields;
            array_unshift($selectFields,$moduleId);

            $listResult = $focus->field($selectFields)->limit($Page->firstRow.','.$Page->listRows)->order("$moduleId desc")->select();

            // 从数据中列出列表的数据
            $listviewEntries = $this->getListviewEntity($listResult,$moduleId);

            $this->assign('list_link_field',$focus->list_link_field);
            $this->assign('moduleId',$moduleId);
            $this->assign('listEntries',$listviewEntries);
            $this->assign('page',$show);// 赋值分页输出

            $this->display('OrderForm/listview');

        }

        orderFormHandleGrid.datagrid('updateRow',{
                                    index: rowIndex,    //定位行
                                    row: {
                                        state : handleData['state'],
                                        sendname: handleData['sendname']
                                    }  
                                });
                                
        $('#winprintpage').window({
            title:'打印纸张设置',
            width:200,
            height:100,
            collapsible:false,
            minimizable:false,
            maximizable:false,
            modal:true
        });
