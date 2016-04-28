<?php
<div class="list" id="list{$moduleName}">
<input id="{$moduleName}Action" type="hidden" value="listview" />
<table  border="0" cellspacing="1" cellpadding="3" width="100%" class="lvt"   align="center">
<tr class="listHeader">
<td class=""><input class="listColCheck" type="checkbox"  name="selectall" ></td>
<foreach name="listHeader" item="header">
<td class="listHeader">{$Think.lang.$header}</td>
</foreach>
<td class="listHeader" >操作</td>
</tr>
<volist id="entity"  name="listEntries" key='ad'>
<assign name='record' value="$key" />
<tr bgcolor="white" onMouseOver="this.className='lvtColDataHover'" onMouseOut="this.className='lvtColData'" ondblclick="updateTab('__URL__/detailview/record/{$record}/returnAction/{$returnAction}');" id="row_{$entity_id}">
<volist name="entity" id="id">
<if condition="$key eq $moduleId">
<td class="listColCheck" width="2%"><input  type="checkbox" name="selected_id" id="{$record}" value= '{$record}'></td>
<elseif condition="$key eq $list_link_field" />
<td><a class="small" href="javascript:void();" onclick="updateTab('__URL__/detailview/record/{$record}/returnAction/listview');">{$id}</a></td>
<else />
<td class="listColData">{$id}</td>
</if>
</volist>
<td><center><a href="javascript:void(0);" onclick="updateTab('__URL__/detailview/record/{$record}/returnAction/{$returnAction}');">查看</a><span >&nbsp&nbsp|&nbsp&nbsp</span>
<a href="javascript:void(0);" onclick="updateTab('__URL__/editview/record/{$record}/returnAction/listview');">编辑</a>&nbsp&nbsp|&nbsp&nbsp</span>
<a href="javascript:void(0);" onclick="deleteRecord(&apos;{:U('delete',array('record'=> $record,'returnAction' => $returnAction))}&apos;);"">删除</a></center></td>
            </tr>
        </volist>
    </table>
</div>
<div class="pages">{$page}</div>
