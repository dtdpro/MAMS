<?php
defined('_JEXEC') or die;

?>
<button type="button" class="btn" onclick="" data-dismiss="modal" data-bs-dismiss="modal">
	<?php echo JText::_('JCANCEL'); ?>
</button>
<button type="submit" class="btn btn-success" onclick="Joomla.submitbutton('article.batch');return false;">
	<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
</button>