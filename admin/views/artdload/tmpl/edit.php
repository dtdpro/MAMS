<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$params = $this->form->getFieldsets('params');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'artdload.cancel' || document.formvalidator.isValid(document.id('mams-form'))) {
			Joomla.submitform(task, document.getElementById('mams-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_mams&layout=edit&ad_id='.(int) $this->item->ad_id); ?>" method="post" name="adminForm" id="mams-form" class="form-validate">
	<div class="width-40 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_MAMS_ARTDLOAD_DETAILS' ); ?></legend>
			<ul class="adminformlist">
<?php foreach($this->form->getFieldset('details') as $field): ?>
				<li><?php echo $field->label;echo $field->input;?></li>
<?php endforeach; ?>
			</ul>
		</fieldset>
	</div>
	
	<div>
		<input type="hidden" name="task" value="artdload.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

