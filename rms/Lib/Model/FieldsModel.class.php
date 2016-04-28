<?php

  //定义模块的字段表
  class FieldsModel extends Model{
      //返回模块的字段表
      public function getModuleFields($moduleid){
          $fields_result = $this->where("moduleid=$moduleid")->select();
          return $fields_result; 
      }
  }
?>
