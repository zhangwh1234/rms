<?php
    class TelcustomerModel extends CRMEntityModel{
        var  $trueTableName = 'rms_telcustomer';

        var $focusFields = 'telphone';
        var $listFields = array(
            'name'=>array('width'=>20),
            'telphone'=>array('width'=>20),
            'address' => array('width'=>20),
            'rectime' => array('width' =>20));
        
         //焦点字段
        var $fieldsFocus = 'telphone';

        //定义查询的字段
        var $searchFields = array('rms_telcustomer.telphone','rms_teladdress.address');

        //定义新建，浏览，编辑数据的字段
        var $createFields = array(
        'LBL_ACCOUNT_INFORMATION' => array(
        array(
            'name'=>'telphone','uitype'=>21,'readonley'=>1,'length'=>24
        ),array(
            'name'=>'name','uitype' => 1,'readonly' => 1,'length' => 30
        )
        ),
        'LBL_ADDRESS_INFORMATION' => array(
        array(
            'name'=>'address','uitype'=>50,'readonley'=>1,'length'=>100
        )
        ),

        );

        var $editFields =  array();

        var $detailFields = array();

        // 回调方法 ，初始化
        protected function _initialize() {
            $this->editFields = $this->createFields; //编辑字段
            $this->detailFields = $this->createFields; //浏览字段
            switch($this->getDomain()){
                case 'sz.lihuaerp.com':
                    $this->trueTableName = 'rms_telcustomer_sz';
                    break;
                case 'nj.lihuaerp.com':
                    $this->trueTableName = 'rms_telcustomer_nj';
                    break;
                case 'sh.lihuaerp.com':
                    $this->trueTableName = 'rms_telcustomer_sh';
                    break;
                case 'wx.lihuaerp.com':
                    $this->trueTableName = 'rms_telcustomer_wx';
                    break;
                case 'bj.lihuaerp.com':
                    $this->trueTableName = 'rms_telcustomer';
                    break;
                default:
                    $this->trueTableName = 'rms_telcustomer';
                    break;
            }
        }

        //返回ID
        public function getPk(){
            return 'telcustomerid';
        }



    }
?>
