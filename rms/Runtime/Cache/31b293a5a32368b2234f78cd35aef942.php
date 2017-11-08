<?php if (!defined('THINK_PATH')) exit();?><div>
    <form style="overflow:hidden;"  id="PaymentEditformSelectSendname">
        <table cellspacing="6" width="100%" style="margin-left: 10px;margin-top:10px;">
            <tr>
                <td>
                    <span style="font-size: 16px;">送餐员选择</span>
                    <select style="width: 100px;" id="selectprintpage" name="sendnameselect">  
                        <?php if(is_array($sendnamemgr)): foreach($sendnamemgr as $key=>$vo): ?><option value="<?php echo ($vo["name"]); ?>"><?php echo ($vo["name"]); ?></option><?php endforeach; endif; ?>
                    </select>
                </td>
            <tr>
        </table>
    </form>
</div>