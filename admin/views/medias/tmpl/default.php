<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');


JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$extension	= $this->escape($this->state->get('filter.extension'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_mams&view=medias'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_MAMS_SEARCH_MEDIA'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
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
					<?php echo JText::_('COM_MAMS_MEDIA_HEADING_ID'); ?>
				</th>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
				</th>			
				<th>
					<?php echo JHtml::_('grid.sort','COM_MAMS_MEDIA_HEADING_NAME','m.med_inttitle', $listDirn, $listOrder); ?>
				</th>		
				<th>
					<?php echo JText::_('COM_MAMS_MEDIA_LOC'); ?>
				</th>
				<th width="100">
					<?php echo JText::_('COM_MAMS_MEDIA_TYPE'); ?>
				</th>
				<th width="120">
					<?php echo JHtml::_('grid.sort','COM_MAMS_MEDIA_ADDED','m.med_added', $listDirn, $listOrder); ?>
				</th>	
				<th width="120">
					<?php echo JHtml::_('grid.sort','COM_MAMS_MEDIA_MODIFIED','m.med_modified', $listDirn, $listOrder); ?>
				</th>		
				<th width="50">
					<?php echo JText::_('JFEATURED'); ?>
				</th>
				<th width="100">
					<?php echo JText::_('JPUBLISHED'); ?>
				</th>
				<th width="100">
					<?php echo JText::_('JGRID_HEADING_ACCESS'); ?>
				</th>
			</tr>
		
		
		</thead>
		<tfoot><tr><td colspan="10"><?php echo $this->pagination->getListFooter(); ?></td></tr></tfoot>
		<tbody>
		<?php foreach($this->items as $i => $item): ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td><?php echo $item->med_id; ?></td>
				<td><?php echo JHtml::_('grid.id', $i, $item->med_id); ?></td>
				<td>
					<a href="<?php echo JRoute::_('index.php?option=com_mams&task=media.edit&med_id='.(int) $item->med_id.'&extension='.$extension); ?>">
					<?php echo $this->escape($item->med_inttitle); ?></a>
					<p class="smallsub"><?php echo $this->escape($item->med_exttitle);?></p>
				</td>
				<td><?php echo $item->med_file; ?></td>
				<td><?php 
					switch ($item->med_type) {
						case 'vids': echo 'Streaming Video'; break;
						case 'vid': echo 'Video'; break;
						case 'aud': echo 'Audio'; break;
					} 
				
				?></td>
				<td><?php echo $item->med_added; ?></td>
				<td><?php echo $item->med_modified; ?></td>
				<td class="center"><?php echo JHtml::_('mamsadministrator.featured', $item->featured, $i, true,'medias').'<br />'.$item->feataccess_level; ?></td>
				<td class="center"><?php echo JHtml::_('jgrid.published', $item->published, $i, 'medias.', true);?></td>
				<td><?php echo $item->access_level; ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="extension" value="<?php echo $extension;?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>


