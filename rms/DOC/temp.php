<?php
   $(function(){
        var options = {
            target : '#output1', //把服务器返回的内容放入id为output1的元素中
            beformSubmit: showRequest,    //提交前的回调函数
            success:      showResponse,   //提交后的回调寒素
            clearForm : true,               //成功提交后，清除所有表单元素的值
            resetForm : true,               //成功提交后，重置所有表单元素的值
            timeout : 3000                  //限制请求的时间，当请求大于3秒，跳出请求             
        }
        
        function showRequest(formData,jqForm,options){
                alert('dd');
        }
        
        function showResponse(responseText,statusText,xhr,$form){
            alert('ok');
        }
        //提交
        $('#CreateView').ajaxForm(options);
      
    })
    
    
    var order = new Array(4);
    order[0] = "Trident";
    order[1] = "Internet Explorer 5.0";
    order[2] = "Win 95+";
    order[3] = "5";
    order[4] = "<input type='text'>";
    var form = new Array(order[0],order[1],order[2],order[3],order[4]);
    var orderform_arr = new Array(form);  
    
    
    setInterval(function(){
        //要执行的代码 
        //alert("__URL__/listview");
        //$.ajax({
            type : "GET",
            url : "__URL__/listjson",
            dataType : "json",
            success : function(data){
                 
                og = new Array();
                $.each(data,function(commentIndex,comment){
    
                  $.each(comment,function(key,value){
                     // alert(value);
                     og.unshift(value);
                  })
                  
                })
                og.push("<input type='text'>");
                //alert(og);
                //alert(orderform_arr);
               // alert(data);
                                           
               // var od = $.parseJSON(data);
               // alert(od);
                //od = new Array(og)
                //orderform_arr.push(og);
                //alert('合并后新数组长度为: '+or.length+'. 其值为: '+or);
                $('#example').dataTable().fnClearTable();
                $('#example').dataTable().fnAddData(og);
            }
        })                    
    },2000);
    
    
    
                  
                //alert(og);
                //alert(orderform_arr);
               // alert(data);
                                           
               // var od = $.parseJSON(data);
               // alert(od);
                //od = new Array(og)
                //orderform_arr.push(og);
                //alert('合并后新数组长度为: '+or.length+'. 其值为: '+or);
?>
<script type="text/javascript">
 
        $('#{$module_name}searchAcc').window({  
            title:'查询',
            width:800,  
            height:200,
            top: ($(window).height() - 320) * 0.5,
            //left: ($(window).width() - 450) * 0.5,
            fit:false,
            border:false,
            inline:true,
            shadow:true,
            closable:true,
            collapsed:true,
            modal:true  
        });
        $('#{$module_name}searchAcc').window('close');

    

    
    //显示查询窗口
    $('#{$module_name}search').click(function(){
             
        $('#{$module_name}searchAcc').window('open');
        $('#{$module_name}searchAcc').window('expand');
                
        $('#{$module_name}searchAcc').show();          
        //输入框获得焦点
        $('#{$module_name}search_text').focus();
    });   

    //关闭查询窗口
    $('#{$module_name}btngeneralcancel').click(function(){
        $('#{$module_name}searchAcc').window('close');
    })

    //调用表单插件的'submit'方法提交 
    submit_url = "__URL__/listview/"; 



    //查询确定
    $('#{$module_name}btngeneralconfirm').click(function(){
        
        //var queryString = $('#generalSearchForm').formSerialize();
        //alert(queryString);
        var search_text = $('#{$module_name}search_text').attr('value');

        var searchOption = $('#{$module_name}searchOption').attr('value');
        queryString = 'searchOption/'+searchOption+'/search_text/'+search_text;
         
        /*
        $.ajax({
        url : submit_url+queryString,
        success : function(data){
        $("#center").html(data);
        }
        })
        */
        updateTab(submit_url+queryString);
        //location.href = submit_url+queryString;

    })

</script>

/* 返回编辑的区块信息  */
        public function getCreateBlocks($moduleid){
            //var_dump($moduleid);
            //var_dump('dd');
            //返回区块信息
            $block_model = D('blocks');
            $blocks = $block_model->field('blockid,blocklabel')->where("moduleid='$moduleid'  and  visible = 0 ")->order('sequence asc')->select();

            foreach($blocks as $block_value){
                //返回模块的字段表
                $fields_model = D('Fields');
                $fields = $fields_model->where("moduleid=$moduleid and blockid=".$block_value['blockid']." and presence=0")->order('sequence')->select();
                //echo $fields_model->getLastSql();
                $noofrows = count($fields);

                $createview_arr[$block_value['blocklabel']] = array();
                for($i=0;$i<$noofrows;$i++){
                    $fields_array = array(); //定义字段集合
                    $field_arr = array();
                    $field_arr['lable'] =  $fields[$i]['fieldname'];
                    $field_arr['name'] = $fields[$i]['fieldname']; 
                    $field_arr['uitype']   = $fields[$i]['uitype'];
                    if($fields[$i]['readonly'] ==  1){
                        $field_arr['readonly'] = 'readonly';
                    }else{
                        $field_arr['readonly'] = "";
                    }

                    $field_arr['length']   = $fields[$i]['length'];
                    //$block_s = $fields[$i]['block'];
                    $fields_array[] = $field_arr;
                    //$filed_arr[]= $this->getOutputHtml($uitype,$fieldname,$fieldname,100,'','','');
                    $i++;
                    if($i<$noofrows)  //这里重复两遍
                    {
                        $field_arr = array();
                        $field_arr['lable'] =  $fields[$i]['fieldname'];
                        $field_arr['name'] = $fields[$i]['fieldname']; 
                        $field_arr['uitype']   = $fields[$i]['uitype'];
                        if($fields[$i]['readonly'] ==  1){
                            $field_arr['readonly'] = 'readonly';
                        }else{
                            $field_arr['readonly'] = "";
                        }
                        $field_arr['length']   = $fields[$i]['length'];
                        $fields_array[] = $field_arr;
                        //$filed_arr[]=$this->getOutputHtml($uitype,$fieldname,$fieldname,100,'','','');               
                    }
                    $createview_arr[$block_value['blocklabel']][] = $fields_array;
                }
            }

            //dump($createview_arr);
            return $createview_arr;

        }

        //返回记录的详细的区块信息
        public function getDetailBlocks($moduleid,$result){
            $block_model = D('blocks');
            $blocks = $block_model->field('blockid,blocklabel')->where("moduleid='$moduleid'   ")->order('sequence asc')->select();

            foreach($blocks as $block_value){
                //返回模块的字段表
                $fields_model = D('Fields');
                $fields = $fields_model->where("moduleid=$moduleid and blockid=".$block_value['blockid'])->order('sequence')->select();
                //echo $fields_model->getLastSql();
                $noofrows = count($fields);

                $createview_arr[$block_value['blocklabel']] = array();
                for($i=0;$i<$noofrows;$i++){
                    $fields_array = array(); //定义字段集合
                    $field_arr = array();
                    $field_arr['lable'] =  $fields[$i]['fieldname'];
                    $field_arr['name'] = $fields[$i]['fieldname']; 
                    $field_arr['uitype']   = $fields[$i]['uitype'];
                    $field_arr['value'] = $result[$field_arr['name']];
                    if($fields[$i]['readonly'] ==  1){
                        $field_arr['readonly'] = 'readonly';
                    }else{
                        $field_arr['readonly'] = "";
                    }

                    $field_arr['length']   = $fields[$i]['length'];
                    //$block_s = $fields[$i]['block'];
                    $fields_array[] = $field_arr;
                    //$filed_arr[]= $this->getOutputHtml($uitype,$fieldname,$fieldname,100,'','','');
                    $i++;
                    if($i<$noofrows)  //这里重复两遍
                    {
                        $field_arr = array();
                        $field_arr['lable'] =  $fields[$i]['fieldname'];
                        $field_arr['name'] = $fields[$i]['fieldname']; 
                        $field_arr['uitype']   = $fields[$i]['uitype'];
                        $field_arr['value'] = $result[$field_arr['name']];
                        if($fields[$i]['readonly'] ==  1){
                            $field_arr['readonly'] = 'readonly';
                        }else{
                            $field_arr['readonly'] = "";
                        }

                        $field_arr['length']   = $fields[$i]['length'];
                        $fields_array[] = $field_arr;
                        //$filed_arr[]=$this->getOutputHtml($uitype,$fieldname,$fieldname,100,'','','');               
                    }
                    $createview_arr[$block_value['blocklabel']][] = $fields_array;
                }
            }

            //dump($createview_arr);
            return $createview_arr;

        }

        //返回记录的详细的区块信息
        public function getEditBlocks($moduleid,$result){
            $block_model = D('blocks');
            $blocks = $block_model->field('blockid,blocklabel')->where("moduleid='$moduleid'  and  visible = 0 ")->order('sequence asc')->select();

            foreach($blocks as $block_value){
                //返回模块的字段表
                $fields_model = D('Fields');
                $fields = $fields_model->where("moduleid=$moduleid and blockid=".$block_value['blockid']." and presence=0")->order('sequence')->select();
                //echo $fields_model->getLastSql();
                $noofrows = count($fields);

                $createview_arr[$block_value['blocklabel']] = array();
                for($i=0;$i<$noofrows;$i++){
                    $fields_array = array(); //定义字段集合
                    $field_arr = array();
                    $field_arr['lable'] =  $fields[$i]['fieldname'];
                    $field_arr['name'] = $fields[$i]['fieldname']; 
                    $field_arr['uitype']   = $fields[$i]['uitype'];
                    $field_arr['value'] = $result[$field_arr['name']];
                    if($fields[$i]['readonly'] ==  1){
                        $field_arr['readonly'] = 'readonly';
                    }else{
                        $field_arr['readonly'] = "";
                    }

                    $field_arr['length']   = $fields[$i]['length'];
                    //$block_s = $fields[$i]['block'];
                    $fields_array[] = $field_arr;
                    //$filed_arr[]= $this->getOutputHtml($uitype,$fieldname,$fieldname,100,'','','');
                    $i++;
                    if($i<$noofrows)  //这里重复两遍
                    {
                        $field_arr = array();
                        $field_arr['lable'] =  $fields[$i]['fieldname'];
                        $field_arr['name'] = $fields[$i]['fieldname']; 
                        $field_arr['uitype']   = $fields[$i]['uitype'];
                        $field_arr['value'] = $result[$field_arr['name']];
                        if($fields[$i]['readonly'] ==  1){
                            $field_arr['readonly'] = 'readonly';
                        }else{
                            $field_arr['readonly'] = "";
                        }

                        $field_arr['length']   = $fields[$i]['length'];
                        $fields_array[] = $field_arr;
                        //$filed_arr[]=$this->getOutputHtml($uitype,$fieldname,$fieldname,100,'','','');               
                    }
                    $createview_arr[$block_value['blocklabel']][] = $fields_array;
                }
            }

            //dump($createview_arr);
            return $createview_arr;

        }

        /* 处理字段的函数 */
        function getOutputHtml($uitype, $fieldname, $fieldlabel, $maxlength, $fieldvalue,$generatedtype,$module_name,$mode='', $typeofdata=null)
        {
            $final_arr = array();
            $final_arr[]=$uitype;
            $final_arr[]=$editview_label;
            $final_arr[]=$fieldname;
            $final_arr[]=$fieldvalue;
            $type_of_data  = explode('~',$typeofdata);
            $final_arr[]=$type_of_data[1];
            return $final_arr;

        }

        
        //取得查询的where
            $whereText = $_REQUEST['search_text'];
            $searchOption = $_REQUEST['searchOption'];
            //var_dump($whereText);
            //var_dump($searchOption);
            if($whereText){
                if($searchOption == '全部'){
                    $count = count($focus->searchFields) - 1;
                    foreach($focus->searchFields as $key=>$value){
                        $where .= " $value like '%$whereText%' ";
                        if($key < $count){
                            $where .= " or "; 
                        }

                    }
                }else{
                    $where = $searchOption." like '%$whereText%' ";
                }
            }
            
            
            //这里修改是为了连接备份数据库
        if ( !empty($db_config) && is_string($db_config)) {
            // 如果DSN字符串则进行解析
            $db_config = $this->parseDSN($db_config);
            $db_config['hostport'] = '3306';
            $db_config['dsn'] = null;
            $db_config['params'] = null;
            return $db_config;
        }
        
        //在这里修改了数据库的配置路径，改为按访问的url来取得
        require APP_PATH.'Conf/datapath.php';
        $HTTP_POST = $_SERVER['HTTP_HOST'];
        $dbConfig = $rmsDataPath[$HTTP_POST];
        $db_config = array (
                    'db_type'  =>  $dbConfig['DB_TYPE'],
                    'db_user'  =>  $dbConfig['DB_USER'],
                    'db_pwd'   =>  $dbConfig['DB_PWD'],
                    'db_host'  =>  $dbConfig['DB_HOST'],
                    'db_port'  =>  $dbConfig['DB_PORT'],
                    'db_name'  =>  $dbConfig['DB_NAME'],
                );

<link rel="stylesheet" type="text/css" href="__PUBLIC__/themes/bootstrap/easyui.css">
<link rel="stylesheet" type="text/css" href="__PUBLIC__/Css/icon.css">
<load href=".__PUBLIC__/Css/demo11.css" />