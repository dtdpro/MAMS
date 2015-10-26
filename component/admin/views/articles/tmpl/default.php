<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

$app	= JFactory::getApplication();
$user	= JFactory::getUser();
$userId	= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$archived	= $this->state->get('filter.published') == 2 ? true : false;
$trashed	= $this->state->get('filter.published') == -2 ? true : false;
$saveOrder = ($listOrder == 'a.ordering');
$published = $this->state->get('filter.published');
if ($saveOrder) {
	$saveOrderingUrl = 'index.php?option=com_mams&task=articles.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'MAMSArtList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
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
	<div id="filter-bar" class="btn-toolbar">
		<div class="filter-search btn-group pull-left">
			<label class="element-invisible" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_MAMS_SEARCH_IN_TITLE'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_MAMS_SEARCH_IN_TITLE'); ?>" />
		</div>
		<div class="btn-group pull-left">
			<button class="btn hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
            <button class="btn hasTooltip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="jQuery('#filter_search').val('');this.form.submit();"><i class="icon-remove"></i></button>
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
	
	<table class="adminlist table table-striped" id="MAMSArtList">
		<thead>
			<tr>
				<th width="1%" class="nowrap center hidden-phone">
					<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
				</th>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>	
				<th width="1%" style="min-width:55px" class="nowrap center">
					<?php echo JHtml::_('grid.sort','JSTATUS','s.published', $listDirn, $listOrder); ?>
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
		<tbody>
		<?php foreach($this->items as $i => $item): 
			$canCreate = $user->authorise('core.create', 'com_mams.sec.'.$item->art_sec);
			$canEdit = $user->authorise('core.edit', 'com_mams.article.'.$item->art_id);
			$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
			$canEditOwn = $user->authorise('core.edit.own', 'com_mams.article.'.$item->art_id) && $item->art_added_by == $userId;
			$canEditDrilldowns = $user->authorise('core.edit.drilldowns', 'com_mams.article.'.$item->art_id);
			$canFeature = $user->authorise('core.edit.featured', 'com_mams.article.'.$item->art_id);
			$canChange = $user->authorise('core.edit.state', 'com_mams.article.'.$item->art_id) && $canCheckin;
			?>
			<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->art_publish_up?>">
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
				<td><?php echo JHtml::_('grid.id', $i, $item->art_id); ?></td>
				<td class="center">
					<div class="btn-group">
						<?php echo JHtml::_('jgrid.published', $item->state, $i, 'articles.', $canChange); ?>
						<?php echo JHtml::_('mamsadministrator.featured', $item->featured, $i, $canFeature); ?>
						<?php echo JHtml::_('mamsadministrator.drilldowns',$i, $canEditDrilldowns); ?>
						<?php
							// Create dropdown items
							if ($canChange) :
								if ($item->state) :
									JHtml::_('actionsdropdown.unpublish', 'cb' . $i, 'articles');
								else :
									JHtml::_('actionsdropdown.publish', 'cb' . $i, 'articles');
								endif;
								
								JHtml::_('actionsdropdown.divider');
								
								if ($canFeature) :
									if ($item->featured) :
										JHtml::_('actionsdropdown.unfeature', 'cb' . $i, 'articles');
									else :
										JHtml::_('actionsdropdown.feature', 'cb' . $i, 'articles');
									endif;
									
									JHtml::_('actionsdropdown.divider');
								endif;
		
								if ($trashed) :
									JHtml::_('actionsdropdown.untrash', 'cb' . $i, 'articles');
								else :
									JHtml::_('actionsdropdown.trash', 'cb' . $i, 'articles');
								endif;
							endif;
							// Render dropdown list
							if ($canEdit || $canEditOwn || $canChange) :
								echo JHtml::_('actionsdropdown.render');
							endif;
							?>
					</div>
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
                                    $itemcats[] = $this->cats[$c];
                                }
                                echo implode(", ",$itemcats);
                                endif;
                            ?>
							<?php if (count($item->authors)) : ?>
								<br /><strong>Author:</strong> <?php
								$itemauths = array();
								foreach ($item->authors as $a) {
									$itemauths[] = $this->authors[$a];
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
	<div class="modal hide fade" id="collapseModal">
		<div class="modal-header">
			<button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
			<h3><?php echo JText::_('COM_MAMS_ARTICLE_BATCH_OPTIONS');?></h3>
		</div>
		<div class="modal-body modal-batch">
			<p><?php echo JText::_('COM_MAMS_ARTICLE_BATCH_TIP'); ?></p>
			<div class="row-fluid">
                <div class="control-group span6">
                    <div class="controls">
                        <?php echo JHtml::_('batch.access'); ?>
                    </div>
                </div>
                <div class="control-group span6">
                    <div class="controls">
                        <?php
                            echo '<label id="batch-feataccess-lbl" for="batch-feataccess" class="hasTip" title="' . JText::_('COM_MAMS_ARTICLE_BATCH_FEATACCESS_LABEL') . '::'. JText::_('COM_MAMS_ARTICLE_BATCH_FEATACCESS_LABEL_DESC') . '">';
                            echo JText::_('COM_MAMS_ARTICLE_BATCH_FEATACCESS_LABEL').'</label>';
                            echo JHtml::_('access.assetgrouplist','batch[featassetgroup_id]', '','class="inputbox"',array('title' => JText::_('JLIB_HTML_BATCH_NOCHANGE'),'id' => 'batch-feataccess'));
                        ?>
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="control-group span6">
                    <div class="controls">
                        <?php
                            echo '<label id="batch-section-lbl" for="featsection_id" class="hasTip" title="' . JText::_('COM_MAMS_ARTICLE_BATCH_SECTION_LABEL') . '::'. JText::_('COM_MAMS_ARTICLE_BATCH_SECTION_LABEL_DESC') . '">';
                            echo JText::_('COM_MAMS_ARTICLE_BATCH_SECTION_LABEL').'</label>';
                        ?>
                        <select name="batch[featsection_id]" class="inputbox" id="featsection_id">
                            <option value="*"><?php echo JText::_('COM_MAMS_SELECT_SEC');?></option>
                            <?php echo JHtml::_('select.options', MAMSHelper::getSections("article"), 'value', 'text', "");?>
                        </select>
                    </div>
                </div>
				<div class="control-group span6">
					<div class="controls">
						<?php
						echo '<label id="batch-section-lbl" for="batch-stratdate" class="hasTip" title="' . JText::_('COM_MAMS_ARTICLE_BATCH_STARTDATE_LABEL') . '::'. JText::_('COM_MAMS_ARTICLE_BATCH_STARTDATE_DESC') . '">';
						echo JText::_('COM_MAMS_ARTICLE_BATCH_STARTDATE_LABEL').'</label>';
						?>
						<?php echo JHtml::_('calendar',null,'batch[batch-startdate]','batch-startdate','%Y-%m-%d'); ?>

					</div>
				</div>
            </div>
            <div class="row-fluid">
				<div class="control-group span6">
					<div class="controls">
						<?php
						echo '<label id="batch-section-lbl" for="batch-addcat" class="hasTip" title="' . JText::_('COM_MAMS_ARTICLE_BATCH_ADDCAT_LABEL') . '::'. JText::_('COM_MAMS_ARTICLE_BATCH_ADDCAT_DESC') . '">';
						echo JText::_('COM_MAMS_ARTICLE_BATCH_ADDCAT_LABEL').'</label>';
						?>
						<select name="batch[batch-addcat]" class="inputbox" id="batch-addcat">
							<option value="*"><?php echo JText::_('COM_MAMS_SELECT_Cat');?></option>
							<?php echo JHtml::_('select.options', MAMSHelper::getCats(), 'value', 'text', "");?>
						</select>
					</div>
				</div>
                <div class="control-group span6">
                    <div class="controls">
                        <?php
                        echo '<label id="batch-section-lbl" for="batch-rmvcat" class="hasTip" title="' . JText::_('COM_MAMS_ARTICLE_BATCH_RMVCAT_LABEL') . '::'. JText::_('COM_MAMS_ARTICLE_BATCH_RMVCAT_DESC') . '">';
                        echo JText::_('COM_MAMS_ARTICLE_BATCH_RMVCAT_LABEL').'</label>';
                        ?>
                        <select name="batch[batch-rmvcat]" class="inputbox" id="batch-rmvcat">
                            <option value="*"><?php echo JText::_('COM_MAMS_SELECT_CAT');?></option>
                            <?php echo JHtml::_('select.options', MAMSHelper::getCats(), 'value', 'text', "");?>
                        </select>
                    </div>
                </div>
            </div>
			<div class="row-fluid">
				<div class="control-group span6">
					<div class="controls">
						<?php
						echo '<label id="batch-section-lbl" for="batch-addauth" class="hasTip" title="' . JText::_('COM_MAMS_ARTICLE_BATCH_ADDAUTH_LABEL') . '::'. JText::_('COM_MAMS_ARTICLE_BATCH_ADDAUTH_DESC') . '">';
						echo JText::_('COM_MAMS_ARTICLE_BATCH_ADDAUTH_LABEL').'</label>';
						?>
						<select name="batch[batch-addauth]" class="inputbox" id="batch-addauth">
							<option value="*"><?php echo JText::_('COM_MAMS_SELECT_AUTHOR');?></option>
							<?php echo JHtml::_('select.options', MAMSHelper::getAuths(), 'value', 'text', "");?>
						</select>
					</div>
				</div>
				<div class="control-group span6">
					<div class="controls">
						<?php
						echo '<label id="batch-section-lbl" for="batch-addauth" class="hasTip" title="' . JText::_('COM_MAMS_ARTICLE_BATCH_RMVAUTH_LABEL') . '::'. JText::_('COM_MAMS_ARTICLE_BATCH_RMVAUTH_DESC') . '">';
						echo JText::_('COM_MAMS_ARTICLE_BATCH_RMVAUTH_LABEL').'</label>';
						?>
						<select name="batch[batch-rmvauth]" class="inputbox" id="batch-rmvauth">
							<option value="*"><?php echo JText::_('COM_MAMS_SELECT_AUTHOR');?></option>
							<?php echo JHtml::_('select.options', MAMSHelper::getAuths(), 'value', 'text', "");?>
						</select>
					</div>
				</div>
			</div>
        </div>
		<div class="modal-footer">	
			<button class="btn" type="button" onclick="document.id('batch-access').value='';document.id('batch-feataccess').value='';document.id('featsection_id').value='';" data-dismiss="modal">
				<?php echo JText::_('JCANCEL'); ?>
			</button>
			<button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('article.batch');">
				<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
			</button>
		</div>
	</div>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</div>
</form>


