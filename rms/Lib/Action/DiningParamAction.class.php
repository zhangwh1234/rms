<?php
/**
 * Created by IntelliJ IDEA.
 * User: apple
 * Date: 17/10/30
 * Time: 下午5:24
 */

class DiningParamAction extends ModuleAction{

    /**
     * 第一个页面是编辑页面
     */
    public function index() {
        $this->editview ();
    }


}