<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$archived	= $this->state->get('filter.published') == 2 ? true : false;
$trashed	= $this->state->get('filter.published') == -2 ? true : false;
$published = $this->state->get('filter.published');
$sortFields = $this->getSortFields();

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
	
	<table class="adminlist table table-striped" id="MAMSCatList">
		<thead>
			<tr>
                <th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>	
				<th width="1%">
					<?php echo JHtml::_('grid.sort','JSTATUS','c.published', $listDirn, $listOrder); ?>
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
					<?php echo JHtml::_('grid.sort','COM_MAMS_TAG_HEADING_NUMITEMS','tag_items', $listDirn, $listOrder); ?>
				</th>
				<th width="1%">
					<?php echo JHtml::_('grid.sort','COM_MAMS_TAG_HEADING_ID','c.tag_id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		
		
		</thead>
		<tfoot><tr><td colspan="10"><?php echo $this->pagination->getListFooter(); ?></td></tr></tfoot>
		<tbody>
		<?php foreach($this->items as $i => $item):


			?>
            <tr class="row<?php echo $i % 2; ?>">
                <td><?php echo JHtml::_('grid.id', $i, $item->tag_id); ?></td>
				<td class="center">
					<div class="btn-group">
						<?php echo JHtml::_('jgrid.published', $item->published, $i, 'tags.', true);?>
						<?php echo JHtml::_('mamsadministrator.featured', $item->tag_featured, $i, true, "tags"); ?>
						<?php
							// Create dropdown items
							if ($item->published) :
								JHtml::_('actionsdropdown.unpublish', 'cb' . $i, 'tags');
							else :
								JHtml::_('actionsdropdown.publish', 'cb' . $i, 'tags');
							endif;
							
							JHtml::_('actionsdropdown.divider');
							
							if ($item->tag_featured) :
								JHtml::_('actionsdropdown.unfeature', 'cb' . $i, 'tags');
							else :
								JHtml::_('actionsdropdown.feature', 'cb' . $i, 'tags');
							endif;
							
							JHtml::_('actionsdropdown.divider');

							if ($trashed) :
								JHtml::_('actionsdropdown.untrash', 'cb' . $i, 'tags');
							else :
								JHtml::_('actionsdropdown.trash', 'cb' . $i, 'tags');
							endif;

							// Render dropdown list
							echo JHtml::_('actionsdropdown.render');
							?>
					</div>
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
	<div class="modal hide fade" id="collapseModal">
		<div class="modal-header">
			<button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
			<h3><?php echo JText::_('COM_MAMS_TAG_BATCH_OPTIONS');?></h3>
		</div>
		<div class="modal-body modal-batch">
			<p><?php echo JText::_('COM_MAMS_TAG_BATCH_TIP'); ?></p>
			<div class="control-group">
				<div class="controls">
					<?php echo JHtml::_('batch.access'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<?php 
						echo '<label id="batch-addfeataccess-lbl" for="batch-addfeataccess" class="hasTip" title="' . JText::_('COM_MAMS_TAG_BATCH_ADDFEATACCESS_LABEL') . '::'. JText::_('COM_MAMS_TAG_BATCH_ADDFEATACCESS_LABEL_DESC') . '">';
						echo JText::_('COM_MAMS_TAG_BATCH_ADDFEATACCESS_LABEL').'</label>';
						echo JHtml::_('access.assetgrouplist','batch[addfeatassetgroup_id]', '','class="inputbox"',array('title' => JText::_('JLIB_HTML_BATCH_NOCHANGE'),'id' => 'batch-addfeataccess'));
					?>
				</div>
			</div>
            <div class="control-group">
                <div class="controls">
					<?php
					echo '<label id="batch-rmvfeataccess-lbl" for="batch-rmvfeataccess" class="hasTip" title="' . JText::_('COM_MAMS_TAG_BATCH_RMVFEATACCESS_LABEL') . '::'. JText::_('COM_MAMS_TAG_BATCH_RMVFEATACCESS_LABEL_DESC') . '">';
					echo JText::_('COM_MAMS_TAG_BATCH_RMVFEATACCESS_LABEL').'</label>';
					echo JHtml::_('access.assetgrouplist','batch[rmvfeatassetgroup_id]', '','class="inputbox"',array('title' => JText::_('JLIB_HTML_BATCH_NOCHANGE'),'id' => 'batch-rmvfeataccess'));
					?>
                </div>
            </div>
		</div>
		<div class="modal-footer">	
			<button class="btn" type="button" onclick="document.id('batch-access').value='';document.id('batch-feataccess').value='';document.id('featsection_id').value='';" data-dismiss="modal">
				<?php echo JText::_('JCANCEL'); ?>
			</button>
			<button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('tag.batch');">
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


