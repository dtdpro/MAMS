<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$saveOrder = ($listOrder == 'a.ordering' || $listOrder == 'a.art_published');
$ordering = ($listOrder == 'a.ordering' || $listOrder == 'a.art_published');
$published = $this->state->get('filter.published');
$db =& JFactory::getDBO();
?>
<form action="<?php echo JRoute::_('index.php?option=com_mams&view=articles'); ?>" method="post" name="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_MAMS_SEARCH_IN_TITLE'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_sec" class="inputbox" onchange="this.form.submit()">
				<option value="*"><?php echo JText::_('COM_MAMS_SELECT_SEC');?></option>
				<?php echo JHtml::_('select.options', MAMSHelper::getSections("article"), 'value', 'text', $this->state->get('filter.sec'));?>
			</select>
			<select name="filter_published" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true);?>
			</select>
			<select name="filter_access" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_ACCESS');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'));?>
			</select>

		</div>
	</fieldset>
	
	<div class="clr"> </div>
	
	<table class="adminlist">
		<thead>
			<tr>
				<th width="5">
					<?php echo JHtml::_('grid.sort','COM_MAMS_ARTICLE_HEADING_ID','a.art_id', $listDirn, $listOrder); ?>
				</th>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
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
				<th width="120">
					<?php echo JHtml::_('grid.sort','COM_MAMS_ARTICLE_HEADING_ADDED','a.art_added', $listDirn, $listOrder); ?>
				</th>		
				<th width="120">
					<?php echo JHtml::_('grid.sort','COM_MAMS_ARTICLE_HEADING_MODIFIED','a.art_modified', $listDirn, $listOrder); ?>
				</th>	
				<th width="100">
					<?php echo JText::_('COM_MAMS_ARTICLE_HEADING_SECTION'); ?>
				</th>
				<th width="120">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
					<?php echo JHtml::_('grid.order', $this->items, 'filesave.png', 'articles.saveorder'); ?>
				</th>
				<th width="50">
					<?php echo JText::_('JFEATURED'); ?>
				</th>
				<th width="50">
					<?php echo JText::_('JPUBLISHED'); ?>
				</th>
				<th width="100">
					<?php echo JText::_('JGRID_HEADING_ACCESS'); ?>
				</th>
				<th width="30">
					<?php echo JText::_('COM_MAMS_ARTICLE_HEADING_HITS'); ?>
				</th>
			</tr>
		
		
		</thead>
		<tfoot><tr><td colspan="14"><?php echo $this->pagination->getListFooter(); ?></td></tr></tfoot>
		<tbody>
		<?php foreach($this->items as $i => $item): ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td><?php echo $item->art_id; ?></td>
				<td><?php echo JHtml::_('grid.id', $i, $item->art_id); ?></td>
				<td>
					<a href="<?php echo JRoute::_('index.php?option=com_mams&task=article.edit&art_id='.(int) $item->art_id); ?>">
					<?php echo $this->escape($item->art_title); ?></a>
					<p class="smallsub"><a href="<?php echo "/index.php?option=com_mams&view=article&secid=".$item->art_sec.":".$item->sec_alias."&artid=".$item->art_id.":".$item->art_alias; ?>" target="_blank">Internal Link</a>
					<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->art_alias));?></p>
				</td>
				<td><?php echo $item->art_published; ?></td>
				<td><?php 
					//Authors
					echo '<a href="index.php?option=com_mams&view=artarticles&filter_article='.$item->art_id.'">Authors ';
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
				<td><?php 
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
				<td><?php echo $item->art_added; ?></td>
				<td><?php echo $item->art_modified; ?></td>
				<td><?php echo $item->sec_name; ?></td>
				<td class="order">
				<?php if ($saveOrder) :?>
					<?php if ($listDirn == 'asc') : ?>
						<span><?php echo $this->pagination->orderUpIcon($i, ($item->art_sec == @$this->items[$i-1]->art_sec && $item->art_published == @$this->items[$i-1]->art_published), 'articles.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
						<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->art_sec == @$this->items[$i+1]->art_sec && $item->art_published == @$this->items[$i+1]->art_published), 'articles.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
					<?php elseif ($listDirn == 'desc') : ?>
						<span><?php echo $this->pagination->orderUpIcon($i, ($item->art_sec == @$this->items[$i-1]->art_sec && $item->art_published == @$this->items[$i-1]->art_published), 'articles.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
						<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->art_sec == @$this->items[$i+1]->art_sec && $item->art_published == @$this->items[$i+1]->art_published), 'articles.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
					<?php endif; ?>
				<?php endif; ?>
				<?php $disabled = $saveOrder ? '' : 'disabled="disabled"'; ?>
				<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
				
				</td>
				<td class="center"><?php echo JHtml::_('mamsadministrator.featured', $item->featured, $i, true).'<br />'.$item->feataccess_level; ?></td>
				<td class="center"><?php echo JHtml::_('jgrid.published', $item->published, $i, 'articles.', true);?></td>
				<td><?php echo $item->access_level; ?></td>
				<td><?php echo $item->art_hits; ?></td>
				
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<fieldset class="batch">
	<legend><?php echo JText::_('COM_MAMS_ARTICLE_BATCH_OPTIONS');?></legend>
		<p><?php echo JText::_('COM_MAMS_ARTICLE_BATCH_TIP'); ?></p>
		<?php 
			//Access Level
			echo JHtml::_('batch.access');
			
			//Featured Access Level
			echo '<br /><br /><br /><label id="batch-feataccess-lbl" for="batch-feataccess" class="hasTip" title="' . JText::_('COM_MAMS_ARTICLE_BATCH_FEATACCESS_LABEL') . '::'. JText::_('COM_MAMS_ARTICLE_BATCH_FEATACCESS_LABEL_DESC') . '">';
			echo JText::_('COM_MAMS_ARTICLE_BATCH_FEATACCESS_LABEL').'</label>';
			echo JHtml::_('access.assetgrouplist','batch[featassetgroup_id]', '','class="inputbox"',array('title' => JText::_('JLIB_HTML_BATCH_NOCHANGE'),'id' => 'batch-feataccess'));
		
		echo '<br /><br /><br /><label id="batch-section-lbl" for="batch-section" class="hasTip" title="' . JText::_('COM_MAMS_ARTICLE_BATCH_SECTION_LABEL') . '::'. JText::_('COM_MAMS_ARTICLE_BATCH_SECTION_LABEL_DESC') . '">';
		echo JText::_('COM_MAMS_ARTICLE_BATCH_SECTION_LABEL').'</label>';
			
		//Section
		?>
		<select name="batch[featsection_id]" class="inputbox">
			<option value="*"><?php echo JText::_('COM_MAMS_SELECT_SEC');?></option>
			<?php echo JHtml::_('select.options', MAMSHelper::getSections("article"), 'value', 'text', "");?>
		</select>
	<br /><br /><br />
		<button type="submit" onclick="Joomla.submitbutton('article.batch');">
			<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
		</button>
		<button type="button" onclick="document.id('batch-access').value='';">
			<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
		</button>
	</fieldset>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>


