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
		<div class="control-group span12">
			<div class="controls">
				<?php
				echo '<label id="batch-addfeataccess-lbl" for="batch-addfeataccess" class="hasTip" title="' . JText::_('COM_MAMS_CAT_BATCH_ADDFEATACCESS_LABEL') . '::'. JText::_('COM_MAMS_CAT_BATCH_ADDFEATACCESS_LABEL_DESC') . '">';
				echo JText::_('COM_MAMS_CAT_BATCH_ADDFEATACCESS_LABEL').'</label>';
				echo JHtml::_('access.assetgrouplist','batch[addfeatassetgroup_id]', '','class="inputbox form-select"',array('title' => JText::_('JLIB_HTML_BATCH_NOCHANGE'),'id' => 'batch-addfeataccess'));
				?>
			</div>
		</div>

        <div class="control-group span12">
            <div class="controls">
				<?php
				echo '<label id="batch-section-lbl" for="featsection_id" class="hasTip" title="' . JText::_('COM_MAMS_ARTICLE_BATCH_SECTION_LABEL') . '::'. JText::_('COM_MAMS_ARTICLE_BATCH_SECTION_LABEL_DESC') . '">';
				echo JText::_('COM_MAMS_ARTICLE_BATCH_SECTION_LABEL').'</label>';
				?>
                <select name="batch[featsection_id]" class="inputbox form-select" id="featsection_id">
                    <option value="0"><?php echo JText::_('COM_MAMS_SELECT_SEC');?></option>
					<?php echo JHtml::_('select.options', MAMSHelper::getSections("author"), 'value', 'text', "");?>
                </select>
            </div>
        </div>
	</div>
</div>