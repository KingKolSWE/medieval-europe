<div class="pagetitle"><?php echo 
	kohana::lang("ca_upgradestructure.build_pagetitle",
		kohana::lang($structure -> structure_type -> name),
		kohana::lang($structure -> region -> name),
		$upgradeinfo['currentlevel']+1);?>
</div>

<?php echo html::image(array('src' => 'media/images/template/hruler.png')); ?>
<br/>

<p id='helper'>
<?= kohana::lang('ca_upgradestructure.upgradestructurehelper',
$workedhours,
$upgradeinfo['hours'],
round($workedhours/$upgradeinfo['hours']*100,2),
$upgradehourlywage
);
?>
</p>

<br/>

<div class='center'>

<?= form::open() ?>
<?= form::hidden('structure_id', $structure -> id);?>

<?= kohana::lang('structures.helpbuild1') ?> 

<?= 
	form::input( array(
		'id' => 'hours',
		'name' => 'hours',
		'value' => 1,
		'class' => 'input-xsmall right')
); ?> 

<?= kohana::lang('structures.helpbuild2') ?> 

<?= form::submit( array (
	'id' => 'submit', 
	'class' => 'button button-medium', 			
	'onclick' => 'return confirm(\''.kohana::lang('global.confirm_operation').'\')' ), kohana::lang('global.work')) ;		
echo form::close();
?>
</div>
<br style='clear:both'/>