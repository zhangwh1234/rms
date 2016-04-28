<?php
    class EmptyAction extends Action{
        public function index(){
            //转向默认的模块
            //$this->redirect("/Notice");
            echo "no Action";
             
        }
        
        public function _empty(){
            $this->redirect("/Notice");
        }
   
    }

