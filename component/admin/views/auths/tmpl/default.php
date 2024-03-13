<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
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
$saveOrder = ($listOrder == 'a.ordering');
$published = $this->state->get('filter.published');
if ($saveOrder) {
	if (JVersion::MAJOR_VERSION == 3) {
		$saveOrderingUrl = 'index.php?option=com_mams&task=auths.saveOrderAjax&tmpl=component';
		JHtml::_('sortablelist.sortable', 'MAMSCatList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
	} else {
		$saveOrderingUrl = 'index.php?option=com_mams&task=auths.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1';
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
<form action="<?php echo JRoute::_('index.php?option=com_mams&view=auths'); ?>" method="post" name="adminForm" id="adminForm">
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
	
	<table class="adminlist table table-striped" id="MAMSAuthList">
		<thead>
			<tr>
				<th width="1%" class="nowrap center hidden-phone">
					<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
				</th>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>	
				<th width="1%" class="nowrap center">
					<?php echo JText::_('JSTATUS'); ?>
				</th>			
				<th>
					<?php echo JHtml::_('grid.sort','COM_MAMS_AUTH_HEADING_FNAME','a.auth_fname', $listDirn, $listOrder); ?>
					<?php echo JHtml::_('grid.sort','COM_MAMS_AUTH_HEADING_LNAME','a.auth_lname', $listDirn, $listOrder); ?>
				</th>		
				<th width="10%">
					<?php echo JHtml::_('grid.sort','COM_MAMS_AUTH_ADDED','a.auth_added', $listDirn, $listOrder); ?>
				</th>		
				<th width="10%">
					<?php echo JHtml::_('grid.sort','COM_MAMS_AUTH_MODIFIED','a.auth_modified', $listDirn, $listOrder); ?>
				</th>	
				<th width="5%">
					<?php echo JHtml::_('grid.sort','JGRID_HEADING_ACCESS','a.access', $listDirn, $listOrder); ?>
				</th>
				<th width="1%">
					<?php echo JText::_('COM_MAMS_CAT_HEADING_NUMITEMS'); ?>
				</th>
				<th width="1%">
					<?php echo JHtml::_('grid.sort','COM_MAMS_AUTH_HEADING_ID','a.auth_id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		
		
		</thead>
		<tfoot><tr><td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td></tr></tfoot>
        <tbody <?php if (JVersion::MAJOR_VERSION == 4 || JVersion::MAJOR_VERSION == 5) { ?>class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="true"<?php } ?>>
		<?php foreach($this->items as $i => $item): ?>
            <tr class="row<?php echo $i % 2; ?>" <?php if (JVersion::MAJOR_VERSION == 3) { ?>sortable-group-id="<?php echo $item->auth_sec; ?>" <?php } else { ?>data-draggable-group="<?php echo $item->auth_sec; ?>"<?php } ?>>
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
				<td><?php echo JHtml::_('grid.id', $i, $item->auth_id); ?></td>
				<td class="center text-center">
					<?php
					if (JVersion::MAJOR_VERSION == 3) {
						echo JHtml::_('jgrid.published', $item->published, $i, 'auths.', true);
					} else {
						$options = [ 'task_prefix' => 'auths.', 'id' => 'state-' . $item->auth_id ];
						echo ( new PublishedButton() )->render( (int) $item->published, $i, $options );
					}
					?>
				</td>
				<td class="nowrap has-context">
					<div class="pull-left">
						<a href="<?php echo JRoute::_('index.php?option=com_mams&task=auth.edit&auth_id='.(int) $item->auth_id); ?>">
						<?php echo $this->escape($item->auth_fname).(($item->auth_mi) ? " ".$this->escape($item->auth_mi) : "")." ".$this->escape($item->auth_lname).(($item->auth_titles) ? ", ".$this->escape($item->auth_titles) : ""); ?></a>
						<div class="small">Section: <?php echo $item->sec_name;?></div>
					</div>
				</td>
				<td class="small"><?php echo $item->auth_added; ?></td>
				<td class="small"><?php echo $item->auth_modified; ?></td>
				<td class="small"><?php echo $item->access_level; ?></td>
				<td class="small"><?php 
					$query = 'SELECT count(*) FROM #__mams_artauth WHERE aa_auth='.$item->auth_id;
					$db->setQuery( $query );
					$num=$db->loadResult();
					echo $num;
				?></td>
				<td><?php echo $item->auth_id; ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<?php echo JHtml::_( 'bootstrap.renderModal', 'collapseModal', array( 'title'  => JText::_('COM_MAMS_ARTICLE_BATCH_OPTIONS'), 'footer' => $this->loadTemplate('batch_footer'), ), $this->loadTemplate('batch_body') ); ?>

        <input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>


