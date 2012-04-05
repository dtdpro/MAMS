<?php defined('_JEXEC') or die('Restricted access'); 

$typesl[1] = JHTML::_('select.option',  'article','Article Page');
$typesl[2] = JHTML::_('select.option',  'author','Author Page');
$typesl[3] = JHTML::_('select.option',  'seclist','Section Artice List');
$typesl[4] = JHTML::_('select.option',  'catlist','Category Artice List');
$typesl[5] = JHTML::_('select.option',  'autlist','Author Artice List');
$typesl[6] = JHTML::_('select.option',  'authors','Authors List');
$typesl[7] = JHTML::_('select.option',  'dload','Download');
?>
<form action="" method="post" name="adminForm">
<div id="editcell">
	<table class="adminlist">
    	<thead><tr>
        	<th align="left" width="50%"><?php

            ?></th>
            <th align="right" width="50%"><?php
				echo JText::_('Item Type:').JHTML::_('select.genericlist',$typesl,'filter_type','onchange="submitform();"','value','text',$this->filter_type,'filter_type');
            	echo 'Start: '.JHTML::_('calendar',$this->startdate,'startdate','startdate','%Y-%m-%d','onchange="this.form.submit()"');
				echo ' End: '.JHTML::_('calendar',$this->enddate,'enddate','enddate','%Y-%m-%d','onchange="this.form.submit()"');
				if ($this->config->continued) {
					echo JText::_(' User Group:').JHTML::_('select.genericlist',$this->grouplist,'filter_group','onchange="submitform();"','value','text',$this->filter_group,'filter_group');
				}
            ?>

			</th>
        </tr></thead>
    </table> 
	<table class="adminlist">
	<thead>
		<tr>
			<th width="60"><?php echo JText::_( 'NUM' ); ?></th>
			<th><?php echo JText::_( 'Item' ); ?></th>
			<th><?php echo JText::_( 'Type' ); ?></th>
			<th><?php echo JText::_( 'When' ); ?></th>
			<th><?php echo JText::_( 'Who' ); ?></th>
			<?php 
				if ($this->config->continued) {
					echo '<th>'.JText::_( 'Group' ).'</th>';
				}
			?>
        	<th width="70"><?php echo JText::_( 'Session' ); ?></th>
        	<th width="70"><?php echo JText::_( 'IP Address' ); ?></th>
		</tr>			
	</thead>
	<?php
	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)
	{
		$row = &$this->items[$i];
		if ($row->mt_user == 0) $row->users_name='Guest User';
		
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td><?php echo $i + 1 + $this->pagination->limitstart; ?></td>
			<td><?php echo $row->item_title; ?></td>
			<td><?php 
				switch ($row->mt_type) {
					case "article": echo "Article Page"; break;
					case "author": echo "Author Page"; break;
					case "catlist": echo "Category Article List"; break;
					case "seclist": echo "Section Article LIst"; break;
					case "autlist": echo "Author Article List"; break;
					case "authors": echo "Authors List"; break;
					case "dload": echo "Download"; break;	
				}
			
			?></td>
            <td><?php echo $row->mt_time; ?></td>
			<td><?php 
				echo $row->users_name; 
			?></td>
			<?php 
				if ($this->config->continued) {
					echo '<td>'.$row->UserGroup.'</td>';
				}
			?>
			<td><?php echo $row->mt_session; ?></td>
			<td><?php echo $row->mt_ipaddr; ?></td>

		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
         <tfoot>
		<td colspan="7">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
	</tfoot>
	</table>
</div>

<input type="hidden" name="option" value="com_mams" /> 
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" /> 
<input type="hidden" name="controller" value="stats" />
</form>
