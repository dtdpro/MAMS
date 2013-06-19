<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$archived	= $this->state->get('filter.published') == 2 ? true : false;
$trashed	= $this->state->get('filter.published') == -2 ? true : false;
$saveOrder = ($listOrder == 's.ordering');
$published = $this->state->get('filter.published');
if ($saveOrder) {
	$saveOrderingUrl = 'index.php?option=com_mams&task=secs.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'MAMSSecList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();
$db =& JFactory::getDBO();
?>
<script type="text/javascript">
	Joomla.orderTable = function()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>')
		{
			dirn = 'asc';
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_mams&view=secs'); ?>" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
	<div id="filter-bar" class="btn-toolbar">
		<div class="filter-search btn-group pull-left">
			<label class="element-invisible" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_MAMS_SEARCH_IN_TITLE'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_MAMS_SEARCH_IN_TITLE'); ?>" />
		</div>
		<div class="btn-group pull-left">
			<button class="btn hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
			<button class="btn hasTooltip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
		</div>	
		<div class="btn-group pull-right hidden-phone">
			<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
		<div class="btn-group pull-right hidden-phone">
			<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
			<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
				<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
				<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
				<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
			</select>
		</div>
		<div class="btn-group pull-right">
			<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
			<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
				<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
				<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
			</select>
		</div>
	</div>
	
	<div class="clearfix"> </div>
	
	<table class="adminlist table table-striped" id="MAMSSecList">
		<thead>
			<tr>
				<th width="1%" class="nowrap center hidden-phone">
					<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 's.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
				</th>	
				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
				</th>	
				<th width="1%">
					<?php echo JHtml::_('grid.sort','JSTATUS','s.published', $listDirn, $listOrder); ?>
				</th>		
				<th>
					<?php echo JHtml::_('grid.sort','COM_MAMS_SEC_HEADING_NAME','s.sec_name', $listDirn, $listOrder); ?>
				</th>		
				<th width="80">
					<?php echo JHtml::_('grid.sort','COM_MAMS_SEC_HEADING_TYPE','s.sec_type', $listDirn, $listOrder); ?>
				</th>		
				<th width="120" class="hidden-phone">
					<?php echo JHtml::_('grid.sort','COM_MAMS_SEC_ADDED','s.sec_added', $listDirn, $listOrder); ?>
				</th>		
				<th width="120" class="hidden-phone">
					<?php echo JHtml::_('grid.sort','COM_MAMS_SEC_MODIFIED','s.sec_modified', $listDirn, $listOrder); ?>
				</th>
				<th width="100" class="hidden-phone">
					<?php echo JHtml::_('grid.sort','JGRID_HEADING_ACCESS','s.access', $listDirn, $listOrder); ?>
				</th>
				<th width="50">
					<?php echo JText::_('COM_MAMS_SEC_HEADING_NUMITEMS'); ?>
				</th>
				<th width="5">
					<?php echo JText::_('COM_MAMS_SEC_HEADING_ID'); ?>
				</th>
			</tr>
		
		
		</thead>
		<tfoot><tr><td colspan="10"><?php echo $this->pagination->getListFooter(); ?></td></tr></tfoot>
		<tbody>
		<?php foreach($this->items as $i => $item): ?>
			<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->sec_type; ?>">
				<td class="order nowrap center hidden-phone">
					<?php 
					$disableClassName = '';
					$disabledLabel	  = '';
					if (!$saveOrder) :
						$disabledLabel    = JText::_('JORDERINGDISABLED');
						$disableClassName = 'inactive tip-top';
					endif; ?>
					<span class="sortable-handler hasTooltip <?php echo $disableClassName?>" title="<?php echo $disabledLabel?>">
						<i class="icon-menu"></i>
					</span>
					<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering;?>" class="width-20 text-area-order " />

				</td>
				<td><?php echo JHtml::_('grid.id', $i, $item->sec_id); ?></td>
				<td class="center"><?php echo JHtml::_('jgrid.published', $item->published, $i, 'secs.', true);?></td>
				<td class="nowrap has-context">
					<div class="pull-left">
						<a href="<?php echo JRoute::_('index.php?option=com_mams&task=sec.edit&sec_id='.(int) $item->sec_id); ?>">
						<?php echo $this->escape($item->sec_name); ?></a>
						<div class="small"><?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->sec_alias));?></div>
					</div>
					<div class="pull-left">
						<?php
							// Create dropdown items
							JHtml::_('dropdown.edit', $item->sec_id, 'sec.');
							JHtml::_('dropdown.divider');
							if ($item->published) :
								JHtml::_('dropdown.unpublish', 'cb' . $i, 'secs.');
							else :
								JHtml::_('dropdown.publish', 'cb' . $i, 'secs.');
							endif;
							
							JHtml::_('dropdown.divider');

							if ($trashed) :
								JHtml::_('dropdown.untrash', 'cb' . $i, 'secs.');
							else :
								JHtml::_('dropdown.trash', 'cb' . $i, 'secs.');
							endif;

							// Render dropdown list
							echo JHtml::_('dropdown.render');
							?>
					</div>
				</td>
				<td class="small"><?php 
					switch ($item->sec_type) {
						case "author": echo "Author"; break; 
						case "article": echo "Article"; break;
					}
				?></td>
				<td class="small hidden-phone"><?php echo $item->sec_added; ?></td>
				<td class="small hidden-phone"><?php echo $item->sec_modified; ?></td>
				<td class="small hidden-phone"><?php echo $item->access_level; ?></td>
				<td class="small"><?php 
					if ($item->sec_type == "article") $query = 'SELECT count(*) FROM #__mams_articles WHERE art_sec='.$item->sec_id;
					if ($item->sec_type == "author") $query = 'SELECT count(*) FROM #__mams_authors WHERE auth_sec='.$item->sec_id;
					$db->setQuery( $query );
					$num=$db->loadResult();
					echo $num;
				?></td>
				<td><?php echo $item->sec_id; ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</div>
</form>


