<?php
    /**
    * 导sqlserver的来电表到rms中
    * 执行字段：/Applications/XAMPP/xamppfiles/bin/php /Applications/XAMPP/htdocs/rms/litter.php TranTeltoRms/index
    */
    class TranTeltoRmsAction extends Action{
        public function index(){
            $handle = fopen("/Users/apple/Downloads/tel.txt","r");
            if($handle){
                while(!feof($handle)){
                    $context= fgets($handle);
                    $content_arr = explode(',',$context);
                    var_dump($content_arr);

                    foreach($content_arr as $key => $value){
                        if($key == 0){
                            $telphone = str_replace('"','',$value);
                            $telphone = trim($telphone);
                            echo $telphone;
                        }
                        if($key == 1){
                            $address = str_replace('"','',$value);
                            $address = trim($address);
                            echo $address;
                        }
                    }

                    $rmsdns = 'mysql://root:@localhost:3306/rms';
                    //建立orderform的备份表
                    $TelcustomerModel = M('telcustomer','rms_',$rmsdns);
                    //$TelcustomerModel = D('Telcustomer');

                    $data = array();
                    $data['telphone'] = $telphone;
                    $address = $s = iconv("GB2312","UTF-8",$address);
                    if(empty($address)){
                        $address = '';
                        $data['address'] = '';
                    }else{
                        $data['address'] = $address;
                    }

                    $data['rectime'] = date('Y-m-d H:i:s');
                    var_dump($data);
                    $TelcustomerModel->create();
                    $result = $TelcustomerModel->add($data);
                    var_dump($TelcustomerModel->getLastSql());
                    if($result){
                        var_dump($result);
                        $record = $TelcustomerModel->getLastInsID();
                        $TeladdressModel = M('teladdress','rms_',$rmsdns);  //D('Teladdress');
                        $data = array();
                        $data['telcustomerid'] = $record;
                        $data['address'] = $address;
                        $TeladdressModel->create();
                        $result = $TeladdressModel->add($data);
                    }
                }
            }
            fclose($handle);
        }
    }
?>
