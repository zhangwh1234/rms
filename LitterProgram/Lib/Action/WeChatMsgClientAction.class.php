<?php

/**
 * Created by zhangwh1234
 * User: lihua
 * Date: 16/3/14
 * Time: 上午11:51
 * 获取消息的客户端
 * 测试命令：/usr/local/php/bin/php
 * 部署命令：/usr/local/php/bin/php /home/lihuasoft/assistant/litter.php WeChatMsgClient/getGzMsg
 */
class WeChatMsgClientAction extends Action
{

    /**
     * 获取常州的消息
     * insert into interface_mt_msg (dispID,MSGID,SRCnumber,DESTnumber,MSGSRC,MSGCONTENT,COMMITTIME,SENDSTAT,sendname,company,area) va
     */
    public function getCzMsg()
    {
        Log::$format = '[y-m-d H:i:s]';
        $this->LogFile = LOG_PATH . 'czWeChat_' . date('Y-m-d') . '.log';
        while(1){
            $this->getMsg(4);
            usleep(300000);  //1秒
        }

    }

    /**
     * 获取南京
     */
    public function getNjMsg(){
        Log::$format = '[y-m-d H:i:s]';
        $this->LogFile = LOG_PATH . 'njWeChat_' . date('Y-m-d') . '.log';
        while(1){
            $this->getmsg(6);
            usleep(300000);
        }

    }

    /**
     * 获取广州的消息
     */
    public function getGzMsg()
    {
        Log::$format = '[y-m-d H:i:s]';
        $this->LogFile = LOG_PATH . 'gzWeChat_' . date('Y-m-d') . '.log';
        while(1){
            $this->getMsg(9);
            usleep(300000);
        }
    }

    /**
     * 获取消息
     * @param $shopid
     */
    function getMsg($shopid)
    {
        // 载入curl函数等
        load("@.function");
        $connect_str = C('RMS_CONNECT');
        $interfacemtmsg_model = M('interface_mt_msg', Null, $connect_str);
        $url = 'http://nj.lihuaerp.com/index.php/InterfaceServer/getMsg/token/lihua1/domain/' . $shopid;
        $result = curl($url);
        Log::write('返回数据！'.$result, LOG::INFO, LOG::FILE, $this->LogFile);
        if (empty($result)) {
            return;
        }
        $smsmgr = json_decode($result, true);
        foreach ($smsmgr as $value) {
            $data = array();
            $data['MSGID'] = $value['smsmgrid'];
            $data['DESTnumber'] = $value['telphone'];
            $data['MSGCONTENT'] = $value['content'];
            $data['committime'] = date('Y-m-d H:i:s');
            $data['sendname'] = $value['sendname'];
            $data['company'] = $value['company'];
            $data['weixin'] = $value['weixin'];
            $data['area'] = $this->getArea($shopid);
            $interfacemtmsg_model->create();
            $interfacemtmsg_model->add($data);
            Log::write('保存数据！'.$interfacemtmsg_model->getLastSql(), LOG::INFO, LOG::FILE, $this->LogFile);
            //确认消息
            $this->setMsg($value['smsmgrid']);
        }

    }

    /**
     * 确认消息
     */
    function setMsg($smsmgrid)
    {
        // 载入curl函数等
        load("@.function");
        $url = 'http://nj.lihuaerp.com/index.php/InterfaceServer/setMsg/token/lihua1/smsmgrid/' . $smsmgrid;
        Log::write('确认！'.$url, LOG::INFO, LOG::FILE, $this->LogFile);
        $result = curl($url);
        Log::write('确认返回数据！'.$result, LOG::INFO, LOG::FILE, $this->LogFile);
    }

    /**
     * 获得地区
     */
    function getArea($shopid)
    {
        switch ($shopid) {
            case 4:
                return '常州';
                break;
            case 6:
                return '南京';
                break;
            case 9:
                return '广州';
                break;

        }
    }
}