<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');
$params = $this->form->getFieldsets('params');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'sec.cancel' || document.formvalidator.isValid(document.id('mams-form'))) {
			Joomla.submitform(task, document.getElementById('mams-form'));
		}
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_mams&layout=edit&sec_id='.(int) $this->item->sec_id); ?>" method="post" name="adminForm" id="mams-form" class="form-validate">
	<div class="row-fluid">
		<div class="span12 form-horizontal">
			<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>
				<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_MAMS_SEC_DETAILS', true)); ?>
					<h4><?php echo JText::_( 'COM_MAMS_SEC_DETAILS' ); ?></h4>
					<?php foreach($this->form->getFieldset('details') as $field): ?>
						<div class="control-group">
							<div class="control-label"><?php echo $field->label;?></div>
							<div class="controls"><?php echo $field->input;?></div>
						</div>
					<?php endforeach; ?>
					<div class="control-group">
						<?php foreach ($this->form->getFieldset('jmetadata') as $field) : ?>
							<?php if ($field->name == 'jform[metadata][tags][]') :?>
								<div class="control-group">
									<div class="control-label"><?php echo $field->label; ?></div>
									<div class="controls"><?php echo $field->input; ?></div>
								</div>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				<?php echo JHtml::_('bootstrap.endTab'); ?>
				
				<?php if ($this->canDo->get('core.admin')) : ?>
					<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'permissions', JText::_('COM_MAMS_SEC_RULES', true)); ?>
						<fieldset>
						<?php echo $this->form->getInput('rules'); ?>
						</fieldset>
					<?php echo JHtml::_('bootstrap.endTab'); ?>
				<?php endif; ?>

			<?php echo JHtml::_('bootstrap.endTabSet'); ?>
		</div>
	</div>
	<div>
		<input type="hidden" name="task" value="sec.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

