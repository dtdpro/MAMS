<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
if (JVersion::MAJOR_VERSION == 3) JHtml::_('bootstrap.tooltip');

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Button\PublishedButton;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$archived	= $this->state->get('filter.published') == 2 ? true : false;
$trashed	= $this->state->get('filter.published') == -2 ? true : false;
$published = $this->state->get('filter.published');

$extension	= $this->escape($this->state->get('filter.extension'));
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
<form action="<?php echo JRoute::_('index.php?option=com_mams&view=dloads'); ?>" method="post" name="adminForm" id="adminForm">
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
	
	<table class="adminlist table table-striped">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>			
				<th width="1%">
					<?php echo JText::_('JSTATUS'); ?>
				</th>	
				<th>
					<?php echo JHtml::_('grid.sort','COM_MAMS_DLOAD_HEADING_NAME','d.dl_fname', $listDirn, $listOrder); ?>
				</th>		
				<th width="20%">
					<?php echo JText::_('COM_MAMS_DLOAD_LOC'); ?>
				</th>
				<th width="5%">
					<?php echo JText::_('COM_MAMS_DLOAD_TYPE'); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort','COM_MAMS_DLOAD_HEADING_ADDED','d.dl_added', $listDirn, $listOrder); ?>
				</th>		
				<th width="10%">
					<?php echo JHtml::_('grid.sort','COM_MAMS_DLOAD_HEADING_MODIFIED','d.dl_modified', $listDirn, $listOrder); ?>
				</th>	
				<th width="5%">
					<?php echo JHtml::_('grid.sort','JGRID_HEADING_ACCESS','d.access', $listDirn, $listOrder); ?>
				</th>
				<th width="1%">
					<?php echo JHtml::_('grid.sort','COM_MAMS_DLOAD_HEADING_ID','d.dl_id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		
		
		</thead>
		<tfoot><tr><td colspan="10"><?php echo $this->pagination->getListFooter(); ?></td></tr></tfoot>
		<tbody>
		<?php foreach($this->items as $i => $item): ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td><?php echo JHtml::_('grid.id', $i, $item->dl_id); ?></td>
				<td class="center text-center">
					<?php
					if (JVersion::MAJOR_VERSION == 3) {
						echo JHtml::_('jgrid.published', $item->published, $i, 'dloads.', true);
					} else {
						$options = [ 'task_prefix' => 'dloads.', 'id' => 'state-' . $item->dl_id ];
						echo ( new PublishedButton() )->render( (int) $item->published, $i, $options );
					}
					?>
				</td>
				<td class="nowrap has-context">
					<div class="pull-left">
						<a href="<?php echo JRoute::_('index.php?option=com_mams&task=dload.edit&dl_id='.(int) $item->dl_id.'&extension='.$extension); ?>">
						<?php echo $this->escape($item->dl_fname); ?></a>
						<div class="small"><?php echo 'Download '.$this->escape($item->dl_lname);?></div>
					</div>
				</td>
				<td class="small"><?php echo $item->dl_loc; ?></td>
				<td class="small"><?php 
					switch ($item->dl_type) {
						case 'mp3': echo 'MP3'; break;
						case 'pdf': echo 'PDF'; break;
						case 'zip': echo 'ZIP'; break;
					} 
				
				?></td>
				<td class="small"><?php echo $item->dl_added; ?></td>
				<td class="small"><?php echo $item->dl_modified; ?></td>
				<td class="small"><?php echo $item->access_level; ?></td>
				<td><?php echo $item->dl_id; ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="extension" value="<?php echo $extension;?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>


