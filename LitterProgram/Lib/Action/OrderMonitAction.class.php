<?php
    /**
    * 自动计算订单的数量，分公司的订单数量等情况
    *  2014-05-12
    *  /Applications/XAMPP/xamppfiles/bin/php /Applications/XAMPP/htdocs/rms/litter.php OrderMonit
    */

    class OrderMonitAction extends Action{
    	
    	/**
    	 *  计算所有的域名的情况
    	 */
		public function index(){
			require APP_PATH.'Conf/domainconfig.php';
			foreach ($domainArray as $key => $value){
				$this->goMonit($value);
			}
		}
    	
    	/***
    	 *  计算
    	 */
        public function goMonit($access){
            $ordermonitModel = D('Ordermonit','',$access);
            $domain="'" .$access."'";
            //插入全部的字段
            $sql = "select name from rms_ordermonit where name='全部' and domain=$domain";
            $allNameResult = $ordermonitModel->query($sql);
            if(!$allNameResult){
                $sql = "insert into rms_ordermonit (name,domain) values ('全部',$domain)"; 
                $ordermonitModel->query($sql);
            }
		
            //查询分公司的名字
            $companyModel = D('Companymgr','',$access);
            $sql = "select name from rms_companymgr where domain=$domain";
            $company = $companyModel->query($sql);
            foreach($company as $value){
                $companyName = trim($value['name']);
                $sql = "select name from rms_ordermonit where name='$companyName' and domain=$domain";
                $result = $companyModel->query($sql);
                if(count($result) == 0){
                    $sql = "insert into rms_ordermonit (name,domain) values ('$companyName',$domain)";
                    $ordermonitModel->query($sql);
                }
            }


            //查询ordermonit表中是否有 全部 ，分公司等字段，如果没有，就补上

            //查询ordermonit表全部的订单量 ，未处理订单，已处理订单，催送，总金额
            $sql = "select count(*) as totalnumber from rms_orderform  where ((state not like '%退餐') and (state not like '%作废')) and domain=$domain";
            $result = $ordermonitModel->query($sql);
            $totalnumber = $result[0]['totalnumber'];

            $sql = "select count(*) as notask from rms_orderform where not (state like '已%') and domain=$domain";
            $result =$ordermonitModel->query($sql);
            $notask = $result[0]['notask'];

            $sql = "select count(*) as task from rms_orderform where  (state like '已%') and domain=$domain";
            $result = $ordermonitModel->query($sql);
            $task = $result[0]['task'];

            $sql = "select count(*) as fast from rms_orderform where hurrynumber > 0 and domain=$domain";
            $result = $ordermonitModel->query($sql);
            $fast =$result[0]['fast'];

            $sql = "select sum(totalmoney) as totalmoney from rms_orderform where ((state not like '%退餐') and (state not like '%作废')) and domain=$domain";
            $result = $ordermonitModel->query($sql);
            $totalmoney = $result[0]['totalmoney'];
            if(empty($totalmoney)) $totalmoney =0;

            //网订的数量
            $sql = "select count(*) as web  from rms_orderform where (telname='网络' or telname='手机APP' or  telname='淘点点' or  telname='微信') 
            		and ((state not like '%退餐') and (state not like '%作废')) and domain=$domain ";
            $result = $ordermonitModel->query($sql);
            $web = $result[0]['web'];

             //废单的数量
            $sql = "select count(*) as cancel from rms_orderform where state like '%废%'  and domain=$domain";
            $result = $ordermonitModel->query($sql);
            $cancel = $result[0]['cancel'];

            //退餐的数量
            $sql = "select count(*) as returnorder from rms_orderform where state like '%退餐%' and domain=$domain";
            $result = $ordermonitModel->query($sql);
            $returnorder = $result[0]['returnorder'];

            //把查询的结果保存到表中
            $sql = "update rms_ordermonit set totalnumber=$totalnumber,notask=$notask,task=$task,fast=$fast,web=$web,
                    totalmoney=$totalmoney,cancel=$cancel,returnorder=$returnorder  where name='全部' and domain=$domain";
            $ordermonitModel->query($sql);

            foreach($company as $value){
                $companyName = trim($value['name']);
                $sql = "select count(*) as totalnumber from rms_orderform where company='$companyName' and 
                	((state not like '%退餐') and (state not like '%作废')) and domain=$domain";

                $result = $ordermonitModel->query($sql);

                $totalnumber = $result[0]['totalnumber'];

                $sql = "select count(*) as notask from rms_orderform where not (state like '已%') and  
                 		company='$companyName' and ((state not like '%退餐') and (state not like '%作废')) and domain=$domain";
                $result = $ordermonitModel->query($sql);
                $notask = $result[0]['notask'];

                $sql = "select count(*) as task from rms_orderform where  (state like '已%') and  company='$companyName' and
                		 ((state not like '%退餐') and (state not like '%作废')) and domain=$domain";
                $result = $ordermonitModel->query($sql);
                $task = $result[0]['task'];

                $sql = "select count(*) as notask from rms_orderform where hurrynumber > 0 and  company='$companyName' and 
                		((state not like '%退餐') and (state not like '%作废')) and domain=$domain";
                $result = $ordermonitModel->query($sql);
                $fast = $result[0]['fast'];
                if(empty($fast)) $fast = 0;

                //网订的数量
                $sql = "select count(*) as web  from rms_orderform where (telname='网络' or telname='手机APP' or 
                		 telname='淘点点' or  telname='微信') and   company='$companyName' and ((state not like '%退餐') 
                		and (state not like '%作废')) and domain=$domain";
                $result = $ordermonitModel->query($sql);
                $web = $result[0]['web'];

                $sql = "select sum(totalmoney) as totalmoney from rms_orderform  where company='$companyName' and
                		 ((state not like '%退餐') and (state not like '%作废')) and domain=$domain";
                $result = $ordermonitModel->query($sql);
                $totalmoney = $result[0]['totalmoney'];
                if(empty($totalmoney)) $totalmoney =0;
                
                $sql = "select count(*) as fast from rms_orderform where  company='$companyName' and hurrynumber > 0 and domain=$domain";
                $result = $ordermonitModel->query($sql);
                $fast = $result[0]['fast'];

                 //废单的数量
                $sql = "select count(*) as cancel from rms_orderform where state like '%废%' and  company='$companyName' and domain=$domain";
                $result = $ordermonitModel->query($sql);
                $cancel = $result[0]['cancel'];

                //退餐的数量
                $sql = "select count(*) as returnorder from rms_orderform where state like '%退餐%' and  company='$companyName' and domain=$domain";
                $result = $ordermonitModel->query($sql);
                $returnorder = $result[0]['returnorder'];

                //把查询的结果保存到表中
                $sql = "update rms_ordermonit set  totalnumber=$totalnumber,notask=$notask,task=$task,fast=$fast,web=$web,
                		totalmoney=$totalmoney,cancel=$cancel,returnorder=$returnorder  where name='$companyName' and domain=$domain";
                $ordermonitModel->query($sql);

            }


        }
    }

?>
