<?php
defined('_JEXEC') or die;
use Joomla\CMS\Layout\LayoutHelper;
$published = (int) $this->state->get('filter.published');
?>

<div class="container-fluid">
	<div class="row-fluid">
		<div class="control-group span6">
			<div class="controls">
				<?php echo LayoutHelper::render('joomla.html.batch.access', []); ?>
			</div>
		</div>
	</div>
	<div class="row-fluid">
		<div class="control-group span6">
			<div class="controls">
				<?php
				echo '<label id="batch-addfeataccess-lbl" for="batch-addfeataccess" class="hasTip" title="' . JText::_('COM_MAMS_CAT_BATCH_ADDFEATACCESS_LABEL') . '::'. JText::_('COM_MAMS_CAT_BATCH_ADDFEATACCESS_LABEL_DESC') . '">';
				echo JText::_('COM_MAMS_CAT_BATCH_ADDFEATACCESS_LABEL').'</label>';
				echo JHtml::_('access.assetgrouplist','batch[addfeatassetgroup_id]', '','class="inputbox form-select"',array('title' => JText::_('JLIB_HTML_BATCH_NOCHANGE'),'id' => 'batch-addfeataccess'));
				?>
			</div>
		</div>
		<div class="control-group span6">
			<div class="controls">
				<?php
				echo '<label id="batch-rmvfeataccess-lbl" for="batch-rmvfeataccess" class="hasTip" title="' . JText::_('COM_MAMS_CAT_BATCH_RMVFEATACCESS_LABEL') . '::'. JText::_('COM_MAMS_CAT_BATCH_RMVFEATACCESS_LABEL_DESC') . '">';
				echo JText::_('COM_MAMS_CAT_BATCH_RMVFEATACCESS_LABEL').'</label>';
				echo JHtml::_('access.assetgrouplist','batch[rmvfeatassetgroup_id]', '','class="inputbox form-select"',array('title' => JText::_('JLIB_HTML_BATCH_NOCHANGE'),'id' => 'batch-rmvfeataccess'));
				?>
			</div>
		</div>
	</div>
</div>