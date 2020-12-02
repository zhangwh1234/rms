<?php if (!defined('THINK_PATH')) exit();?>
<table  id="table<?php echo ($actionName); ?>" border="0" cellspacing="1" cellpadding="3" width="100%" class="lvt"   align="center">
        <tr class="listHeader">
            <td class=""></td>
            <?php if(is_array($listHeader)): foreach($listHeader as $key=>$header): ?><td class="listHeader"><?php echo (L("$header")); ?></td><?php endforeach; endif; ?>
        </tr>
        <?php if(is_array($tongji)): foreach($tongji as $key=>$entity): $record = $key; ?>
        <tr bgcolor="white" onMouseOver="this.className='lvtColDataHover'" onMouseOut="this.className='lvtColData'" ondblclick="alert('row_<?php echo ($record); ?>');" id="row_<?php echo ($entity_id); ?>">
            <td class="listColData"><?php echo ($key); ?></td>
            <?php if(is_array($entity)): foreach($entity as $key=>$id): ?><td class="listColData"><?php echo ($id); ?></td><?php endforeach; endif; ?>               
        </tr><?php endforeach; endif; ?>
</table>