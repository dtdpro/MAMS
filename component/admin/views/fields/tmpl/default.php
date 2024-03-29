<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
if (JVersion::MAJOR_VERSION == 3) JHtml::_('bootstrap.tooltip');

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Layout\LayoutHelper;

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$archived	= $this->state->get('filter.published') == 2 ? true : false;
$trashed	= $this->state->get('filter.published') == -2 ? true : false;
$saveOrder = ($listOrder == 'f.ordering');
$published = $this->state->get('filter.published');
if ($saveOrder) {
	if (JVersion::MAJOR_VERSION == 3) {
		$saveOrderingUrl = 'index.php?option=com_mams&task=fields.saveOrderAjax&tmpl=component';
		JHtml::_('sortablelist.sortable', 'MAMSFieldList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
	} else {
		$saveOrderingUrl = 'index.php?option=com_mams&task=fields.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1';
		HTMLHelper::_('draggablelist.draggable');
	}
}

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
<form action="<?php echo JRoute::_('index.php?option=com_mams&view=fields'); ?>" method="post" name="adminForm" id="adminForm">
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
	
	<table class="adminlist table table-striped" id="MAMSFieldList">
		<thead>
			<tr>
				<th width="1%" class="nowrap center hidden-phone">
					<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'f.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
				</th>	
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>	
				<th width="1%">
					<?php echo JText::_('JSTATUS'); ?>
				</th>	
				<th width="15%">
					<?php echo JText::_('COM_MAMS_FIELD_HEADING_GROUP'); ?>
				</th>			
				<th>
					<?php echo JText::_('COM_MAMS_FIELD_HEADING_TITLE'); ?>
                </th>
                <th width="1%" class="text-center">
					<?php echo "Article"; ?>
                </th>
                <th width="1%" class="text-center">
					<?php echo "Preview"; ?>
                </th>
                <th width="1%" class="text-center">
					<?php echo "List"; ?>
                </th>
                <th width="15%">
					<?php echo JText::_('COM_MAMS_FIELD_HEADING_NAME'); ?>
				</th>		
				<th width="15%">
					<?php echo JText::_('COM_MAMS_FIELD_HEADING_TYPE'); ?>
				</th>		
				<th width="5%" class="hidden-phone">
					<?php echo JText::_('JGRID_HEADING_ACCESS'); ?>
				</th>
				<th width="1%">
					<?php echo JText::_('COM_MAMS_FIELD_HEADING_ID'); ?>
				</th>
			</tr>
		
		
		</thead>
		<tfoot><tr><td colspan="12"><?php echo $this->pagination->getListFooter(); ?></td></tr></tfoot>
        <tbody <?php if (JVersion::MAJOR_VERSION == 4 || JVersion::MAJOR_VERSION == 5) { ?>class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="true"<?php } ?>>
		<?php foreach($this->items as $i => $item): ?>
            <tr class="row<?php echo $i % 2; ?>" <?php if (JVersion::MAJOR_VERSION == 3) { ?>sortable-group-id="field" <?php } else { ?>data-draggable-group="field"<?php } ?>>
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
				<td><?php echo JHtml::_('grid.id', $i, $item->field_id); ?></td>
				<td class="center text-center"><?php
					if ($item->field_id >= 50) echo JHtml::_('jgrid.published', $item->published, $i, 'fields.', true);
					else echo '<a class="btn btn-micro disabled jgrid"  title="Protected extension"><i class="icon-lock"></i></a>';
				?></td>
				<td><?php 
					echo $this->escape($item->group_title);
				?></td>
				<td class="nowrap has-context">
					<a href="<?php echo JRoute::_('index.php?option=com_mams&task=field.edit&field_id='.(int) $item->field_id); ?>">
					<?php echo $this->escape($item->field_title); ?></a>
				</td>
                <td class="center text-center">
		            <?php if ($item->field_show_page) echo '<span class="icon-check"></span>'; ?>
                </td>
                <td class="center text-center">
		            <?php if ($item->field_show_preview) echo '<span class="icon-check"></span>'; ?>
                </td>
                <td class="center text-center">
		            <?php if ($item->field_show_list) echo '<span class="icon-check"></span>'; ?>
                </td>
				<td class="small"><?php 
					echo $this->escape($item->field_name);
				?></td>
				<td class="small"><?php 
					switch ($item->field_type) {
						case "textfield": echo "Text Field"; break;
						case "textbox": echo "Text Box"; break;
						case "editor": echo "Text Editor"; break;
						case "auths": echo "Authors"; break;
						case "dloads": echo "Downloads"; break;
						case "media": echo "Media"; break;
						case "links": echo "Links"; break;
						case "pubinfo": echo "Publish Info"; break;
						case "related": echo "Related Items"; break;
						case "images": echo "Image Gallery"; break;
						case "artauthedlink": echo "Articles by these Authors"; break;
						case "rendera": echo "Render A"; break;
						case "module": echo "Module Position: ".$item->field_rssname; break;
						case "tags": echo "Tags"; break;
						case "title": echo "Title"; break;
						case "desc": echo "Article Desc."; break;
						case "body": echo "Article Body/Preview"; break;
					}
				?></td>
				<td class="small hidden-phone"><?php echo $item->access_level; ?></td>
				<td><?php echo $item->field_id; ?></td>
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


