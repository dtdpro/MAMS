<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$params = $this->form->getFieldsets('params');
?>
<form action="<?php echo JRoute::_('index.php?option=com_mams&layout=edit&dl_id='.(int) $this->item->dl_id); ?>" method="post" name="adminForm" id="continued-form" class="form-validate">
	<div class="width-70 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_MAMS_DLOAD_DETAILS' ); ?></legend>
			<ul class="adminformlist">
<?php foreach($this->form->getFieldset('details') as $field): ?>
				<li><?php echo $field->label;echo $field->input;?></li>
<?php endforeach; ?>
			</ul>
		</fieldset>
	</div>
	<div class="width-30 fltlft">
	</div>
	<div>
		<input type="hidden" name="task" value="dload.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

