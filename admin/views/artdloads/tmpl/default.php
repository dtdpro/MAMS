<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('behavior.tooltip');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$saveOrder = $listOrder == 'a.ordering';
$ordering = ($listOrder == 'a.ordering');
?>
<form action="<?php echo JRoute::_('index.php?option=com_mams&view=artdloads'); ?>" method="post" name="adminForm" id="adminForm">
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
					<?php echo JText::_('COM_MAMS_ARTDLOAD_HEADING_ID'); ?>
				</th>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
				</th>			
				<th>
					<?php echo JText::_('COM_MAMS_ARTDLOAD_HEADING_NAME'); ?>
				</th>		
				
				<th width="100">
					<?php echo JText::_('JPUBLISHED'); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
					<?php echo JHtml::_('grid.order', $this->items, 'filesave.png', 'artdloads.saveorder'); ?>
				</th>
			</tr>
		
		
		</thead>
		<tfoot><tr><td colspan="5"><?php echo $this->pagination->getListFooter(); ?></td></tr></tfoot>
		<tbody>
		<?php foreach($this->items as $i => $item): ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td><?php echo $item->ad_id; ?></td>
				<td><?php echo JHtml::_('grid.id', $i, $item->ad_id); ?></td>
				<td>
					<a href="<?php echo JRoute::_('index.php?option=com_mams&task=artdload.edit&ad_id='.(int) $item->ad_id); ?>">
					<?php echo $this->escape($item->dl_fname); ?></a>
				</td>
				<td class="center"><?php echo JHtml::_('jgrid.published', $item->published, $i, 'artdloads.', true);?></td>
				<td class="order">
				<?php if ($saveOrder) :?>
					<?php if ($listDirn == 'asc') : ?>
						<span><?php echo $this->pagination->orderUpIcon($i, ($item->ad_art == @$this->items[$i-1]->ad_art), 'artdloads.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
						<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->ad_art == @$this->items[$i+1]->ad_art), 'artdloads.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
					<?php elseif ($listDirn == 'desc') : ?>
						<span><?php echo $this->pagination->orderUpIcon($i, ($item->ad_art == @$this->items[$i-1]->ad_art), 'artdloads.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
						<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->ad_art == @$this->items[$i+1]->ad_art), 'artdloads.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
					<?php endif; ?>
				<?php endif; ?>
				<?php $disabled = $saveOrder ? '' : 'disabled="disabled"'; ?>
				<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
				
				</td>
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


