<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('bootstrap.tooltip');
JHtml::_('dropdown.init');

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

$user	= JFactory::getUser();
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$archived	= $this->state->get('filter.published') == 2 ? true : false;
$trashed	= $this->state->get('filter.published') == -2 ? true : false;
$ordering  = ($listOrder == 's.lft');
$saveOrder = ($listOrder == 's.lft' && strtolower($listDirn) == 'asc');
$published = $this->state->get('filter.published');
if ($saveOrder) {
    if (JVersion::MAJOR_VERSION == 3) {
        $saveOrderingUrl = 'index.php?option=com_mams&task=secs.saveOrderAjax&tmpl=component';
        JHtml::_('sortablelist.sortable', 'MAMSSecList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
    } else {
	    $saveOrderingUrl = 'index.php?option=com_mams&task=secs.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1';
	    HTMLHelper::_('draggablelist.draggable');
    }
}

$db = JFactory::getDBO();
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
    <?php
    // Search tools bar
    echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
    ?>

    <div class="clearfix"> </div>
	
	<table class="adminlist table table-striped" id="MAMSSecList">
		<thead>
			<tr>
				<th width="1%" class="nowrap center hidden-phone">
					<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 's.lft', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
				</th>	
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>	
				<th width="1%">
					<?php echo JHtml::_('grid.sort','JSTATUS','s.published', $listDirn, $listOrder); ?>
				</th>		
				<th>
					<?php echo JHtml::_('grid.sort','COM_MAMS_SEC_HEADING_NAME','s.sec_name', $listDirn, $listOrder); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort','COM_MAMS_SEC_HEADING_TYPE','s.sec_type', $listDirn, $listOrder); ?>
				</th>		
				<th width="10%" class="hidden-phone">
					<?php echo JHtml::_('grid.sort','COM_MAMS_SEC_ADDED','s.sec_added', $listDirn, $listOrder); ?>
				</th>		
				<th width="10%" class="hidden-phone">
					<?php echo JHtml::_('grid.sort','COM_MAMS_SEC_MODIFIED','s.sec_modified', $listDirn, $listOrder); ?>
				</th>
				<th width="5%" class="hidden-phone">
					<?php echo JHtml::_('grid.sort','JGRID_HEADING_ACCESS','s.access', $listDirn, $listOrder); ?>
				</th>
				<th width="1%">
					<?php echo JText::_('COM_MAMS_SEC_HEADING_NUMITEMS'); ?>
				</th>
				<th width="1%">
					<?php echo JHtml::_('grid.sort','COM_MAMS_SEC_HEADING_ID','s.sec_id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		
		
		</thead>
		<tfoot><tr><td colspan="10"><?php echo $this->pagination->getListFooter(); ?></td></tr></tfoot>
		<tbody <?php if (JVersion::MAJOR_VERSION == 4) { ?>class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="true"<?php } ?>>
		<?php foreach($this->items as $i => $item):
			$orderkey   = array_search($item->sec_id, $this->ordering[$item->parent_id]);
			$canEdit = $user->authorise('core.edit','com_mams.sec.' . $item->sec_id);
			$canChange = $user->authorise('core.edit.state','com_mams.sec.' . $item->sec_id);
			?>
			<tr class="row<?php echo $i % 2; ?>" <?php if (JVersion::MAJOR_VERSION == 3) { ?>sortable-group-id="<?php echo $item->sec_type.$item->parent_id; ?>" <?php } else { ?>data-draggable-group="<?php echo $item->sec_type.$item->parent_id; ?>"<?php } ?>>
				<td class="order nowrap center hidden-phone">
					<?php if ($canChange) :
						$disableClassName = '';
						$disabledLabel	  = '';
						if (!$saveOrder) :
							$disabledLabel    = JText::_('JORDERINGDISABLED');
							$disableClassName = 'inactive tip-top';
						endif; ?>
						<span class="sortable-handler hasTooltip <?php echo $disableClassName?>" title="<?php echo $disabledLabel?>">
							<i class="icon-menu"></i>
						</span>
					<?php else : ?>
						<span class="sortable-handler inactive">
						<i class="icon-menu"></i>
						</span>
					<?php endif; ?>
					<?php if ($canChange && $saveOrder) : ?>
						<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $orderkey + 1; ?>" />
					<?php endif; ?>

				</td>
				<td><?php echo JHtml::_('grid.id', $i, $item->sec_id); ?></td>
				<td class="center text-center">
					<div class="btn-group">
						<?php echo JHtml::_('jgrid.published', $item->published, $i, 'secs.', true);?>
					</div>
				</td>
				<td class="nowrap has-context">
					<div class="pull-left">
						<?php echo str_repeat('<span class="gi">&mdash;</span>', $item->level - 1) ?>
						<?php if ($canEdit) : ?>
							<a href="<?php echo JRoute::_('index.php?option=com_mams&task=sec.edit&sec_id='.(int) $item->sec_id); ?>">
							<?php echo $this->escape($item->sec_name); ?></a>
						<?php else : ?>
							<?php echo $this->escape($item->sec_name); ?>
						<?php endif; ?>
						<div class="small">Alias: <?php echo $item->sec_alias; ?></div>
					</div>
				</td>
				<td class="small"><?php 
					switch ($item->sec_type) {
						case "author": echo "Author"; break; 
						case "article": echo "Article"; break;
						case "image": echo "Image"; break;
					}
				?></td>
				<td class="small hidden-phone"><?php echo $item->sec_added; ?></td>
				<td class="small hidden-phone"><?php echo $item->sec_modified; ?></td>
				<td class="small hidden-phone"><?php echo $item->access_level; ?></td>
				<td class="small"><?php 
					if ($item->sec_type == "article") $query = 'SELECT count(*) FROM #__mams_articles WHERE art_sec='.$item->sec_id;
					if ($item->sec_type == "author") $query = 'SELECT count(*) FROM #__mams_authors WHERE auth_sec='.$item->sec_id;
					if ($item->sec_type == "image") $query = 'SELECT count(*) FROM #__mams_images WHERE img_sec='.$item->sec_id;
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


