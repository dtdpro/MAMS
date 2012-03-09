<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('behavior.tooltip');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_mams&view=dloads'); ?>" method="post" name="adminForm">
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
	
	<table class="adminlist">
		<thead>
			<tr>
				<th width="5">
					<?php echo JText::_('COM_MAMS_DLOAD_HEADING_ID'); ?>
				</th>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
				</th>			
				<th>
					<?php echo JHtml::_('grid.sort','COM_MAMS_DLOAD_HEADING_NAME','d.dl_fname', $listDirn, $listOrder); ?>
				</th>		
				<th>
					<?php echo JText::_('COM_MAMS_DLOAD_LOC'); ?>
				</th>
				<th width="100">
					<?php echo JText::_('COM_MAMS_DLOAD_TYPE'); ?>
				</th>
				<th width="120">
					<?php echo JHtml::_('grid.sort','COM_MAMS_DLOAD_HEADING_ADDED','d.dl_added', $listDirn, $listOrder); ?>
				</th>		
				<th width="120">
					<?php echo JHtml::_('grid.sort','COM_MAMS_DLOAD_HEADING_MODIFIED','d.dl_modified', $listDirn, $listOrder); ?>
				</th>		
				<th width="100">
					<?php echo JText::_('JPUBLISHED'); ?>
				</th>
				<th width="100">
					<?php echo JText::_('JGRID_HEADING_ACCESS'); ?>
				</th>
			</tr>
		
		
		</thead>
		<tfoot><tr><td colspan="8"><?php echo $this->pagination->getListFooter(); ?></td></tr></tfoot>
		<tbody>
		<?php foreach($this->items as $i => $item): ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td><?php echo $item->dl_id; ?></td>
				<td><?php echo JHtml::_('grid.id', $i, $item->dl_id); ?></td>
				<td>
					<a href="<?php echo JRoute::_('index.php?option=com_mams&task=dload.edit&dl_id='.(int) $item->dl_id); ?>">
					<?php echo $this->escape($item->dl_fname); ?></a>
					<p class="smallsub"><?php echo '(<span>Link name</span>: Download '.$this->escape($item->dl_lname).')';?></p>
				</td>
				<td><?php echo $item->dl_loc; ?></td>
				<td><?php 
					switch ($item->dl_type) {
						case 'mp3': echo 'MP3'; break;
						case 'pdf': echo 'PDF'; break;
					} 
				
				?></td>
				<td><?php echo $item->dl_added; ?></td>
				<td><?php echo $item->dl_modified; ?></td>
				<td class="center"><?php echo JHtml::_('jgrid.published', $item->published, $i, 'dloads.', true);?></td>
				<td><?php echo $item->access_level; ?></td>
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


