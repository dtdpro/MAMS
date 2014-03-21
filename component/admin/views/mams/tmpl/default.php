<?php
if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;
echo '<div align="center"><img src="../media/com_mams/images/mams.png"></div>'; ?>
</div>