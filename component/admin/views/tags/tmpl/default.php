<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
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

?>
<form action="<?php echo JRoute::_('index.php?option=com_mams&view=tags'); ?>" method="post" name="adminForm" id="adminForm">
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
					<?php echo JHtml::_('grid.sort','COM_MAMS_TAG_HEADING_NAME','c.tag_title', $listDirn, $listOrder); ?>
				</th>		
				<th width="10%">
					<?php echo JHtml::_('grid.sort','COM_MAMS_TAG_ADDED','c.tag_added', $listDirn, $listOrder); ?>
				</th>		
				<th width="10%">
					<?php echo JHtml::_('grid.sort','COM_MAMS_TAG_MODIFIED','c.tag_modified', $listDirn, $listOrder); ?>
				</th>	
				<th width="5%">
					<?php echo JText::_('COM_MAMS_CAT_HEADING_FEATACCESS'); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort','JGRID_HEADING_ACCESS','c.access', $listDirn, $listOrder); ?>
				</th>
				<th width="1%">
					<?php echo JText::_('COM_MAMS_TAG_HEADING_NUMITEMS'); ?>
				</th>
				<th width="1%">
					<?php echo JHtml::_('grid.sort','COM_MAMS_TAG_HEADING_ID','c.tag_id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		
		
		</thead>
		<tfoot><tr><td colspan="10"><?php echo $this->pagination->getListFooter(); ?></td></tr></tfoot>
		<tbody>
		<?php foreach($this->items as $i => $item): ?>
            <tr class="row<?php echo $i % 2; ?>">
                <td><?php echo JHtml::_('grid.id', $i, $item->tag_id); ?></td>
                <td class="center text-center">

		            <?php
		            if (JVersion::MAJOR_VERSION == 3) {
			            echo JHtml::_('jgrid.published', $item->published, $i, 'tags.', true);
		            } else {
			            $options = [ 'task_prefix' => 'tags.', 'id' => 'state-' . $item->tag_id ];
			            echo ( new PublishedButton() )->render( (int) $item->published, $i, $options );
		            }
		            ?>
                </td>
                <td class="center text-center">
		            <?php
		            if (JVersion::MAJOR_VERSION == 3) {
			            echo JHtml::_('mamsadministrator.featured', $item->tag_featured, $i, true, "tags");
		            } else {
			            $options = [ 'task_prefix' => 'tags.', 'id' => 'featured-' . $item->tag_id ];
			            echo ( new FeaturedButton() )->render( (int) $item->tag_featured, $i, $options );
		            }
		            ?>
                </td>

                <td class="nowrap has-context">
					<div class="pull-left">
						<a href="<?php echo JRoute::_('index.php?option=com_mams&task=tag.edit&tag_id='.(int) $item->tag_id); ?>">
						<?php echo $this->escape($item->tag_title); ?></a>
						<div class="small">Alias: <?php echo $item->tag_alias; ?></div>
					</div>
				</td>
				<td class="small"><?php echo $item->tag_added; ?></td>
				<td class="small"><?php echo $item->tag_modified; ?></td>
				<td class="small"><?php echo $item->feataccess_level; ?></td>
				<td class="small"><?php echo $item->access_level; ?></td>
				<td class="small"><?php if ($item->tag_items) echo $item->tag_items; else echo 0; ?></td>
				<td><?php echo $item->tag_id; ?></td>
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


