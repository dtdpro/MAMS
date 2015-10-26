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
	<div class="form-inline form-inline-header">
		<div class="control-group ">
			<div class="control-label"><?php echo $this->form->getLabel('sec_name'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('sec_name'); ?></div>
		</div>
	</div>
	<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_MAMS_SEC_DETAILS', true)); ?>
	<div class="row-fluid">

		<div class="span10 form-horizontal">
			<div class="control-group">
				<?php echo $this->form->getInput('sec_content'); ?>
			</div>
			<div class="row-fluid">
				<div class="span6">
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('sec_thumb')?></div>
						<div class="controls"><?php echo $this->form->getInput('sec_thumb');?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('sec_desc')?></div>
						<div class="controls"><?php echo $this->form->getInput('sec_desc');?></div>
					</div>
				</div>
				<div class="span6">
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('sec_image')?></div>
						<div class="controls"><?php echo $this->form->getInput('sec_image');?></div>
					</div>
				</div>
			</div>
		</div>
		<div class="span2">
			<?php foreach($this->form->getFieldset('details') as $field): ?>
				<div class="control-group">
					<div class="control-label"><?php echo $field->label;?></div>
					<div class="controls"><?php echo $field->input;?></div>
				</div>
			<?php endforeach; ?>

		</div>
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
	<div>
		<input type="hidden" name="task" value="sec.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

