<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');
if (JVersion::MAJOR_VERSION == 3) JHtml::_('formbehavior.chosen', 'select');
$params = $this->form->getFieldsets('params');

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'field.cancel' || document.formvalidator.isValid(document.getElementById('mams-form'))) {
			Joomla.submitform(task, document.getElementById('mams-form'));
		}
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_mams&layout=edit&field_id='.(int) $this->item->field_id); ?>" method="post" name="adminForm" id="mams-form" class="form-validate">
	<?php if (JVersion::MAJOR_VERSION == 4) { ?><div class="form-horizontal main-card"><?php } ?>

		<?php
		if (JVersion::MAJOR_VERSION == 4) {
			echo HTMLHelper::_('uitab.startTabSet', 'myTab', array( 'active' => 'details', 'recall' => true, 'breakpoint' => 768 ) );
			echo HTMLHelper::_('uitab.addTab', 'myTab', 'general', Text::_('COM_MAMS_FIELDGROUP_DETAILS'));
		} else {
			echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general'));
			echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_MAMS_FIELDGROUP_DETAILS', true));
		}
		?>

        <div class="row-fluid <?php if (JVersion::MAJOR_VERSION == 4) { ?>row<?php } ?>">
            <div class="span6 col-md-6">
				<?php foreach($this->form->getFieldset('details') as $field): ?>
                    <div class="control-group">
                        <div class="control-label"><?php echo $field->label;?></div>
                        <div class="controls"><?php echo $field->input;?></div>
                    </div>
				<?php endforeach; ?>
            </div>
            <div class="span6 form-vertical col-md-6">
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

            </div>
        </div>

		<?php
		if (JVersion::MAJOR_VERSION == 4) {
			echo HTMLHelper::_('uitab.endTab');
			echo HTMLHelper::_('uitab.endTabSet');
		} else {
			echo JHtml::_('bootstrap.endTab');
			echo JHtml::_('bootstrap.endTabSet');
		}
		?>
		<?php if (JVersion::MAJOR_VERSION == 4) { ?></div><?php } ?>

    <input type="hidden" name="task" value="field.edit" />
	<?php echo JHtml::_('form.token'); ?>
	
	
</form>

