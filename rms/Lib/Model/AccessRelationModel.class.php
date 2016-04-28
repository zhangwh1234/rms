<?php
  /**
  * 权限表和节点表的视图
  */
  class AccessRelationModel extends RelationModel{
      
      // 定义主表名称
      protected $tableName = 'access';
      
      //定义关联关系
      protected $_link = array(
        'node' => array(
            'mapping_type' => MANY_TO_MANY,
            'foreign_key' => 'rold_id',
            'relation_key' => 'node_id',
            'relation_table' => 'rms_node',
            'mapping_fields' => 'id, name, title'
        )
      );
      
  }
?>
