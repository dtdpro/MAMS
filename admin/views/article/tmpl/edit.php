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
		<div class="span12 form-horizontal">
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
		</div>
	</div>
	<div>
		<input type="hidden" name="task" value="article.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<div class="clr"></div>