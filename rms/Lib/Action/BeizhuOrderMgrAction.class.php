<?php
/**
 * 备注字段管理
 */

class BeizhuOrderMgrAction extends ModuleAction{

	//定义启动是的焦点字段
	public function getFocusFields(){
		$fields = "name";
		return $fields;
	}
}