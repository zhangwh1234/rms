<?php
    //定义系统的基类
    class CRMEntityModel extends Model{

        var $otherListFields = array();

        /* 返回列表的表格头 */
        function ListViewHeader(){

        }

        /* 返回新建的区块信息  */ 
        public function createBlocks(){
            //定义返回数组
            $createFields = $this->createFields;
            $createViewArray = array();
            foreach($createFields as $blocks => $createFieldsValue){
                $fieldsLength = count($createFieldsValue);
                for($i=0;$i<$fieldsLength;$i++){
                    $fieldsArray = array();
                    $fieldsArray[] = $createFieldsValue[$i]; 

                    $i++;
                    if($i<$fieldsLength){
                        $fieldsArray[] = $createFieldsValue[$i]; 
                    }                  
                    $createViewArray[$blocks][] = $fieldsArray;

                }
            }
            return $createViewArray;
        }

        //返回浏览记录的详细的区块信息
        public function detailBlocks($result){
            //定义返回数组
            $detailFields = $this->detailFields;
            $detailViewArray = array();
            foreach($detailFields as $blocks => $detailFieldsValue){
                $fieldsLength = count($detailFieldsValue);
                for($i=0;$i<$fieldsLength;$i++){
                    $fieldsArray = array();
                    $detailFieldsValue[$i]['value'] =$result[$detailFieldsValue[$i]['name']];
                    $fieldsArray[] = $detailFieldsValue[$i]; 

                    $i++;
                    if($i<$fieldsLength){
                        $detailFieldsValue[$i]['value'] =$result[$detailFieldsValue[$i]['name']];   
                        $fieldsArray[] = $detailFieldsValue[$i]; 
                    }                  
                    $detailViewArray[$blocks][] = $fieldsArray;

                }
            }

            return $detailViewArray;
        }
        //返回编辑记录的详细的区块信息
        public function editBlocks($result){
            //定义返回数组
            $editFields = $this->editFields;
            $editViewArray = array();
            foreach($editFields as $blocks => $editFieldsValue){
                $fieldsLength = count($editFieldsValue);
                for($i=0;$i<$fieldsLength;$i++){
                    $fieldsArray = array();
                    $editFieldsValue[$i]['value'] =$result[$editFieldsValue[$i]['name']];
                    $fieldsArray[] = $editFieldsValue[$i]; 

                    $i++;
                    if($i<$fieldsLength){
                        $editFieldsValue[$i]['value'] =$result[$editFieldsValue[$i]['name']];   
                        $fieldsArray[] = $editFieldsValue[$i]; 
                    }                  
                    $editViewArray[$blocks][] = $fieldsArray;

                }
            }
            return $editViewArray;
        }
        
        //退餐的blocks信息
        function returnBlocks(){
            //定义返回数组
            $returnFields = $this->returnFields;
            $returnViewArray = array();
            foreach($returnFields as $blocks => $returnFieldsValue){
                $fieldsLength = count($returnFieldsValue);
                for($i=0;$i<$fieldsLength;$i++){
                    $fieldsArray = array();
                    $fieldsArray[] = $returnFieldsValue[$i]; 

                    $i++;
                    if($i<$fieldsLength){
                        $fieldsArray[] = $returnFieldsValue[$i]; 
                    }                  
                    $returnViewArray[$blocks][] = $fieldsArray;

                }
            }
            return $returnViewArray;
        }

        
        //根据模块名称，返回对应的导航
        function getNavName($currentModule){
        	
            //读取缓存，如果有的话
            $navName = F('moduleName'.$currentModule);
            if(!empty($navName)){
                return $navName;
            }
            //启动模块,取得模块的ID
            $menuModel = D('Menu');
            $menuResult = $menuModel->field('menuid')->where("menuname='$currentModule'")->select();      
            $menuid = $menuResult[0]['menuid'];

            //启动导航和模块关联
            $navmenuModel = D('Nav_menu');
            $navmenuResult = $navmenuModel->field('navid')->where("menuid=$menuid")->select();
            $navid = $navmenuResult[0]['navid'];

            //取得当前导航名称
            $navModel = D('Nav');
            $navResult = $navModel->field('navname')->where("navid=$navid")->select();
            $navName = $navResult[0]['navname'];

            //缓存
            F('moduleName'.$currentModule,$navName);
            return $navName;

        }

    }

?>
