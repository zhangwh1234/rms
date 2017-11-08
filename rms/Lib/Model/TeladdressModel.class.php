<?php
    class TeladdressModel extends CRMEntityModel{

        var $trueTableName = 'rms_teladdress';

        // 回调方法 ，初始化
        protected function _initialize() {
            switch($this->getDomain()){
                case 'sz.lihuaerp.com':
                    $this->trueTableName = 'rms_teladdress_sz';
                    break;
                case 'nj.lihuaerp.com':
                    $this->trueTableName = 'rms_teladdress_nj';
                    break;
                case 'sh.lihuaerp.com':
                    $this->trueTableName = 'rms_teladdress_sh';
                    break;
                case 'wx.lihuaerp.com':
                    $this->trueTableName = 'rms_teladdress_wx';
                    break;
                case 'bj.lihuaerp.com':
                    $this->trueTableName = 'rms_teladdress';
                    break;
                default:
                    $this->trueTableName = 'rms_teladdress';
                    break;
            }
        }
    }
?>
