<?php
  /**
  * 用户与角色关联模型
  */
  class UserRelationModel extends RelationModel{
      
      // 定义主表名称
      protected $tableName = 'user';
      
      //定义关联关系
      protected $_link = array(
        'role' => array(
            'mapping_type' => MANY_TO_MANY,
            'foreign_key' => 'user_id',
            'relation_key' => 'role_id',
            'relation_table' => 'rms_role_user',
            'mapping_fields' => 'id, name, remark'
        )
      );
  }
?>
