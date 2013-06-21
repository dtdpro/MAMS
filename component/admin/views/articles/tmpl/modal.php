<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('behavior.tooltip');

$function	= JRequest::getCmd('function', 'jSelectMAMSArticle');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_mams&view=articles&layout=modal&tmpl=component'); ?>" method="post" name="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_MAMS_SEARCH_IN_TITLE'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_sec" class="inputbox" onchange="this.form.submit()">
				<option value="*"><?php echo JText::_('COM_MAMS_SELECT_SEC');?></option>
				<?php echo JHtml::_('select.options', MAMSHelper::getSections(), 'value', 'text', $this->state->get('filter.sec'));?>
			</select>
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
					<?php echo JText::_('COM_MAMS_ARTICLE_HEADING_ID'); ?>
				</th>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
				</th>			
				<th>
					<?php echo JHtml::_('grid.sort','COM_MAMS_ARTICLE_HEADING_TITLE','a.art_title', $listDirn, $listOrder); ?>
				</th>		
				<th width="120">
					<?php echo JHtml::_('grid.sort','COM_MAMS_ARTICLE_HEADING_PUBLISHED','a.art_published', $listDirn, $listOrder); ?>
				</th>		
				<th width="100">
					<?php echo JText::_('COM_MAMS_ARTICLE_HEADING_SECTION'); ?>
				</th>
				<th width="100">
					<?php echo JText::_('JGRID_HEADING_ACCESS'); ?>
				</th>
			</tr>
		
		
		</thead>
		<tfoot><tr><td colspan="6"><?php echo $this->pagination->getListFooter(); ?></td></tr></tfoot>
		<tbody>
		<?php foreach($this->items as $i => $item): ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td><?php echo $item->art_id; ?></td>
				<td><?php echo JHtml::_('grid.id', $i, $item->art_id); ?></td>
				<td>
					<a class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->art_id.':'.$item->art_alias; ?>', '<?php echo $this->escape(addslashes($item->art_title)); ?>','<?php echo $item->art_sec.':'.$item->sec_alias; ?>');">
						<?php echo $this->escape($item->art_title); ?></a>
				</td>
				<td><?php echo $item->art_published; ?></td>
				<td><?php echo $item->sec_name; ?></td>
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
