<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('behavior.tooltip');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$saveOrder = $listOrder == 's.ordering';
$ordering = ($listOrder == 's.ordering');
$db =& JFactory::getDBO();
?>
<form action="<?php echo JRoute::_('index.php?option=com_mams&view=secs'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			
		</div>
		<div class="filter-select fltrt">
			<select name="filter_published" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true);?>
			</select>
			<select name="filter_access" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_ACCESS');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'));?>
			</select>

		</div>
	</fieldset>
	
	<div class="clr"> </div>
	
	<table class="adminlist table table-striped">
		<thead>
			<tr>
				<th width="5">
					<?php echo JText::_('COM_MAMS_SEC_HEADING_ID'); ?>
				</th>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
				</th>			
				<th>
					<?php echo JHtml::_('grid.sort','COM_MAMS_SEC_HEADING_NAME','s.sec_name', $listDirn, $listOrder); ?>
				</th>		
				<th width="80">
					<?php echo JHtml::_('grid.sort','COM_MAMS_SEC_HEADING_TYPE','s.sec_type', $listDirn, $listOrder); ?>
				</th>		
				<th width="120">
					<?php echo JHtml::_('grid.sort','COM_MAMS_SEC_ADDED','s.sec_added', $listDirn, $listOrder); ?>
				</th>		
				<th width="120">
					<?php echo JHtml::_('grid.sort','COM_MAMS_SEC_MODIFIED','s.sec_modified', $listDirn, $listOrder); ?>
				</th>	
				<th width="100">
					<?php echo JText::_('JPUBLISHED'); ?>
				</th>
				<th width="100">
					<?php echo JText::_('JGRID_HEADING_ACCESS'); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ORDERING', 's.ordering', $listDirn, $listOrder); ?>
					<?php echo JHtml::_('grid.order', $this->items, 'filesave.png', 'secs.saveorder'); ?>
				</th>
				<th width="50">
					<?php echo JText::_('COM_MAMS_SEC_HEADING_NUMITEMS'); ?>
				</th>
			</tr>
		
		
		</thead>
		<tfoot><tr><td colspan="10"><?php echo $this->pagination->getListFooter(); ?></td></tr></tfoot>
		<tbody>
		<?php foreach($this->items as $i => $item): ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td><?php echo $item->sec_id; ?></td>
				<td><?php echo JHtml::_('grid.id', $i, $item->sec_id); ?></td>
				<td>
					<a href="<?php echo JRoute::_('index.php?option=com_mams&task=sec.edit&sec_id='.(int) $item->sec_id); ?>">
					<?php echo $this->escape($item->sec_name); ?></a>
					<p class="smallsub"><?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->sec_alias));?></p>
				</td>
				<td><?php 
					switch ($item->sec_type) {
						case "author": echo "Author"; break; 
						case "article": echo "Article"; break;
					}
				?></td>
				<td><?php echo $item->sec_added; ?></td>
				<td><?php echo $item->sec_modified; ?></td>
				<td class="center"><?php echo JHtml::_('jgrid.published', $item->published, $i, 'secs.', true);?></td>
				<td><?php echo $item->access_level; ?></td>
				<td class="order">
				<?php if ($saveOrder) :?>
					<?php if ($listDirn == 'asc') : ?>
						<span><?php echo $this->pagination->orderUpIcon($i, ($item->sec_type == @$this->items[$i-1]->sec_type), 'secs.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
						<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->sec_type == @$this->items[$i+1]->sec_type), 'secs.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
					<?php elseif ($listDirn == 'desc') : ?>
						<span><?php echo $this->pagination->orderUpIcon($i, ($item->sec_type == @$this->items[$i-1]->sec_type), 'secs.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
						<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->sec_type == @$this->items[$i+1]->sec_type), 'secs.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
					<?php endif; ?>
				<?php endif; ?>
				<?php $disabled = $saveOrder ? '' : 'disabled="disabled"'; ?>
				<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
				
				</td>
				<td><?php 
					if ($item->sec_type == "article") $query = 'SELECT count(*) FROM #__mams_articles WHERE art_sec='.$item->sec_id;
					if ($item->sec_type == "author") $query = 'SELECT count(*) FROM #__mams_authors WHERE auth_sec='.$item->sec_id;
					$db->setQuery( $query );
					$num=$db->loadResult();
					echo $num;
				?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>


