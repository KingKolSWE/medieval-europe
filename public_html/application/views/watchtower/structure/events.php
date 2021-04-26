<div class="pagetitle"><?php echo $structure -> getName()?></div>

<?php echo $submenu ?>
<?php echo html::image(array('src' => 'media/images/template/hruler.png')); ?>


<div id='helper'><?php echo kohana::lang('events.structure_events_helper'); ?></div>

<?php
if ($events->count() == 0 )
{
?>
<p class='center'>
	<?= kohana::lang('character.noevents'); ?>
</p>
<?
}
else
{
?>
<div class="pagination"><?php echo $pagination->render('extended'); ?></div>
<table class='small'>
<th width='20%' ><?php echo kohana::lang('global.date') ?></th>
<th width='80%' ><?php echo kohana::lang('global.description') ?></th>
<?php
$i=0;
foreach ( $events as $e )
{	
	($i % 2 == 0) ? $class = "alternaterow_1" : $class = "alternaterow_2";
	echo "<tr class=\"$class\">";
	echo "<td class='center'>".Model_Utility::format_datetime($e->timestamp)."</td>";
	echo "<td>".Model_MyI18n::translate( $e->description ) ."</td>";
	echo "</tr>";
	$i++;
}
?>
</table>
<br/>
<div class="pagination"><?php echo $pagination->render('extended'); ?></div>
<?php } ?>
