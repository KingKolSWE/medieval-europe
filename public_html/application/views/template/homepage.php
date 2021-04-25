
<!DOCTYPE html>

<head>
<meta name="verifyownership"
 content="02c92071ea213f01aa84f08ac2c3097e"/>

	<title><?php
		if ( !isset( $title ))
			echo __('page-homepage.gameheader');
		else
			echo $title ?>
	</title>

	<link href="/favicon.ico" rel="icon" type="image/x-icon" />

	<?php
	echo '<meta name="description", content="Medieval Europe is an historical game with elements of roleplay and strategy set in medieval age.">';
	echo '<meta name="viewport", content="width=device-width, initial-scale=1.0">';
	echo '<meta name="content-type", content="text/html; charset=utf-8">';
	echo '<meta name="Content-Language", content="en">';
	echo '<meta name="X-UA-Compatible", content="IE=8">';
	echo '<meta name="keywords", content="medieval, historical, roleplay game, strategy">';
	echo '<meta name="robots", "all">';

	?>

	<!-- Bootstrap -->
	<!-- Latest compiled and minified CSS -->

	<?= HTML::style('https://code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css'); ?>
	<?= HTML::style("https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"); ?>
	<?= HTML::style("media/css/homepage.css"); ?>

	<?= HTML::script('https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js'); ?>
	<?= HTML::script('https://code.jquery.com/ui/1.12.0/jquery-ui.min.js'); ?>


	<?= HTML::script('https://code.jquery.com/jquery-2.1.4.min.js'); ?>
	<?= HTML::script('https://code.jquery.com/ui/1.11.4/jquery-ui.min.js'); ?>
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>

	<!--Start Cookie Script-->
	<script type="text/javascript" charset="UTF-8" src="http://chs03.cookie-script.com/s/c1a45fad0c67cfbc0502841bd4338ca7.js"></script>
	<!--End Cookie Script-->

	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-11143472-3"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());
	  gtag('config', 'UA-11143472-3');
	</script>


  <!-- Facebook Pixel Code -->
  <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '699496567081418');
    fbq('track', 'PageView');
  </script>
  <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=699496567081418&ev=PageView&noscript=1"
  /></noscript>
  <!-- End Facebook Pixel Code -->

  <!-- Adsense code -->
  <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
  <script>
    (adsbygoogle = window.adsbygoogle || []).push({
      google_ad_client: "ca-pub-3403853548398248",
      enable_page_level_ads: true
    });
  </script>

</head>

<body>

<?php KO7::$log->add(KO7_Log::INFO, '-> In homepage.php...');
$meduerConfig = KO7::$config->load('medeur');
?>

<div class="container">

	<div class="row" id="header">
			<div class="col-xs-12 col-md-8 text-center">
				medieval-europe.eu - v2.9.5.3 - Env: <span class="value"><?php echo $meduerConfig->get('environment'); ?></span> -
				Server: <span class="value"><?= $meduerConfig->get('servername'); ?></span> - <?= HTML::anchor(
					'page/serverinfo', I18n::$lang); ?>
				</span> -
				<?= HTML::anchor('https://wiki.medieval-europe.eu', 'Wiki', array( 'target' => 'blank'  ) );?>	-
				<?= HTML::anchor($meduerConfig->get('supporturl'), 'Support', array( 'target' => 'blank'  ) );?>	-
				<?= HTML::anchor($meduerConfig->get('officialrpforumurl'), 'Forum', array( 'target' => 'blank'  ) );?>
			</div>

			<div class="col-xs-12 col-md-4 text-right">

				<ul id="languages" class="list-inline">
					<li>

					<?= HTML::anchor('character/change_language/it_IT', HTML::image('media/images/flags-lang/it.png', array('title' => 'Italian', 'class' => 'img-responsive img-flag', 'alt' => 'Italy flag')));
					?>
					</li>
					<li>
					<?= HTML::anchor('character/change_language/en_US', HTML::image('media/images/flags-lang/gb.png', array('title' => 'English', 'class' => 'img-responsive', 'alt' => 'Great Britain flag')));
					?>
					</li>
					<li>
					<?= HTML::anchor('character/change_language/fr_FR', HTML::image('media/images/flags-lang/fr.png', array('title' => 'French', 'class' => 'img-responsive')));?>
					</li>
					<!--
					<li>
					<?= HTML::anchor('character/change_language/ro_RO', HTML::image('media/images/flags-lang/ro.png', array('title' => 'Romanian', 'class' => 'img-responsive', 'alt' => 'Romania flag')));?>
					</li>
					-->
					<li>
					<?= HTML::anchor('character/change_language/bg_BG', HTML::image('media/images/flags-lang/bg.png', array('title' => 'Bulgarian', 'class' => 'img-responsive', 'alt' => 'Bulgary flag')));?>
					</li>
					<li>
					<?= HTML::anchor('character/change_language/de_DE', HTML::image('media/images/flags-lang/de.png', array('title' => 'Deutsch', 'class' => 'img-responsive', 'alt' => 'Germany flag')));?>
					</li>
					<li>
					<?=	HTML::anchor('character/change_language/ru_RU', HTML::image('media/images/flags-lang/ru.png', array('title' => 'Russian', 'class' => 'img-responsive', 'alt' => 'Russia flag')));?>
					</li>
					<!--
					<li>
					<?= HTML::anchor('character/change_language/tr_TR', HTML::image('media/images/flags-lang/tr.png', array('title' => 'Turkish', 'class' => 'img-responsive', 'alt' => 'Turkey flag')));?>
					</li>
					-->
					<li>
					<?= HTML::anchor('character/change_language/cz_CZ', HTML::image('media/images/flags-lang/cz.png', array('title' => 'Czech', 'class' => 'img-responsive', 'alt' => 'Czech flag')));?>
					</li>
					<!--
					<li>
					<?= HTML::anchor('character/change_language/cz_CZ', HTML::image('media/images/flags-lang/sk.png', array('title' => 'Slovak', 'class' => 'img-responsive', 'alt' => 'Slovacchia flag')));?>
					</li>
					-->
					<li>
					<?= HTML::anchor('character/change_language/pt_PT', HTML::image('media/images/flags-lang/pt.png', array('title' => 'Portuguese', 'class' => 'img-responsive', 'alt' => 'Portugal flag')));?>
					</li>
					<li>
					<?= HTML::anchor('character/change_language/gr_GR', HTML::image('media/images/flags-lang/gr.png', array('title' => 'Greek', 'class' => 'img-responsive', 'alt' => 'Greece flag')));?>
					</li>
					<li>
				        <?= HTML::anchor('character/change_language/es_ES', HTML::image('media/images/flags-lang/es.png', array ('title' => 'Spanish', 'class' => 'img-responsive', 'alt' => 'Spanish flag')));?>
					</li>
				</ul>
			</div>
	</div>

	<div class="row block push">
		<div id="content" class="col-xs-12 ">
			<?php echo $content ?>
			<br style='clear:both'/>
		</div>
	</div>

	<div id="footer" class="row">
		<div class="col-xs-8 col-xs-offset-2 text-center">
			We accept crypto:
			<?= HTML::image('media/images/template/btc.png', array('width' => '20px', 'alt' => 'Bitcoin accepted', 'title' => 'Bitcoin Accepted')) ?>
			<?= HTML::image('media/images/template/bch.png', array('width' => '20px', 'alt' => 'Bitcoin Cash accepted', 'title' => 'Bitcoin Cash Accepted')) ?>
			<?= HTML::image('media/images/template/ethereum.png', array('width' => '20px', 'alt' => 'Ethereum accepted', 'title' => 'Ethereum Accepted')) ?>
			<?= HTML::image('media/images/template/litecoin.png', array('width' => '20px', 'alt' => 'Litecoin accepted', 'title' => 'Litecoin Accepted')) ?>
			<?php /*<?= HTML::image('media/images/template/waves.png', array('width' => '20px', 'alt' => 'Waves accepted', 'title' => 'Waves Accepted')) ?> plus others */ ?>
			 -
			<?= HTML::anchor('/page/display/privacy-and-cookies', __('page-homepage.privacy'));?>
			 -
			&copy; medieval-europe.eu is a product of <a href="https://eightyeightbrands.com">Eighty Eight Brands</a> <?= date("Y"); ?>
		</div>
	</div>

</div> <!-- container-->

<?= HTML::script('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'); ?>
<?= HTML::script('https://code.jquery.com/ui/1.11.4/jquery-ui.min.js'); ?>

<?php KO7::$log->add(KO7_Log::INFO, '-> End of homepage.php...'); ?>
</body>
</html>
