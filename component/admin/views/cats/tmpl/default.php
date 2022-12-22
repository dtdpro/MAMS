<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
if (JVersion::MAJOR_VERSION == 3) JHtml::_('bootstrap.tooltip');

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Button\FeaturedButton;
use Joomla\CMS\Button\PublishedButton;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$archived	= $this->state->get('filter.published') == 2 ? true : false;
$trashed	= $this->state->get('filter.published') == -2 ? true : false;
$published = $this->state->get('filter.published');
$ordering  = ($listOrder == 'c.lft');
$saveOrder = ($listOrder == 'c.lft' && strtolower($listDirn) == 'asc');

if ($saveOrder) {
	if (JVersion::MAJOR_VERSION == 3) {
		$saveOrderingUrl = 'index.php?option=com_mams&task=cats.saveOrderAjax&tmpl=component';
		JHtml::_('sortablelist.sortable', 'MAMSCatList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
	} else {
		$saveOrderingUrl = 'index.php?option=com_mams&task=cats.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1';
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
<form action="<?php echo JRoute::_('index.php?option=com_mams&view=cats'); ?>" method="post" name="adminForm" id="adminForm">
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
	
	<table class="adminlist table table-striped" id="MAMSCatList">
		<thead>
			<tr>
                <th width="1%" class="nowrap center hidden-phone">
					<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'c.lft', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
                </th>
                <th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>	
				<th width="1%">
					<?php echo JText::_('JSTATUS'); ?>
				</th>
                <th width="1%">
					<?php echo JText::_('JFEATURED'); ?>
                </th>
				<th>
					<?php echo JHtml::_('grid.sort','COM_MAMS_CAT_HEADING_NAME','c.cat_title', $listDirn, $listOrder); ?>
				</th>		
				<th width="10%">
					<?php echo JHtml::_('grid.sort','COM_MAMS_CAT_ADDED','c.cat_added', $listDirn, $listOrder); ?>
				</th>		
				<th width="10%">
					<?php echo JHtml::_('grid.sort','COM_MAMS_CAT_MODIFIED','c.cat_modified', $listDirn, $listOrder); ?>
				</th>	
				<th width="5%">
					<?php echo JText::_('COM_MAMS_CAT_HEADING_FEATACCESS'); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort','JGRID_HEADING_ACCESS','c.access', $listDirn, $listOrder); ?>
				</th>
				<th width="1%">
					<?php echo JText::_('COM_MAMS_CAT_HEADING_NUMITEMS'); ?>
				</th>
				<th width="1%">
					<?php echo JHtml::_('grid.sort','COM_MAMS_CAT_HEADING_ID','c.cat_id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot><tr><td colspan="10"><?php echo $this->pagination->getListFooter(); ?></td></tr></tfoot>
		<tbody <?php if (JVersion::MAJOR_VERSION == 4) { ?>class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="true"<?php } ?>>
		<?php foreach($this->items as $i => $item):
			$orderkey   = array_search($item->cat_id, $this->ordering[$item->parent_id]);
			?>
            <tr class="row<?php echo $i % 2; ?>" <?php if (JVersion::MAJOR_VERSION == 3) { ?>sortable-group-id="<?php echo $item->parent_id; ?>" <?php } else { ?>data-draggable-group="<?php echo $item->parent_id; ?>"<?php } ?>>
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
		            <?php if ($saveOrder) : ?>
                        <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $orderkey + 1; ?>" />
		            <?php endif; ?>

                </td>
                <td><?php echo JHtml::_('grid.id', $i, $item->cat_id); ?></td>
                <td class="center text-center">
		            <?php
		            if (JVersion::MAJOR_VERSION == 3) {
			            echo JHtml::_('jgrid.published', $item->published, $i, 'cats.', true);
		            } else {
			            $options = [ 'task_prefix' => 'cats.', 'id' => 'state-' . $item->cat_id ];
			            echo ( new PublishedButton() )->render( (int) $item->published, $i, $options );
		            }
		            ?>
                </td>
				<td class="center text-center">
						<?php
						if (JVersion::MAJOR_VERSION == 3) {
							echo JHtml::_('mamsadministrator.featured', $item->cat_featured, $i, true, "cats");
						} else {
							$options = [ 'task_prefix' => 'cats.', 'id' => 'featured-' . $item->cat_id ];
							echo ( new FeaturedButton() )->render( (int) $item->cat_featured, $i, $options );
						}
						?>
				</td>
				<td class="nowrap has-context">
					<div class="pull-left">
						<?php if ($item->level > 0) { echo str_repeat('<span class="gi">&mdash;</span>', $item->level - 1); } ?>
						<a href="<?php echo JRoute::_('index.php?option=com_mams&task=cat.edit&cat_id='.(int) $item->cat_id); ?>">
						<?php echo $this->escape($item->cat_title); ?></a>
						<div class="small">Alias: <?php echo $item->cat_alias; ?></div>
					</div>
				</td>
				<td class="small"><?php echo $item->cat_added; ?></td>
				<td class="small"><?php echo $item->cat_modified; ?></td>
				<td class="small"><?php echo $item->feataccess_level; ?></td>
				<td class="small"><?php echo $item->access_level; ?></td>
				<td class="small"><?php if ($item->cat_items) echo $item->cat_items; else echo 0; ?></td>
				<td><?php echo $item->cat_id; ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

    <?php echo JHtml::_( 'bootstrap.renderModal', 'collapseModal', array( 'title'  => JText::_('COM_MAMS_CAT_BATCH_OPTIONS'), 'footer' => $this->loadTemplate('batch_footer'), ), $this->loadTemplate('batch_body') ); ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>


