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
		if (task == 'article.cancel' || document.formvalidator.isValid(document.id('mams-form'))) {
			Joomla.submitform(task, document.getElementById('mams-form'));
		}
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_mams&layout=edit&art_id='.(int) $this->item->art_id); ?>" method="post" name="adminForm" id="mams-form" class="form-validate">
	<div class="row-fluid">
		<div class="span12">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', "Main"); ?>
			<div class="row-fluid">
				<div class="span4 form-horizontal">
					<h4><?php echo JText::_( 'COM_MAMS_ARTICLE_INFO' ); ?></h4>
					<?php foreach($this->form->getFieldset('info') as $field): ?>
						<div class="control-group">
							<div class="control-label"><?php echo $field->label;?></div>
							<div class="controls"><?php echo $field->input;?></div>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="span4 form-horizontal">
					<h4><?php echo JText::_( 'COM_MAMS_ARTICLE_CONTENT' ); ?></h4>
					<?php foreach($this->form->getFieldset('content') as $field): ?>
						<div class="control-group">
							<div class="control-label"><?php echo $field->label;?></div>
							<div class="controls"><?php echo $field->input;?></div>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="span4 form-horizontal">
					<h4><?php echo JText::_('COM_MAMS_ARTICLE_DETAILS');?></h4>
					<?php foreach($this->form->getFieldset('accessibility') as $field): ?>
						<div class="control-group">
							<div class="control-label"><?php echo $field->label;?></div>
							<div class="controls"><?php echo $field->input;?></div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<h4><?php echo JText::_( 'COM_MAMS_ARTICLE_BODY' ); ?></h4>
			<?php foreach($this->form->getFieldset('body') as $field): ?>
				<?php echo $field->input;?>
			<?php endforeach; ?>
		<?php echo JHtml::_('bootstrap.endTab'); 
		foreach ($this->addfields as $g) {
			echo JHtml::_('bootstrap.addTab', 'myTab', $g->group_name,$g->group_title);
			/*foreach ($g->fields as $f) {
				echo '<div class="control-group"><div class="control-label">';
				echo '<label id="jform_'.$g->group_name.'_'.$f->field_name.'-lbl" for="jform_'.$g->group_name.'_'.$f->field_name.'" class="hasTip" title="'.$f->field_title.'">';
				echo $f->field_title;
				echo '</label>';
				echo '</div><div class="controls">';
				switch ($f->field_type) {
					case "textfield": echo '<input type="text" name="jform['.$g->group_name.']['.$f->field_name.']" id="jform_'.$g->group_name.'_'.$f->field_name.'" value="" class="inputboxd" />'; break;
					case "textbox": echo '<textarea name="jform['.$g->group_name.']['.$f->field_name.']" id="jform_'.$g->group_name.'_'.$f->field_name.'" cols="80" rows="6" class="inputbox"></textarea>'; break;
					case "editor": echo "Text Editor to go here"; break;
					default: echo "This Field not to go here"; break;
				}
				echo '</div></div>';
			}		*/
			foreach($g->form->getFieldset($g->group_name) as $field): ?>
				<div class="control-group">
					<div class="control-label"><?php echo $field->label;?></div>
					<div class="controls"><?php echo $field->input;?></div>
				</div>
			<?php endforeach; 

			echo JHtml::_('bootstrap.endTab');

		}
		echo JHtml::_('bootstrap.endTabSet'); ?>
		</div>
	</div>
	<div>
		<input type="hidden" name="task" value="article.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<div class="clr"></div>