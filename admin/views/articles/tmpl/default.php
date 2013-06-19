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
$saveOrder = ($listOrder == 'a.ordering');
$published = $this->state->get('filter.published');
if ($saveOrder) {
	$saveOrderingUrl = 'index.php?option=com_mams&task=articles.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'MAMSArtList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
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
	
	<table class="adminlist table table-striped" id="MAMSArtList">
		<thead>
			<tr>
				<th width="1%" class="nowrap center hidden-phone">
					<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
				</th>
				<th width="20">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>	
				<th width="1%" style="min-width:55px" class="nowrap center">
					<?php echo JHtml::_('grid.sort','JSTATUS','s.published', $listDirn, $listOrder); ?>
				</th>		
				<th>
					<?php echo JHtml::_('grid.sort','COM_MAMS_ARTICLE_HEADING_TITLE','a.art_title', $listDirn, $listOrder); ?>
				</th>		
				<th width="120">
					<?php echo JHtml::_('grid.sort','COM_MAMS_ARTICLE_HEADING_PUBLISHED','a.art_published', $listDirn, $listOrder); ?>
				</th>		
				<th width="100">
					<?php echo JText::_('COM_MAMS_ARTICLE_HEADING_TAGS'); ?>
				</th>
				<th width="100">
					<?php echo JText::_('COM_MAMS_ARTICLE_HEADING_EXTRAS'); ?>
				</th>
				<th width="100">
					<?php echo JText::_('COM_MAMS_ARTICLE_HEADING_REFS'); ?>
				</th>
				<th width="120" class="hidden-phone">
					<?php echo JHtml::_('grid.sort','COM_MAMS_ARTICLE_HEADING_ADDED','a.art_added', $listDirn, $listOrder); ?>
				</th>		
				<th width="120" class="hidden-phone">
					<?php echo JHtml::_('grid.sort','COM_MAMS_ARTICLE_HEADING_MODIFIED','a.art_modified', $listDirn, $listOrder); ?>
				</th>	
				<th width="100">
					<?php echo JText::_('COM_MAMS_ARTICLE_HEADING_SECTION'); ?>
				</th>
				<th width="50">
					<?php echo JText::_('JFEATURED'); ?>
				</th>
				<th width="100">
					<?php echo JHtml::_('grid.sort','JGRID_HEADING_ACCESS','a.access', $listDirn, $listOrder); ?>
				</th>
				<th width="30">
					<?php echo JHtml::_('grid.sort','COM_MAMS_ARTICLE_HEADING_HITS','a.art_hits', $listDirn, $listOrder); ?>
				</th>
				<th width="5">
					<?php echo JHtml::_('grid.sort','COM_MAMS_ARTICLE_HEADING_ID','a.art_id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		
		
		</thead>
		<tfoot><tr><td colspan="15"><?php echo $this->pagination->getListFooter(); ?></td></tr></tfoot>
		<tbody>
		<?php foreach($this->items as $i => $item): ?>
			<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->art_published?>">
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
				<td><?php echo JHtml::_('grid.id', $i, $item->art_id); ?></td>
				<td class="center">
					<div class="btn-group">
						<?php echo JHtml::_('jgrid.published', $item->published, $i, 'articles.', true); ?>
						<?php echo JHtml::_('mamsadministrator.featured', $item->featured, $i, true); ?>
					</div>
				</td>
				<td class="nowrap has-context">
					<div class="pull-left">
						<a href="<?php echo JRoute::_('index.php?option=com_mams&task=article.edit&art_id='.(int) $item->art_id); ?>">
						<?php echo $this->escape($item->art_title); ?></a>
						<div class="small"><a href="<?php echo "/index.php?option=com_mams&view=article&secid=".$item->art_sec.":".$item->sec_alias."&artid=".$item->art_id.":".$item->art_alias; ?>" target="_blank">Internal Link</a>
						<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->art_alias));?></div>
					</div>
					<div class="pull-left">
						<?php
							// Create dropdown items
							JHtml::_('dropdown.edit', $item->art_id, 'article.');
							JHtml::_('dropdown.divider');
							if ($item->published) :
								JHtml::_('dropdown.unpublish', 'cb' . $i, 'articles.');
							else :
								JHtml::_('dropdown.publish', 'cb' . $i, 'articles.');
							endif;
							
							JHtml::_('dropdown.divider');

							if ($item->featured) :
								JHtml::_('dropdown.unfeatured', 'cb' . $i, 'articles.');
							else :
								JHtml::_('dropdown.featured', 'cb' . $i, 'articles.');
							endif;

							JHtml::_('dropdown.divider');

							if ($trashed) :
								JHtml::_('dropdown.untrash', 'cb' . $i, 'articles.');
							else :
								JHtml::_('dropdown.trash', 'cb' . $i, 'articles.');
							endif;

							// Render dropdown list
							echo JHtml::_('dropdown.render');
							?>
					</div>
				</td>
				<td class="small"><?php echo $item->art_published; ?></td>
				<td class="small"><?php 
					//Authors
					echo '<a href="index.php?option=com_mams&view=artauths&filter_article='.$item->art_id.'">Authors ';
					$query = 'SELECT count(*) FROM #__mams_artauth WHERE published >= 1 && aa_art="'.$item->art_id.'"';
					$db->setQuery( $query );
					$num_aa=$db->loadResult();
					echo ' ['.$num_aa.']</a><br />';
					//Categories
					echo '<a href="index.php?option=com_mams&view=artcats&filter_article='.$item->art_id.'">Categories ';
					$query = 'SELECT count(*) FROM #__mams_artcat WHERE published >= 1 && ac_art="'.$item->art_id.'"';
					$db->setQuery( $query );
					$num_ac=$db->loadResult();
					echo ' ['.$num_ac.']</a>';
				?></td>
				<td class="small"><?php 
					//Downloads
					echo '<a href="index.php?option=com_mams&view=artdloads&filter_article='.$item->art_id.'">Downloads ';
					$query = 'SELECT count(*) FROM #__mams_artdl WHERE published >= 1 && ad_art="'.$item->art_id.'"';
					$db->setQuery( $query );
					$num_ad=$db->loadResult();
					echo ' ['.$num_ad.']</a><br />';
					//Media
					echo '<a href="index.php?option=com_mams&view=artmeds&filter_article='.$item->art_id.'">Media ';
					$query = 'SELECT count(*) FROM #__mams_artmed WHERE published >= 1 && am_art="'.$item->art_id.'"';
					$db->setQuery( $query );
					$num_am=$db->loadResult();
					echo ' ['.$num_am.']</a>';
				?></td>
				<td class="small"><?php 
					//Links
					echo '<a href="index.php?option=com_mams&view=artlinks&filter_article='.$item->art_id.'">Links ';
					$query = 'SELECT count(*) FROM #__mams_artlinks WHERE published >= 1 && al_art="'.$item->art_id.'"';
					$db->setQuery( $query );
					$num_al=$db->loadResult();
					echo ' ['.$num_al.']</a>';
				?></td>
				<td class="small hidden-phone"><?php echo $item->art_added; ?></td>
				<td class="small hidden-phone"><?php echo $item->art_modified; ?></td>
				<td class="small"><?php echo $item->sec_name; ?></td>
				<td class="small"><?php echo $item->feataccess_level; ?></td>
				<td class="small"><?php echo $item->access_level; ?></td>
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
		<div class="modal-body">
			<p><?php echo JText::_('COM_MAMS_ARTICLE_BATCH_TIP'); ?></p>
			<div class="control-group">
				<div class="controls">
					<?php echo JHtml::_('batch.access'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<?php 
						echo '<label id="batch-feataccess-lbl" for="batch-feataccess" class="hasTip" title="' . JText::_('COM_MAMS_ARTICLE_BATCH_FEATACCESS_LABEL') . '::'. JText::_('COM_MAMS_ARTICLE_BATCH_FEATACCESS_LABEL_DESC') . '">';
						echo JText::_('COM_MAMS_ARTICLE_BATCH_FEATACCESS_LABEL').'</label>';
						echo JHtml::_('access.assetgrouplist','batch[featassetgroup_id]', '','class="inputbox"',array('title' => JText::_('JLIB_HTML_BATCH_NOCHANGE'),'id' => 'batch-feataccess')); 
					?>
				</div>
			</div>
			<div class="control-group">
				<div class="controls">		
					<?php 
						echo '<label id="batch-section-lbl" for="batch-section" class="hasTip" title="' . JText::_('COM_MAMS_ARTICLE_BATCH_SECTION_LABEL') . '::'. JText::_('COM_MAMS_ARTICLE_BATCH_SECTION_LABEL_DESC') . '">';
						echo JText::_('COM_MAMS_ARTICLE_BATCH_SECTION_LABEL').'</label>';
					?>
					<select name="batch[featsection_id]" class="inputbox" id="featsection_id">
						<option value="*"><?php echo JText::_('COM_MAMS_SELECT_SEC');?></option>
						<?php echo JHtml::_('select.options', MAMSHelper::getSections("article"), 'value', 'text', "");?>
					</select>
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


