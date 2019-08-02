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
		if (task == 'field.cancel' || document.formvalidator.isValid(document.id('mams-form'))) {
			Joomla.submitform(task, document.getElementById('mams-form'));
		}
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_mams&layout=edit&field_id='.(int) $this->item->field_id); ?>" method="post" name="adminForm" id="mams-form" class="form-validate">
	<div class="row-fluid">
		<div class="span6 form-horizontal">
		

			<h4><?php echo JText::_( 'COM_MAMS_FIELDGROUP_DETAILS' ); ?></h4>
			<?php foreach($this->form->getFieldset('details') as $field): ?>
				<div class="control-group">
					<div class="control-label"><?php echo $field->label;?></div>
					<div class="controls"><?php echo $field->input;?></div>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="span6 form-horizontal">
            <h4><?php echo JText::_( 'COM_MAMS_FIELDSET_FIELDGROUP_OPTIONS_LABEL' ); ?></h4>
		<?php
		$fieldSets = $this->form->getFieldsets('params'); 
		foreach ($fieldSets as $name => $fieldSet) : 
			$paramstabs = 'params-' . $name;
			$fieldSets = $this->form->getFieldsets('params');
			foreach ($fieldSets as $name => $fieldSet) :
				?>
				<div class="tab-pane" id="params-<?php echo $name;?>">
				<?php
				if (isset($fieldSet->description) && trim($fieldSet->description)) :
					echo '<p class="alert alert-info">'.$this->escape(JText::_($fieldSet->description)).'</p>';
				endif;
				?>
				<?php foreach ($this->form->getFieldset($name) as $field) : ?>
					<div class="control-group">
						<div class="control-label"><?php echo $field->label; ?></div>
						<div class="controls"><?php echo $field->input; ?></div>
					</div>
				<?php endforeach; ?>
				</div>
			<?php endforeach;
		endforeach; ?>

		<input type="hidden" name="task" value="field.edit" />
		<?php echo JHtml::_('form.token'); ?>
			
			
		</div>
	</div>
	
	
	
	
	
</form>

