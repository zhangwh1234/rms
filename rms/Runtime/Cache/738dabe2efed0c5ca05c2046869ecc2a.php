<?php if (!defined('THINK_PATH')) exit();?><table id="table<?php echo ($actionName); ?>" border="1" cellspacing="1" cellpadding="3" width="100%" class="lvt" align="center">
    <tr class="listHeader">
        <td class="" width="10%">序号</td>
        <td class="listHeader" width="20%">科目摘要</td>
        <td class="listHeader" width="30%">名称</td>
        <td class="listHeader" width="20%">金额</td>
        <td class="listHeader">分公司</td>       
    </tr>
    <?php if(is_array($revparmgr)): foreach($revparmgr as $key=>$entity): $record = $key; ?>
        <?php if($entity[1] == '营收情况'): ?><tr bgcolor="66CCFF"  id="row_<?php echo ($entity_id); ?>">          
                <td class="listColData"  align="center"><?php echo ($key); ?></td>               
                <td class="listColData"  colspan="4" align="center">营收情况</td>                
            </tr>
        <?php elseif($entity[1] == '贷方营收合计'): ?>
            <tr bgcolor="66CCFF"  id="row_<?php echo ($entity_id); ?>">          
                <td class="listColData"  align="center"><?php echo ($key); ?></td>               
                <td class="listColData"  colspan="2" align="center">贷方营收合计</td> 
                <td class="listColData"  colspan="2" align="center"><?php echo ($entity[2]); ?></td>                
            </tr>
        <?php elseif($entity[1] == '借方营收合计'): ?>
            <tr bgcolor="66CCFF"  id="row_<?php echo ($entity_id); ?>">          
                <td class="listColData"  align="center"><?php echo ($key); ?></td>               
                <td class="listColData"  colspan="2" align="center">借方营收合计</td> 
                <td class="listColData"  colspan="2" align="center"><?php echo ($entity[2]); ?></td>                
            </tr>
        <?php else: ?>
            <tr bgcolor="white" onMouseOver="this.className='lvtColDataHover'" onMouseOut="this.className='lvtColData'" ondblclick="alert('row_<?php echo ($record); ?>');" id="row_<?php echo ($entity_id); ?>">          
               <td class="listColData" align="center"><?php echo ($key); ?></td>
              <?php if(is_array($entity)): foreach($entity as $key=>$id): ?><td class="listColData" align="center"><?php echo ($id); ?></td><?php endforeach; endif; ?>
            </tr><?php endif; endforeach; endif; ?>
</table>