<?php
/**
 * Created by IntelliJ IDEA.
 * User: apple
 * Date: 15/9/28
 * Time: 下午12:16
 * 短信测试代码
 * /Applications/XAMPP/xamppfiles/bin/php /Applications/XAMPP/htdocs/rms/litter.php Sms/sendsmsTest
 */

class SmsAction extends Action{

    /**
     * 测试发送
     */
    public function sendsmsTest(){
        import("@.Extend.MobileMessages");
        $mobileMessages = new MobileMessages();
        $return = $mobileMessages->verifySms();
        var_dump($return);
    }
}