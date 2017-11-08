<?php
    /**
    * 短信发送程序,使用上海移动的API
    * 2014-04-23
    * http://127.0.0.1/newrmstest/litter.php?s=/SmsSendSHyidong.html
    * /usr/local/php/bin/php /home/ftp/1520/lihuaerp-20140513-Lra/weborderdown/index.php Webinfo/index
    */
    class SmsSendSHyidongAction extends Action{
        public function index(){

            //载入同样函数
            load("@.function");
            $smsModel = D('SmsMgr');
            //echo date('H:i:s');
            //获取短信内容
            $where = array();
            $where['issend'] =  0;
            $smsResult = $smsModel->where($where)->find();
            //var_dump($smsModel->getLastSql());
            //var_dump($smsResult);
           // var_dump('\n');
            if($smsResult){
                $date = array();
                $data['dispID'] = $smsResult['smsmgrid'];  //ID
                $data['MSGID'] = $smsResult['smsmgrid'];
                $data['ISMGMSGID'] = null;
                $data['SPnumber'] = null;
                $data['SRCnumber'] = '';
                $data['DESTnumber'] = $smsResult['telphone'];  //电话
                $data['MSGSRC'] = ' ';
                $data['LINKID'] = null;
                $data['MSGFORMAT'] = null;
                $data['MSGCONTENT'] = $smsResult['content']; // 内容
                $data['COMMITTIME'] = date('y-m-d h:i:s');
                $data['FINISHTIME'] = NULL;
                //$data['SENDSTAT'] = '0';
                $data['sendname'] = $smsResult['sendname'];
                $data['company'] =  $smsResult['company'];
                $data['area'] = '南京';
                //p($data);
                //插入数据表
                $dns =  'mysql://smsmgr:smslihua0731@61.160.239.34:9906/cmppdb'; ;
                $shyidongModel = new Model('mt_msg','interface_');
                $shyidongModel->db(1,$dns);
                //var_dump($shyidongModel);
                //$result = $shyidongModel->find();
                // var_dump($result);
                $shyidongModel->create();
                $result = $shyidongModel->add($data);
                //var_dump($shyidongModel->getLastSql());
                //var_dump($result);

                if($result){
                    $where = array();
                    $where['smsmgrid']=$smsResult['smsmgrid'];
                    $data=array();
                    $data['issend'] =  1;
                    $data['senddate'] = date('Y-md-d H:i:s');
                    $smsModel->where($where)->save($data);
                }

                Log::write('发送消息'.$smsResult['telphone'].' '.$smsResult['content']);
            }                




        }
    }
?>
