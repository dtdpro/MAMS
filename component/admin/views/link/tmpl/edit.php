<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');
if (JVersion::MAJOR_VERSION == 3) JHtml::_('formbehavior.chosen', 'select');
$params = $this->form->getFieldsets('params');

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
?>
<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if (task == 'link.cancel' || document.formvalidator.isValid(document.getElementById('mams-form'))) {
            Joomla.submitform(task, document.getElementById('mams-form'));
        }
    }
</script>
<form action="<?php echo JRoute::_('index.php?option=com_mams&layout=edit&link_id='.(int) $this->item->link_id); ?>" method="post" name="adminForm" id="mams-form" class="form-validate">
	<?php if (JVersion::MAJOR_VERSION == 4 || JVersion::MAJOR_VERSION == 5) { ?><div class="form-horizontal main-card"><?php } ?>

		<?php
		if (JVersion::MAJOR_VERSION == 4 || JVersion::MAJOR_VERSION == 5) {
			echo HTMLHelper::_('uitab.startTabSet', 'myTab', array( 'active' => 'details', 'recall' => true, 'breakpoint' => 768 ) );
			echo HTMLHelper::_('uitab.addTab', 'myTab', 'general', Text::_('COM_MAMS_LINK_DETAILS'));
		} else {
			echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general'));
			echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_MAMS_LINK_DETAILS', true));
		}
		?>

        <div class="row-fluid <?php if (JVersion::MAJOR_VERSION == 4 || JVersion::MAJOR_VERSION == 5) { ?>row<?php } ?>">
            <div class="span10 col-md-10">
				<?php foreach($this->form->getFieldset('details') as $field): ?>
                    <div class="control-group">
                        <div class="control-label"><?php echo $field->label;?></div>
                        <div class="controls"><?php echo $field->input;?></div>
                    </div>
				<?php endforeach; ?>
            </div>
            <div class="span2 form-vertical col-md-2">
				<?php foreach($this->form->getFieldset('options') as $field): ?>
                    <div class="control-group">
                        <div class="control-label"><?php echo $field->label;?></div>
                        <div class="controls"><?php echo $field->input;?></div>
                    </div>
				<?php endforeach; ?>

            </div>
        </div>

		<?php
		if (JVersion::MAJOR_VERSION == 4 || JVersion::MAJOR_VERSION == 5) {
			echo HTMLHelper::_('uitab.endTab');
			echo HTMLHelper::_('uitab.endTabSet');
		} else {
			echo JHtml::_('bootstrap.endTab');
			echo JHtml::_('bootstrap.endTabSet');
		}
		?>
		<?php if (JVersion::MAJOR_VERSION == 4 || JVersion::MAJOR_VERSION == 5) { ?></div><?php } ?>

    <input type="hidden" name="task" value="link.edit" />
	<?php echo JHtml::_('form.token'); ?>
</form>

