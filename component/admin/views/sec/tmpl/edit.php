<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');
if (JVersion::MAJOR_VERSION == 3) JHtml::_('formbehavior.chosen', 'select');
$params = $this->form->getFieldsets('params');
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
    {
        if (task == 'sec.cancel' || document.formvalidator.isValid(document.getElementById('mams-form'))) {
            Joomla.submitform(task, document.getElementById('mams-form'));
        }
    }
</script>
<form action="<?php echo JRoute::_('index.php?option=com_mams&layout=edit&sec_id='.(int) $this->item->sec_id); ?>" method="post" name="adminForm" id="mams-form" class="form-validate">
	<div class="form-inline form-inline-header">
		<div class="control-group ">
			<div class="control-label"><?php echo $this->form->getLabel('sec_name'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('sec_name'); ?></div>
		</div>
	</div>
	<?php if (JVersion::MAJOR_VERSION == 4) { ?><div class="form-horizontal main-card"><?php } ?>

	    <?php
	    if (JVersion::MAJOR_VERSION == 4) {
		    echo HTMLHelper::_('uitab.startTabSet', 'myTab', array( 'active' => 'details', 'recall' => true, 'breakpoint' => 768 ) );
		    echo HTMLHelper::_('uitab.addTab', 'myTab', 'general', Text::_('COM_MAMS_SEC_DETAILS'));
	    } else {
		    echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general'));
	        echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_MAMS_SEC_DETAILS', true));
	    }
	    ?>

	<div class="row-fluid <?php if (JVersion::MAJOR_VERSION == 4) { ?>row<?php } ?>">

		<div class="span10 col-md-10">
            <div class="row-fluid <?php if (JVersion::MAJOR_VERSION == 4) { ?>row<?php } ?>">
                <div class="span12 form-vertical col-md-12">
                    <?php echo $this->form->renderField('sec_content'); ?>
                </div>
            </div>
			<div class="row-fluid <?php if (JVersion::MAJOR_VERSION == 4) { ?>row<?php } ?>">
				<div class="span6 form-horizontal col-md-6">
					<?php echo $this->form->renderField('sec_thumb'); ?>
					<?php echo $this->form->renderField('sec_desc'); ?>
				</div>
				<div class="span6 form-horizontal col-md-6">
					<?php echo $this->form->renderField('sec_image'); ?>
				</div>
			</div>
		</div>
		<div class="span2 form-vertical col-md-2">
			<?php foreach($this->form->getFieldset('details') as $field): ?>
				<div class="control-group">
					<div class="control-label"><?php echo $field->label;?></div>
					<div class="controls"><?php echo $field->input;?></div>
				</div>
			<?php endforeach; ?>

		</div>
	</div>

	    <?php
	    if (JVersion::MAJOR_VERSION == 4) {
            if ($this->canDo->get('core.admin')) {
                echo HTMLHelper::_('uitab.endTab');
            }
		    echo HTMLHelper::_('uitab.addTab', 'myTab', 'permissions', Text::_('COM_MAMS_SEC_RULES'));
	    } else {
		    echo JHtml::_('bootstrap.endTab');
            if ($this->canDo->get('core.admin')) {
	            echo JHtml::_( 'bootstrap.addTab', 'myTab', 'permissions', JText::_( 'COM_MAMS_SEC_RULES', true ) );
            }
	    }
	    ?>





		<fieldset>
			<?php echo $this->form->getInput('rules'); ?>
		</fieldset>

	    <?php
	    if (JVersion::MAJOR_VERSION == 4) {
            if ($this->canDo->get('core.admin')) {
                echo HTMLHelper::_('uitab.endTab');
            }
		    echo HTMLHelper::_('uitab.endTabSet');
	    } else {
            if ($this->canDo->get('core.admin')) {
                echo JHtml::_('bootstrap.endTab');
            }
		    echo JHtml::_('bootstrap.endTabSet');
	    }
	    ?>



		<?php if (JVersion::MAJOR_VERSION == 4) { ?></div><?php } ?>
	<div>
		<input type="hidden" name="task" value="sec.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

