<?php if (!defined('THINK_PATH')) exit();?><!-- 打印纸张选择 -->
<div>
    <form style="overflow:hidden;">
        <table cellspacing="6" width="100%" style="margin-left: 10px;margin-top:10px;">
            <tr>
                <td>
                    <span style="font-size: 16px;">打印纸张选择</span>
                    <select style="width: 100px;" id="selectprintpage"
                            name="selectprintpage">
                        <?php if(empty($rmsPrintPageName)): ?><option value=""></option>
                        <?php else: ?>
                            <option value="<?php echo ($rmsPrintPage); ?>"><?php echo ($rmsPrintPageName); ?></option><?php endif; ?>

                        <option value="30lian">三联单</option>
                        <option value="60hot">60宽热敏</option>
                        <option value="80hot">80款热敏</option>
                    </select>
                </td>
            <tr>
        </table>
    </form>
</div>