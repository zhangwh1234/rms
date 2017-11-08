<?php if (!defined('THINK_PATH')) exit();?><!-- 报数单生成器  -->
<form style="overflow: hidden;">
    <table cellspacing="6" width="100%" style="margin-left: 10px;margin-top:10px;">
        <tr>
            <td>
                <span style="font-size: 16px;">日期:</span>
                <input type="text"  id="roomservicebillGeneralviewDateInput"
                       class="easyui-datebox"
                       name="roomservicebillGeneralviewDateInput"
                       style="font-size: 16px;width:30%;" value="<?php echo ($getDate); ?>"/>
                <span style="font-size: 16px;margin-left:20px;">午别:</span>
                <select name="roomservicebillGeneralviewApInput" id="roomservicebillGeneralviewApInput"
                        class="txtBox" style="width:100px;font-size: 14px;">
                    <?php if($getAp): ?><option value="<?php echo ($getAp); ?>"><?php echo ($getAp); ?></option><?php endif; ?>
                    <option value="上午">上午</option>
                    <option value="下午">下午</option>
                </select>
                <input type="text" style="width:0px;visibility:hidden;" >
            </td>
        <tr>
    </table>
</form>