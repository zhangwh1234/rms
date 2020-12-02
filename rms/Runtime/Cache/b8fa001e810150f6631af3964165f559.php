<?php if (!defined('THINK_PATH')) exit();?><div id="popupviewproducts_div" class="easyui-layout" data-options="fit:true" style="overflow: hidden;">
    <div data-options="region:'center'">
                <table id="yingshouinnercarry_popupaccountsview_datagrid" class="easyui-datagrid" data-options='<?php $dataOptions = array_merge(array ( 'border' => true, 'fit' => true, 'fitColumns' => true, 'rownumbers' => true, 'singleSelect' => true, 'striped' => true, 'pagination' => true, 'pageList' => array ( 0 => 10, 1 => 20, 2 => 30, 3 => 50, 4 => 80, 5 => 100, ), 'pageSize' => 10, ), $datagrid["options"]);if(isset($dataOptions['toolbar']) && substr($dataOptions['toolbar'],0,1) != '#'): unset($dataOptions['toolbar']); endif; echo trim(json_encode($dataOptions), '{}[]').((isset($datagrid["options"]['toolbar']) && substr($datagrid["options"]['toolbar'],0,1) != '#')?',"toolbar":'.$datagrid["options"]['toolbar']:null); ?>' style=""><thead><tr><?php if(is_array($datagrid["fields"])):foreach ($datagrid["fields"] as $key=>$arr):if(isset($arr['formatter'])):unset($arr['formatter']);endif;echo "<th data-options='".trim(json_encode($arr), '{}[]').(isset($datagrid["fields"][$key]['formatter'])?",\"formatter\":".$datagrid["fields"][$key]['formatter']:null)."'>".$key."</th>";endforeach;endif; ?></tr></thead></table>
    </div>
</div>
<input id="popupviewproductstable_row" name="popupviewproductstable_row"  hidden value="<?php echo ($row); ?>" />

<script type="text/javascript">

    var YingshouIncomeMgrPopupAccountsviewModule = {

        init: function () {
            //设置div的高度
            $('#OrderFormListviewDiv').height(IndexIndexModule.operationHeight);
        },

        //操作格式化
        operate: function (val, rowData, rowIndex) {
            var btn = [];
            btn.push('<a href="javascript:void(0);" onclick="YingshouIncomeMgrPopupAccountsviewModule.selectProducts(' + rowData.paymentmgrid+','+rowData.code+',\''
                + rowData.name+'\',' +')">选择</a>');
            return btn.join(' | ');
        },

        //单选产品
        selectProducts:function (productsid,code,name){
             $('#YingshouIncomeMgrCreateviewForm input[name="paymentcode"]').val(code);
             $('#YingshouIncomeMgrCreateviewForm input[name="name"]').val(name);

            $('#globel-dialog-div').dialog('close');

        }
    }

</script>