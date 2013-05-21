<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$params = $this->form->getFieldsets('params');
?>
<form action="<?php echo JRoute::_('index.php?option=com_mams&layout=edit&al_id='.(int) $this->item->al_id); ?>" method="post" name="adminForm" id="continued-form" class="form-validate">
	<div class="width-40 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_MAMS_ARTLINK_DETAILS' ); ?></legend>
			<ul class="adminformlist">
<?php foreach($this->form->getFieldset('details') as $field): ?>
				<li><?php echo $field->label;echo $field->input;?></li>
<?php endforeach; ?>
			</ul>
		</fieldset>
	</div>
	
	<div>
		<input type="hidden" name="task" value="artlink.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

