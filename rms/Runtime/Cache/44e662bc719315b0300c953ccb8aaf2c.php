<?php if (!defined('THINK_PATH')) exit();?><table id="table<?php echo ($actionName); ?>" border="1" cellspacing="1" cellpadding="3" width="100%" class="lvt" align="center">
    <tr class="listHeader">
        <td class="" width="10%">序号</td>
        <td class="listHeader" width="10%">代码</td>
        <td class="listHeader" width="20%">产品名称</td>
        <td class="listHeader" width="10%">数量</td>
        <td class="listHeader" width="10%">单价</td>
        <td class="listHeader" width="20%">金额</td>
        <td class="listHeader" width="20%">分公司</td>
    </tr>
    <?php if(is_array($products)): foreach($products as $key=>$entity): $record = $key; ?>
        <tr bgcolor="white" onMouseOver="this.className='lvtColDataHover'" onMouseOut="this.className='lvtColData'" ondblclick="alert('row_<?php echo ($record); ?>');" id="row_<?php echo ($entity_id); ?>">
             <td class="listColData" align="center"><?php echo ($key+1); ?></td>
            <?php if(is_array($entity)): foreach($entity as $key=>$id): ?><td class="listColData" align="center"><?php echo ($id); ?></td><?php endforeach; endif; ?>
        </tr><?php endforeach; endif; ?>
</table>
</br>
<table border="1" cellspacing="1" cellpadding="3" width="100%" class="lvt" align="center">
    <tr>
        <td class="listColData">汇总</td>
        <td class="listColData">数量:<?php echo ($totalnumber); ?></td>
        <td class="listColData">金额:<?php echo ($totalmoney); ?></td>
    </tr>
</table>