<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
if (JVersion::MAJOR_VERSION == 3) {
    JHtml::_('bootstrap.tooltip');
	JHtml::_('formbehavior.chosen', 'select');
}

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Button\FeaturedButton;
use Joomla\CMS\Button\PublishedButton;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

$app	= JFactory::getApplication();
$user	= JFactory::getUser();
$userId	= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$archived	= $this->state->get('filter.published') == 2 ? true : false;
$trashed	= $this->state->get('filter.published') == -2 ? true : false;
$saveOrder = ($listOrder == 'a.ordering' && $this->state->get('filter.sec'));
if ($this->state->get('filter.cat') || $this->state->get('filter.tag') || $this->state->get('filter.auth') || $this->state->get('filter.feataccess') || $this->state->get('filter.access') || $this->state->get('filter.state')) {
    $saveOrder = false;
}
$published = $this->state->get('filter.published');
if ($saveOrder) {
	if (JVersion::MAJOR_VERSION == 3) {
		$saveOrderingUrl = 'index.php?option=com_mams&task=articles.saveOrderAjax&tmpl=component';
		JHtml::_('sortablelist.sortable', 'MAMSArtList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
	} else {
		$saveOrderingUrl = 'index.php?option=com_mams&task=articles.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1';
		HTMLHelper::_('draggablelist.draggable');
	}
}
$sortFields = $this->getSortFields();
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
<form action="<?php echo JRoute::_('index.php?option=com_mams&view=articles'); ?>" method="post" name="adminForm" id="adminForm">
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

<?php if (empty($this->items)) : ?>
    <div class="alert alert-no-items">
        <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
    </div>
<?php else : ?>

	<table class="adminlist table table-striped" id="MAMSArtList">
		<thead>
			<tr>
				<th width="1%" class="nowrap center hidden-phone">
					<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
				</th>
                <?php if($app->get('debug') == '1') { ?>
                    <th width="1%"></th>
                <?php } ?>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>	
				<th width="1%" style="min-width:55px" class="nowrap center">
					<?php echo JText::_('JSTATUS'); ?>
				</th>
                <th width="1%" style="min-width:55px" class="nowrap center">
					<?php echo JText::_('JFEATURED'); ?>
                </th>
                <th>
					<?php echo JHtml::_('grid.sort','COM_MAMS_ARTICLE_HEADING_TITLE','a.art_title', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort','COM_MAMS_ARTICLE_HEADING_PUBLISH_ON','a.art_publish_up', $listDirn, $listOrder); ?> - 
					<?php echo JHtml::_('grid.sort','COM_MAMS_ARTICLE_HEADING_PUBLISH_DOWN','a.art_publish_down', $listDirn, $listOrder); ?>
				</th>	
				<th width="10%" class="hidden-phone">
					<?php echo JHtml::_('grid.sort','COM_MAMS_ARTICLE_HEADING_ADDED','a.art_added', $listDirn, $listOrder); ?>
				</th>		
				<th width="10%" class="hidden-phone">
					<?php echo JHtml::_('grid.sort','COM_MAMS_ARTICLE_HEADING_MODIFIED','a.art_modified', $listDirn, $listOrder); ?>
				</th>	
				<th width="5%">
					<?php echo JHtml::_('grid.sort','JGRID_HEADING_ACCESS','a.access', $listDirn, $listOrder); ?><br />
					<?php echo JText::_('COM_MAMS_ARTICLE_HEADING_FEATACCESS'); ?>
				</th>
				<th width="1%">
					<?php echo JHtml::_('grid.sort','COM_MAMS_ARTICLE_HEADING_HITS','a.art_hits', $listDirn, $listOrder); ?>
				</th>
				<th width="1%">
					<?php echo JHtml::_('grid.sort','COM_MAMS_ARTICLE_HEADING_ID','a.art_id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		
		
		</thead>
		<tfoot><tr><td colspan="15"><?php echo $this->pagination->getListFooter(); ?></td></tr></tfoot>
        <tbody <?php if (JVersion::MAJOR_VERSION == 4) { ?>class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="true"<?php } ?>>
		<?php foreach($this->items as $i => $item): 
			$canCreate = $user->authorise('core.create', 'com_mams.sec.'.$item->art_sec);
			$canEdit = $user->authorise('core.edit', 'com_mams.article.'.$item->art_id);
			$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
			$canEditOwn = $user->authorise('core.edit.own', 'com_mams.article.'.$item->art_id) && $item->art_added_by == $userId;
			$canEditDrilldowns = $user->authorise('core.edit.drilldowns', 'com_mams.article.'.$item->art_id);
			$canFeature = $user->authorise('core.edit.featured', 'com_mams.article.'.$item->art_id);
			$canChange = $user->authorise('core.edit.state', 'com_mams.article.'.$item->art_id) && $canCheckin;
			?>
            <tr class="row<?php echo $i % 2; ?>" <?php if (JVersion::MAJOR_VERSION == 3) { ?>sortable-group-id="<?php echo $item->art_publish_up; ?>" <?php } else { ?>data-draggable-group="<?php echo $item->art_publish_up; ?>"<?php } ?>>
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
						<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering;?>" class="width-20 text-area-order " /> 
					<?php else : ?>	
						<span class="sortable-handler inactive" >
						<i class="icon-menu"></i>
						</span>
					<?php endif; ?>

				</td>
				<?php if($app->get('debug') == '1') { ?>
                    <td><?php echo $item->ordering; ?></td>
				<?php } ?>
				<td><?php echo JHtml::_('grid.id', $i, $item->art_id); ?></td>
                <td class="center text-center">
		            <?php
		            if (JVersion::MAJOR_VERSION == 3) {
			            echo JHtml::_('jgrid.published', $item->state, $i, 'articles.', true);
		            } else {
			            $options = [ 'task_prefix' => 'articles.', 'id' => 'state-' . $item->art_id ];
			            echo ( new PublishedButton() )->render( (int) $item->state, $i, $options );
		            }
		            ?>
                </td>
                <td class="center text-center">
		            <?php
		            if (JVersion::MAJOR_VERSION == 3) {
			            echo JHtml::_('mamsadministrator.featured', $item->featured, $i, true, "articles");
		            } else {
			            $options = [ 'task_prefix' => 'articles.', 'id' => 'featured-' . $item->art_id ];
			            echo ( new FeaturedButton() )->render( (int) $item->featured, $i, $options );
		            }
		            ?>
                </td>
				<td class="has-context">
					<div class="pull-left">
					
						<?php if ($item->checked_out) : ?>
							<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'articles.', $canCheckin); ?>
						<?php endif; ?>
						<?php if ($canEdit || $canEditOwn) : ?>
							<a href="<?php echo JRoute::_('index.php?option=com_mams&task=article.edit&art_id=' . $item->art_id); ?>" title="<?php echo JText::_('JACTION_EDIT'); ?>">
							<?php echo $this->escape($item->art_title); ?></a>
						<?php else : ?>
							<span title="<?php echo JText::sprintf('JFIELD_ALIAS_LABEL', $this->escape($item->art_alias)); ?>"><?php echo $this->escape($item->art_title); ?></span>
						<?php endif; ?>

                        <div class="small">
                            <strong>Section:</strong> <?php echo $item->sec_name; ?>
                            <?php if (count($item->cats)) : ?>
                            <strong>Category:</strong> <?php
                                $itemcats = array();
                                foreach ($item->cats as $c) {
                                    if (isset($this->cats[$c])) $itemcats[] = $this->cats[$c];
                                }
                                echo implode(", ",$itemcats);
                                endif;
                            ?>
	                        <?php if (count($item->tags)) : ?>
                                <br /><strong>Tag:</strong> <?php
		                        $itemtags = array();
		                        foreach ($item->tags as $t) {
			                        if (isset($this->tags[$t])) $itemtags[] = $this->tags[$t];
		                        }
		                        echo implode(", ",$itemtags);
	                        endif;
	                        ?>
							<?php if (count($item->authors)) : ?>
								<br /><strong>Author:</strong> <?php
								$itemauths = array();
								foreach ($item->authors as $a) {
									if (isset($this->authors[$a])) $itemauths[] = $this->authors[$a];
								}
								echo implode("; ",$itemauths);
							endif;
							?>
                        </div>
						<div class="small"><strong>Alias:</strong> <?php echo $item->art_alias; ?></div>
					</div>
					
				</td>
				<td class="small"><strong>Start:</strong> <?php echo $item->art_publish_up; ?><br /><strong>End:</strong> <?php echo $item->art_publish_down; ?></td>
				<td class="small hidden-phone"><?php echo $item->art_added.'<br />'.$item->adder; ?></td>
				<td class="small hidden-phone"><?php echo $item->art_modified.'<br />'.$item->modifier; ?></td>
				<td class="small"><?php echo $item->access_level.'<br />'.$item->feataccess_level; ?></td>
				<td class="small"><?php echo $item->art_hits; ?></td>
				<td><?php echo $item->art_id; ?></td>
				
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>

		<?php echo JHtml::_( 'bootstrap.renderModal', 'collapseModal', array( 'title'  => JText::_('COM_MAMS_CAT_BATCH_OPTIONS'), 'footer' => $this->loadTemplate('batch_footer'), ), $this->loadTemplate('batch_body') ); ?>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</div>
</form>


