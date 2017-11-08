<?php if (!defined('THINK_PATH')) exit();?><!-- 报数单生成器  -->
<form style="overflow: hidden;">
    <table cellspacing="6" width="100%" style="margin-left: 10px;margin-top:10px;">
        <tr>
            <td>
                <span style="font-size: 16px;">日期:</span>
                <input type="text"  id="yingshouRevparMgrGeneralviewDateInput"
                       class="easyui-datebox"
                       name="yingshouRevparMgrGeneralviewDateInput"
                       style="font-size: 16px;width:30%;" value="<?php echo ($cdate); ?>"/>
                <span style="font-size: 16px;margin-left:20px;">午别:</span>
                <select name="yingshouRevparMgrGeneralviewApInput" id="yingshouRevparMgrGeneralviewApInput"
                        class="txtBox" style="width:100px;font-size: 14px;">
                    <?php if($searchAp): ?><option value="<?php echo ($cap); ?>"><?php echo ($cap); ?></option><?php endif; ?>
                    <option value="上午">上午</option>
                    <option value="下午">下午</option>
                </select>
                <input type="text" style="width:0px;visibility:hidden;" >
            </td>
        <tr>
    </table>
</form>