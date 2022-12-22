<?php
defined('_JEXEC') or die;
use Joomla\CMS\Layout\LayoutHelper;
$published = (int) $this->state->get('filter.published');
?>

<div class="container-fluid">
	<div class="row-fluid <?php if (JVersion::MAJOR_VERSION == 4) { ?>row<?php } ?>">
		<div class="control-group span6 col-md-6">
			<div class="controls">
				<?php echo LayoutHelper::render('joomla.html.batch.access', []); ?>
			</div>
		</div>
        <div class="control-group span6 col-md-6">
            <div class="controls">
	            <?php
	            echo '<label id="batch-feataccess-lbl" for="batch-feataccess" class="hasTip" title="' . JText::_('COM_MAMS_ARTICLE_BATCH_FEATACCESS_LABEL') . '::'. JText::_('COM_MAMS_ARTICLE_BATCH_FEATACCESS_LABEL_DESC') . '">';
	            echo JText::_('COM_MAMS_ARTICLE_BATCH_FEATACCESS_LABEL').'</label>';
	            echo JHtml::_('access.assetgrouplist','batch[featassetgroup_id]', '','class="inputbox form-select"',array('title' => JText::_('JLIB_HTML_BATCH_NOCHANGE'),'id' => 'batch-feataccess'));
	            ?>
            </div>
        </div>
	</div>
	<div class="row-fluid <?php if (JVersion::MAJOR_VERSION == 4) { ?>row<?php } ?>">
		<div class="control-group span6 col-md-6">
			<div class="controls">
				<?php
				echo '<label id="batch-section-lbl" for="featsection_id" class="hasTip" title="' . JText::_('COM_MAMS_ARTICLE_BATCH_SECTION_LABEL') . '::'. JText::_('COM_MAMS_ARTICLE_BATCH_SECTION_LABEL_DESC') . '">';
				echo JText::_('COM_MAMS_ARTICLE_BATCH_SECTION_LABEL').'</label>';
				?>
                <select name="batch[featsection_id]" class="inputbox form-select" id="featsection_id">
                    <option value="0"><?php echo JText::_('COM_MAMS_SELECT_SEC');?></option>
					<?php echo JHtml::_('select.options', MAMSHelper::getSections("article"), 'value', 'text', "");?>
                </select>
			</div>
		</div>
		<div class="control-group span6 col-md-6">
			<div class="controls">
				<?php
				echo '<label id="batch-section-lbl" for="batch-stratdate" class="hasTip" title="' . JText::_('COM_MAMS_ARTICLE_BATCH_STARTDATE_LABEL') . '::'. JText::_('COM_MAMS_ARTICLE_BATCH_STARTDATE_DESC') . '">';
				echo JText::_('COM_MAMS_ARTICLE_BATCH_STARTDATE_LABEL').'</label>';
				?>
				<?php echo JHtml::_('calendar',null,'batch[batch-startdate]','batch-startdate','%Y-%m-%d'); ?>
			</div>
		</div>
	</div>
    <div class="row-fluid <?php if (JVersion::MAJOR_VERSION == 4) { ?>row<?php } ?>">
        <div class="control-group span6 col-md-6">
            <div class="controls">
	            <?php
	            echo '<label id="batch-section-lbl" for="batch-addcat" class="hasTip" title="' . JText::_('COM_MAMS_ARTICLE_BATCH_ADDCAT_LABEL') . '::'. JText::_('COM_MAMS_ARTICLE_BATCH_ADDCAT_DESC') . '">';
	            echo JText::_('COM_MAMS_ARTICLE_BATCH_ADDCAT_LABEL').'</label>';
	            ?>
                <select name="batch[batch-addcat]" class="inputbox form-select" id="batch-addcat">
                    <option value="*"><?php echo JText::_('COM_MAMS_SELECT_Cat');?></option>
		            <?php echo JHtml::_('select.options', MAMSHelper::getCats(), 'value', 'text', "");?>
                </select>
            </div>
        </div>
        <div class="control-group span6 col-md-6">
            <div class="controls">
	            <?php
	            echo '<label id="batch-section-lbl" for="batch-rmvcat" class="hasTip" title="' . JText::_('COM_MAMS_ARTICLE_BATCH_RMVCAT_LABEL') . '::'. JText::_('COM_MAMS_ARTICLE_BATCH_RMVCAT_DESC') . '">';
	            echo JText::_('COM_MAMS_ARTICLE_BATCH_RMVCAT_LABEL').'</label>';
	            ?>
                <select name="batch[batch-rmvcat]" class="inputbox form-select" id="batch-rmvcat">
                    <option value="*"><?php echo JText::_('COM_MAMS_SELECT_CAT');?></option>
		            <?php echo JHtml::_('select.options', MAMSHelper::getCats(), 'value', 'text', "");?>
                </select>
            </div>
        </div>
    </div>
    <div class="row-fluid <?php if (JVersion::MAJOR_VERSION == 4) { ?>row<?php } ?>">
        <div class="control-group span6 col-md-6">
            <div class="controls">
	            <?php
	            echo '<label id="batch-section-lbl" for="batch-addtag" class="hasTip" title="' . JText::_('COM_MAMS_ARTICLE_BATCH_ADDTAG_LABEL') . '::'. JText::_('COM_MAMS_ARTICLE_BATCH_ADDTAG_DESC') . '">';
	            echo JText::_('COM_MAMS_ARTICLE_BATCH_ADDTAG_LABEL').'</label>';
	            ?>
                <select name="batch[batch-addtag]" class="inputbox form-select" id="batch-addtag">
                    <option value="*"><?php echo JText::_('COM_MAMS_SELECT_TAG');?></option>
		            <?php echo JHtml::_('select.options', MAMSHelper::getTags(), 'value', 'text', "");?>
                </select>
            </div>
        </div>
        <div class="control-group span6 col-md-6">
            <div class="controls">
	            <?php
	            echo '<label id="batch-section-lbl" for="batch-rmvtag" class="hasTip" title="' . JText::_('COM_MAMS_ARTICLE_BATCH_RMVTAG_LABEL') . '::'. JText::_('COM_MAMS_ARTICLE_BATCH_RMVTAG_DESC') . '">';
	            echo JText::_('COM_MAMS_ARTICLE_BATCH_RMVTAG_LABEL').'</label>';
	            ?>
                <select name="batch[batch-rmvtag]" class="inputbox form-select" id="batch-rmvtag">
                    <option value="*"><?php echo JText::_('COM_MAMS_SELECT_TAG');?></option>
		            <?php echo JHtml::_('select.options', MAMSHelper::getTags(), 'value', 'text', "");?>
                </select>
            </div>
        </div>
    </div>
    <div class="row-fluid <?php if (JVersion::MAJOR_VERSION == 4) { ?>row<?php } ?>">
        <div class="control-group span6 col-md-6">
            <div class="controls">
	            <?php
	            echo '<label id="batch-section-lbl" for="batch-addauth" class="hasTip" title="' . JText::_('COM_MAMS_ARTICLE_BATCH_ADDAUTH_LABEL') . '::'. JText::_('COM_MAMS_ARTICLE_BATCH_ADDAUTH_DESC') . '">';
	            echo JText::_('COM_MAMS_ARTICLE_BATCH_ADDAUTH_LABEL').'</label>';
	            ?>
                <select name="batch[batch-addauth]" class="inputbox form-select" id="batch-addauth">
                    <option value="*"><?php echo JText::_('COM_MAMS_SELECT_AUTHOR');?></option>
		            <?php echo JHtml::_('select.options', MAMSHelper::getAuths(), 'value', 'text', "");?>
                </select>
            </div>
        </div>
        <div class="control-group span6 col-md-6">
            <div class="controls">
	            <?php
	            echo '<label id="batch-section-lbl" for="batch-addauth" class="hasTip" title="' . JText::_('COM_MAMS_ARTICLE_BATCH_RMVAUTH_LABEL') . '::'. JText::_('COM_MAMS_ARTICLE_BATCH_RMVAUTH_DESC') . '">';
	            echo JText::_('COM_MAMS_ARTICLE_BATCH_RMVAUTH_LABEL').'</label>';
	            ?>
                <select name="batch[batch-rmvauth]" class="inputbox form-select" id="batch-rmvauth">
                    <option value="*"><?php echo JText::_('COM_MAMS_SELECT_AUTHOR');?></option>
		            <?php echo JHtml::_('select.options', MAMSHelper::getAuths(), 'value', 'text', "");?>
                </select>
            </div>
        </div>
    </div>
</div>