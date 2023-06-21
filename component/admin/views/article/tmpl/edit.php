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
		if (task == 'article.cancel' || document.formvalidator.isValid(document.getElementById('mams-form'))) {
			Joomla.submitform(task, document.getElementById('mams-form'));
		}
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_mams&layout=edit&art_id='.(int) $this->item->art_id); ?>" method="post" name="adminForm" id="mams-form" class="form-validate">

    <div class="<?php if (JVersion::MAJOR_VERSION == 3) { ?>form-inline form-inline-header <?php } ?>row row-fluid title-alias form-vertical mb-3">
        <div class="col-md-12 span12">
			<?php echo $this->form->renderField('art_title'); ?>
        </div>
    </div>

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
            <div class="span10 form-vertical col-md-10">
                <?php echo $this->form->renderField('art_sec'); ?>
                <?php echo $this->form->renderField('art_content'); ?>
            </div>
            <div class="span2 form-vertical col-md-2">
	            <?php echo $this->form->renderField('art_showtitle'); ?>
	            <?php echo $this->form->renderField('state'); ?>
	            <?php echo $this->form->renderField('access'); ?>
	            <?php echo $this->form->renderField('version_note'); ?>
	            <?php echo $this->form->renderField('art_thumb'); ?>
	            <?php echo $this->form->renderField('art_desc'); ?>
            </div>
        </div>
        <div class="row-fluid <?php if (JVersion::MAJOR_VERSION == 4) { ?>row<?php } ?>">
            <div class="span12 col-md-12">
                <div class="row-fluid <?php if (JVersion::MAJOR_VERSION == 4) { ?>row<?php } ?>">
                    <div class="span4 form-vertical col-md-4">
	                    <?php echo $this->form->renderField('authors'); ?>
                    </div>
                    <div class="span4 form-vertical col-md-4">
			            <?php echo $this->form->renderField('cats'); ?>
                    </div>
                    <div class="span4 form-vertical col-md-4">
			            <?php echo $this->form->renderField('tags'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row-fluid <?php if (JVersion::MAJOR_VERSION == 4) { ?>row<?php } ?>">
            <div class="span12 col-md-12">
                <div class="row-fluid <?php if (JVersion::MAJOR_VERSION == 4) { ?>row<?php } ?>">
                    <div class="span3 form-vertical col-md-3">
						<?php echo $this->form->renderField('medias'); ?>
                    </div>
                    <div class="span3 form-vertical col-md-3">
	                    <?php echo $this->form->renderField('images'); ?>
                    </div>
                    <div class="span3 form-vertical col-md-3">
	                    <?php echo $this->form->renderField('dloads'); ?>
                    </div>
                    <div class="span3 form-vertical col-md-3">
	                    <?php echo $this->form->renderField('links'); ?>
                    </div>
                </div>
            </div>
        </div>

		<?php
		if (JVersion::MAJOR_VERSION == 4) {
			echo HTMLHelper::_('uitab.endTab');
			echo HTMLHelper::_('uitab.addTab', 'myTab', 'publishing', Text::_('COM_MAMS_ARTICLE_PREVIEW'));
		} else {
			echo JHtml::_('bootstrap.endTab');
			echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('COM_MAMS_ARTICLE_PREVIEW', true));
		}
		?>

        <div class="row-fluid <?php if (JVersion::MAJOR_VERSION == 4) { ?>row<?php } ?>">
            <div class="span12 col-md-12 form-vertical">
	            <?php echo $this->form->renderField('art_preview'); ?>
            </div>
        </div>

		<?php
		if (JVersion::MAJOR_VERSION == 4) {
			echo HTMLHelper::_('uitab.endTab');
			echo HTMLHelper::_('uitab.addTab', 'myTab', 'publishing', Text::_('COM_MAMS_ARTICLE_PUBLISHING'));
		} else {
			echo JHtml::_('bootstrap.endTab');
			echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('COM_MAMS_ARTICLE_PUBLISHING', true));
		}
		?>

        <div class="row-fluid <?php if (JVersion::MAJOR_VERSION == 4) { ?>row<?php } ?>">
            <div class="span6 form-horizontal col-md-6">
                <?php echo $this->form->renderField('feataccess'); ?>
                <?php echo $this->form->renderField('art_alias'); ?>
                <?php echo $this->form->renderField('art_id'); ?>
                <?php echo $this->form->renderField('art_added_by'); ?>
                <?php echo $this->form->renderField('art_added'); ?>
                <?php echo $this->form->renderField('art_excluded'); ?>
            </div>

            <div class="span6 form-horizontal col-md-6">
                <?php echo $this->form->renderField('art_publish_up'); ?>
                <?php echo $this->form->renderField('art_publish_down'); ?>
                <?php echo $this->form->renderField('art_modified_by'); ?>
                <?php echo $this->form->renderField('art_modified'); ?>
                <?php echo $this->form->renderField('version'); ?>
                <?php echo $this->form->renderField('art_hits'); ?>
            </div>
        </div>


		<?php
		if (JVersion::MAJOR_VERSION == 4) {
			echo HTMLHelper::_('uitab.endTab');
		} else {
			echo JHtml::_('bootstrap.endTab');
		}

        // Parameters
		$fieldSets = $this->form->getFieldsets('params');
		foreach ($fieldSets as $name => $fieldSet) {
			$paramstabs = 'params-' . $name;

			if (JVersion::MAJOR_VERSION == 4) {
				echo HTMLHelper::_('uitab.addTab', 'myTab', $paramstabs, Text::_($fieldSet->label, true));
			} else {
				echo JHtml::_('bootstrap.addTab', 'myTab', $paramstabs, JText::_($fieldSet->label, true));
			}

			$fieldSets = $this->form->getFieldsets('params');
			foreach ($fieldSets as $name => $fieldSet) {
				if (isset($fieldSet->description) && trim($fieldSet->description)) {
					echo '<p class="alert alert-info">' . $this->escape( JText::_( $fieldSet->description ) ) . '</p>';
				}
				foreach ($this->form->getFieldset($name) as $field) { ?>
                    <div class="control-group">
                        <div class="control-label"><?php echo $field->label; ?></div>
                        <div class="controls"><?php echo $field->input; ?></div>
                    </div>
                <?php }

            }


			if (JVersion::MAJOR_VERSION == 4) {
				echo HTMLHelper::_('uitab.endTab');
			} else {
				echo JHtml::_('bootstrap.endTab');
			}
		}

		// Metadata
		$fieldSets = $this->form->getFieldsets('metadata'); 
		foreach ($fieldSets as $name => $fieldSet) {
			$metadatatabs = 'metadata-' . $name;

			if (JVersion::MAJOR_VERSION == 4) {
				echo HTMLHelper::_('uitab.addTab', 'myTab', $metadatatabs, Text::_($fieldSet->label, true));
			} else {
				echo JHtml::_('bootstrap.addTab', 'myTab', $metadatatabs, JText::_($fieldSet->label, true));
			}

			echo JLayoutHelper::render( 'joomla.edit.metadata', $this );

			if (JVersion::MAJOR_VERSION == 4) {
				echo HTMLHelper::_('uitab.endTab');
			} else {
				echo JHtml::_('bootstrap.endTab');
			}
		}

		// Rules
		if ($this->canDo->get('core.admin')) {
			if (JVersion::MAJOR_VERSION == 4) {
				echo HTMLHelper::_('uitab.addTab', 'myTab', 'permissions', Text::_('COM_MAMS_ARTICLE_RULES', true));
			} else {
				echo JHtml::_('bootstrap.addTab', 'myTab', 'permissions', JText::_('COM_MAMS_ARTICLE_RULES', true));
			}

            echo $this->form->getInput('rules');

			if (JVersion::MAJOR_VERSION == 4) {
				echo HTMLHelper::_('uitab.endTab');
			} else {
				echo JHtml::_('bootstrap.endTab');
			}
		}
						
		
		//Aditional Fields
		foreach ($this->addfields as $g) {
			if (JVersion::MAJOR_VERSION == 4) {
				echo HTMLHelper::_('uitab.addTab', 'myTab', $g->group_name, $g->group_title);
			} else {
				echo JHtml::_('bootstrap.addTab', 'myTab', $g->group_name, $g->group_title);
			}

			foreach($g->form->getFieldset($g->group_name) as $field): ?>
				<div class="control-group">
					<div class="control-label"><?php echo $field->label;?></div>
					<div class="controls"><?php echo $field->input;?></div>
				</div>
			<?php endforeach;

			if (JVersion::MAJOR_VERSION == 4) {
				echo HTMLHelper::_('uitab.endTab');
			} else {
				echo JHtml::_('bootstrap.endTab');
			}

		}


		if ( JVersion::MAJOR_VERSION == 4 ) {
			echo HTMLHelper::_( 'uitab.endTabSet' );
		} else {
			echo JHtml::_( 'bootstrap.endTabSet' );
		}


		?>

    <?php if (JVersion::MAJOR_VERSION == 4) { ?></div><?php } ?>

    <input type="hidden" name="task" value="article.edit" />
    <?php echo JHtml::_('form.token'); ?>

</form>

<div class="clr"></div>