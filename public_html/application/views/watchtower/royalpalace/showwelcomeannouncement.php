<div class="pagetitle"><?= $welcomeannouncement -> title;?></div>

<?php	echo html::image(array('src' => 'media/images/template/hruler.png')); ?>

<p> <?= Model_Utility::bbcode($welcomeannouncement -> text); ?> </p>

<br style='clear:both'/>
