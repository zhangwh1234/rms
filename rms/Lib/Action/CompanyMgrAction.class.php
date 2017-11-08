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


        // 返回一些其他的数据,比如下拉列表框等的数据
        public function returnMainFnPara($result)
        {


            $baiduauto = array(
                array(
                    'name' => '自动',
                ),
                array(
                    'name' => '人工',
                ),
            );
            $this->assign('baiduauto', $baiduauto);
            $this->assign('baiduautofirst', '自动');
            $elmauto = array(
                array(
                    'name' => '自动',
                ),
                array(
                    'name' => '人工',
                )
            );
            $this->assign('elmauto', $elmauto);


            $meituanauto = array(
                array(
                    'name' => '自动',
                ),
                array(
                    'name' => '人工',
                )
            );
            $this->assign('meituanauto', $meituanauto);

            $telphoneauto = array(
                array(
                    'name' => '营业',
                ),
                array(
                    'name' => '休息',
                ),
                array(
                    'name' => '人工',
                )
            );
            $this->assign('telphoneauto', $telphoneauto);
        }


        /**
         * 绘制地图
         */
        public function drawmap(){
            $this->display('map');
        }

        /**
         * 获取坐标位置
         */
        public function getpoint(){
            $this->display('getpoint');
        }

        /**
         * 返回分公司的送餐范围
         */
        public function getRegion(){
            // 取得模块的名称
            $moduleName = $this->getActionName ();
            $this->assign ( 'moduleName', $moduleName ); // 模块名称

            // 启动当前模块
            $focus = D ( $moduleName );

            $fields = array('name,region,telphoneauto');
            $companymgrResult = $focus->field($fields)->select();

            //处理,返回
            $companyRegion = array();
            foreach($companymgrResult as $companymgrValue){
                $companyRegion[$companymgrValue['name']] = array(
                    'telphoneauto' => $companymgrValue['telphoneauto'],
                    'region' =>  $companymgrValue['region']
                );
            }

            $this->ajaxReturn($companyRegion);
        }
    }


?>


