<?php defined('_JEXEC') or die('Restricted access'); 
JHtml::_('bootstrap.tooltip');
?>
<form action="" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>



        <div class="js-stools" role="search">
            <div class="js-stools-container-bar">
                <div class="btn-toolbar row row-fluid" style="padding-bottom:10px;">
                    <div class="btn-group col-md-3 span3">
                        <label for="startdate" class="element-invisible">Type: </label>
		                <?php echo JHtml::_('select.genericlist', $this->typesl, 'filter_type',['class'=>"inputbox form-select",'onchange'=>"this.form.submit();"],'value', 'text', $this->model->getState('filter_type')); ?>
                    </div>

                    <div class="btn-group col-md-8 span8 text-center">
		                <?php echo JHtml::_('calendar',$this->model->getState('startdate'),'startdate','startdate','%Y-%m-%d',['onChange'=>'this.form.submit()']); ?>
                        <label>&nbsp;to&nbsp;</label>
	                    <?php echo JHtml::_('calendar',$this->model->getState('enddate'),'enddate','enddate','%Y-%m-%d',['onChange'=>'this.form.submit()']); ?>
                    </div>
                    <div class="btn-group pull-right hidden-phone col-md-1 span1 text-right">
                        <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
		                <?php echo $this->pagination->getLimitBox(); ?>
                    </div>
                </div>
            </div>

        </div>
	
	<div class="clearfix"> </div>

	<table class="adminlist table table-striped">
	<thead>
		<tr>
			<th width="60"><?php echo JText::_( 'NUM' ); ?></th>
			<th><?php echo JText::_( 'Section' ); ?></th>
			<th><?php echo JText::_( 'Item' ); ?></th>
			<th><?php echo JText::_( 'Type' ); ?></th>
			<th><?php echo JText::_( 'When' ); ?></th>
			<th><?php echo JText::_( 'Who' ); ?></th>
			<?php 
				if ($this->config->mue) {
					echo '<th>'.JText::_( 'Group' ).'</th>';
				}
			?>
        	<th width="70"><?php echo JText::_( 'Session' ); ?></th>
        	<th width="70"><?php echo JText::_( 'IP Address' ); ?></th>
		</tr>			
	</thead>
	 <tfoot><tr>
		<td colspan="<?php echo ($this->config->mue) ? 9 : 8; ?>">
			<?php echo $this->pagination->getListFooter(); ?>
		</td></tr>
	</tfoot>
	<?php
	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)
	{
		$row = &$this->items[$i];
		if ($row->mt_user == 0) $row->users_name='Guest User';
		
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td class="small"><?php echo $i + 1 + $this->pagination->limitstart; ?></td>
			<td class="small"><?php echo $row->sec_title; ?></td>
			<td class="small"><?php echo $row->item_title; ?></td>
			<td class="small"><?php 
				switch ($row->mt_type) {
					case "article": echo "Article Page"; break;
					case "author": echo "Author Page"; break;
					case "catlist": echo "Category Article List"; break;
					case "seclist": echo "Section Article List"; break;
					case "autlist": echo "Author Article List"; break;
					case "authors": echo "Authors List"; break;
					case "dload": echo "Download"; break;	
					case "media": echo "Media"; break;	
				}
			
			?></td>
            <td class="small"><?php echo $row->mt_time; ?></td>
			<td class="small"><?php 
				echo $row->users_name; 
			?></td>
			<?php 
				if ($this->config->mue) {
					echo '<td>'.$row->UserGroup.'</td>';
				}
			?>
			<td class="small"><?php echo $row->mt_session; ?></td>
			<td class="small"><?php echo $row->mt_ipaddr; ?></td>

		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
        
	</table>
</div>

<input type="hidden" name="option" value="com_mams" /> 
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" /> 
<input type="hidden" name="controller" value="stats" />
</form>
