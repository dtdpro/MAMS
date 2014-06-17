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
		<div class="form-inline form-inline-header">
			<div class="control-group ">
				<div class="control-label"><?php echo $this->form->getLabel('art_title'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('art_title'); ?></div>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_MAMS_ARTICLE_DETAILS')); ?>
			
		<div class="span10 form-horizontal">
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('art_sec'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('art_sec'); ?></div>
			</div>
			<div class="control-group">
				<?php echo $this->form->getInput('art_content'); ?>
			</div>
			<div class="row-fluid">
				<div class="span6">
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('art_thumb')?></div>
						<div class="controls"><?php echo $this->form->getInput('art_thumb');?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('art_desc')?></div>
						<div class="controls"><?php echo $this->form->getInput('art_desc');?></div>
					</div>
				</div>
			</div>
		</div>
		<div class="span2">
			<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('cats'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('cats'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('authors'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('authors'); ?></div>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('COM_MAMS_ARTICLE_PUBLISHING')); ?>
			<div class="row-fluid">
				<div class="span6 form-horizontal">
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('feataccess')?></div>
						<div class="controls"><?php echo $this->form->getInput('feataccess');?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('art_alias')?></div>
						<div class="controls"><?php echo $this->form->getInput('art_alias');?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('art_id')?></div>
						<div class="controls"><?php echo $this->form->getInput('art_id');?></div>
					</div>	
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('art_added_by'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('art_added_by'); ?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('art_added'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('art_added'); ?></div>
					</div>
				</div>
				
				<div class="span6 form-horizontal">
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('art_publish_up')?></div>
						<div class="controls"><?php echo $this->form->getInput('art_publish_up');?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('art_publish_down')?></div>
						<div class="controls"><?php echo $this->form->getInput('art_publish_down');?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('art_modified_by'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('art_modified_by'); ?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('art_modified'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('art_modified'); ?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('version'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('version'); ?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('art_hits'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('art_hits'); ?></div>
					</div>
				</div>
			</div>
		<?php echo JHtml::_('bootstrap.endTab'); 
		$fieldSets = $this->form->getFieldsets('params'); 
		foreach ($fieldSets as $name => $fieldSet) : 
			$paramstabs = 'params-' . $name; 
			echo JHtml::_('bootstrap.addTab', 'myTab', $paramstabs, JText::_($fieldSet->label, true)); 
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
			echo JHtml::_('bootstrap.endTab'); 
		endforeach; 
		
		$fieldSets = $this->form->getFieldsets('metadata'); 
		foreach ($fieldSets as $name => $fieldSet) : 
			$metadatatabs = 'metadata-' . $name; 
			echo JHtml::_('bootstrap.addTab', 'myTab', $metadatatabs, JText::_($fieldSet->label, true)); 
			echo JLayoutHelper::render('joomla.edit.metadata', $this);
			echo JHtml::_('bootstrap.endTab'); 
		endforeach; 
		


		if ($this->canDo->get('core.admin')) : ?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'permissions', JText::_('COM_MAMS_ARTICLE_RULES', true)); ?>
					<?php echo $this->form->getInput('rules'); ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php endif; 
						
		
		//Aditional Fields
		foreach ($this->addfields as $g) {
			echo JHtml::_('bootstrap.addTab', 'myTab', $g->group_name,$g->group_title);
			foreach($g->form->getFieldset($g->group_name) as $field): ?>
				<div class="control-group">
					<div class="control-label"><?php echo $field->label;?></div>
					<div class="controls"><?php echo $field->input;?></div>
				</div>
			<?php endforeach; 

			echo JHtml::_('bootstrap.endTab');

		}
		echo JHtml::_('bootstrap.endTabSet'); 
		
		?>
		</div>

		<input type="hidden" name="task" value="article.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>

</form>

<div class="clr"></div>