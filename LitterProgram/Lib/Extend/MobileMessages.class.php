<?php
/**
 * Created by zhangwh1234
 * User: apple
 * Date: 15/9/28
 * Time: 上午10:52
 */


class MobileMessages {
    /**
     * 短信验证接口
     */
    public function verifySms(){
        import("@.Extend.ChuanglanSmsApi");
        $clapi  = new ChuanglanSmsApi();
        $result = $clapi->sendSMS('13235192275', '您好，您的验证码是888888','true');
        $result = $clapi->execResult($result);
        var_dump($result);

    }


}