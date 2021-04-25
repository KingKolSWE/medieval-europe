

<div class="pagetitle"><?php echo kohana::lang('global.message')?></div>
<?php echo html::image(array('src' => 'media/images/template/hruler.png')); ?>
<br/>
<?php echo kohana::lang('admin.postedon') ?>&nbsp;
<b>
<?php echo Model_Utility::format_datetime($message -> timestamp) ?>
</b>
<br/>
<?php echo kohana::lang('admin.timesread', $message -> read ) ?>
<br/>
<h5>
<?php echo Model_Utility::bbcode($message -> summary) ?>
</h5>

<br/>

<p>
<?php echo Model_Utility::bbcode($message -> message) ?>
</p>

<?
if (Auth::instance()->logged_in('admin'))
	echo html::anchor( 'page/readnews/' . $message -> id, 'Share article' ); 
?>

<br style="clear:both;" />
