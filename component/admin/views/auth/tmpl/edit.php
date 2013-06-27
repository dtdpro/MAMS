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
		if (task == 'auth.cancel' || document.formvalidator.isValid(document.id('mams-form'))) {
			Joomla.submitform(task, document.getElementById('mams-form'));
		}
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_mams&layout=edit&auth_id='.(int) $this->item->auth_id); ?>" method="post" name="adminForm" id="mams-form" class="form-validate">
	
	
	<div class="row-fluid">
		<div class="span6 form-horizontal">
					<h4><?php echo JText::_( 'COM_MAMS_AUTH_NAMEINFO' ); ?></h4>
					<?php foreach($this->form->getFieldset('nameinfo') as $field): ?>
						<div class="control-group">
							<div class="control-label"><?php echo $field->label;?></div>
							<div class="controls"><?php echo $field->input;?></div>
						</div>
					<?php endforeach; ?>
		</div>
		<div class="span6 form-horizontal">
					<h4><?php echo JText::_( 'COM_MAMS_AUTH_DETAILS' ); ?></h4>
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
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">
			<h4><?php echo JText::_( 'COM_MAMS_AUTH_CONTENT' ); ?></h4>
			<?php foreach($this->form->getFieldset('content') as $field): ?>
						<div class="control-group">
							<div class="control-label"><?php echo $field->label;?></div>
							<div class="controls"><?php echo $field->input;?></div>
						</div>
			<?php endforeach; ?>
		</div>
	</div>
	<div>
		<input type="hidden" name="task" value="auth.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

