<?php
    /**
    * 分公司管理
    */

    class CompanyMgrAction extends ModuleAction{
        
        //定义启动是的焦点字段
        public function getFocusFields(){
            $fields = "name";
            return $fields;
        }
    }


?>
