<link rel="canonical" href="<?php echo url::site('/');?>"/>

<?php
	/*
	$errors = array(
		'username' => "ASDF",
		'email' => "ASDF",
		'captchaanswer' => "ASDF"
	);
	*/
	if (!empty($pixelgifsrc))
		echo HTML::image($pixelgifsrc);

?>

<div class="row">

	<div class="col-xs-12">

		<div class="alert alert-danger text-center">
			<?php $message = Session::instance() -> get('user_message'); echo $message ?>
		</div>

		<h1 class="text-center"><?php echo __('page-homepage.gameheader')?></h1>

		<span class="lead">
		<?php echo __('page-homepage.burbletext'); ?>
		</span>

	</div>
</div>

<div class="row">
	<div class="col-xs-12 col-md-4 col-md-offset-1">
			<h4 class="text-center"><?= __('page-homepage.signin');?></h4>
			<?php echo Form::open('/user/login') ?>

			<div class="form-group">
				<?php Form::input('username',
					null,
					array(
					'tabindex' => 1,
					'id' => 'username1',
					'placeholder' => __('page-homepage.yourusername'),
					'class' => 'form-control') ); ?>
			</div>

			<div class="form-group">

				<?php echo Form::password('password', null, array(
					'tabindex' => 2,
					'id' => 'password',
					'placeholder' => __('page-homepage.yourpassword'),
					'class' => 'form-control') ); ?>
			</div>

			<div class="btn-group btn-group-justified">
				<div class="col-xs-12" style="padding:0 1px">
					<?php echo Form::submit('signin',
						__('page-homepage.signin'),
						array (
							'class' => 'btn btn-me',
							'tabindex' => 3,
						));
					?>
				</div>
				<div class="col-xs-6" style="padding:0 1px;color:yellow">
					<a href='/facebook' tabindex = 5, class = 'btn btn-fb'>Facebook Login</a>
				</div>
				<div class="col-xs-6" style="padding:0 1px">
					<?php
						echo HTML::anchor(
							$google_login_url,
							'Google Login',
							array(
								'tabindex' => 5,
								'class' => 'btn btn-google'
							)
					);
					?>
				</div>
			</div>

			<?php echo Form::close() ?>

			<div class="form-group text-right">
					<div class="col-xs-12 col-md-4 text-left" style="padding:0 1px">
						<ul class="list-inline">
							<li>
							<?php
								echo HTML::anchor('https://www.facebook.com/pages/Medieval-Europe/108773142496282',
									HTML::image('media/images/template/fb.png',
										array(
											'alt' => 'Medieval Europe Facebook' )),
									array(
										'title' => __('page-homepage.fb_followus'),
										'class' => 'littleicon',
										'target' => 'new' ) );
							?>
							</li>
							<li>
							<?php
								echo HTML::anchor('https://google.com/+MedievaleuropeEuGame',
									HTML::image('media/images/template/gp.png',
										array(
											'alt' => 'Medieval Europe Google+' )),
									array(
										'title' => __('page-homepage.google_followus'),
										'class' => 'littleicon',
										'target' => 'new'));
							?>
							</li>
							<li>
							<?php
								echo HTML::anchor('https://twitter.com/Medieval_Europe',
									HTML::image('media/images/template/twitter.png',
										array(
											'alt' => 'Medieval Europe Twitter' )),
									array(
										'title' => __('page-homepage.tw_followus'),
										'class' => 'littleicon',
										'target' => 'new' ) );
							?>
							</li>
						</ul>
					</div>
					<div class="col-xs-12 col-md-8 text-right" style="padding:0 1px">
					<?= HTML::anchor('/user/resendpassword', __('user.login_resendpassword')); ?>
					<br/>
					<?= HTML::anchor('/user/resendvalidationtoken', __('user.resendvalidationtoken_pagetitle'));?>
					</div>
			</div>
			<div class="form-group text-center">

			</div>
	</div>

	<div class="col-xs-12 col-md-4 col-md-offset-1">
	<!-- signup -->

		<h4 class="text-center"><?= __('page-homepage.signup');?></h4>
		<?php echo Form::open('/user/register') ?>
			<div class="form-group row">
			<div class="col-xs-12 text-center">
				<?= Form::input(
				        'username',
				        $form['username'],
				        array(
					'tabindex' => 5,
					'placeholder' => __('page-homepage.yourusername'),
					'id' => 'username2',
					'class' => 'form-control') );
				?>

				<?php if (!empty ($errors['username']))
				{
				?>
					<div class="alert alert-danger text-left"><?= $errors['username']; ?></div>
				<?php } ?>
			</div>
			</div>

			<div class="form-group row">
			<div class="col-xs-12 text-center">
				<?php echo Form::input( 'email',
					$form['email'],
					array(
					'tabindex' => 6,
					'placeholder' => __('page-homepage.youremail'),
					'id' => 'email',
					'class' => 'form-control') );
				?>

				<?php if (!empty ($errors['email']))
				{
				?>
					<div class="alert alert-danger text-left"><?= $errors['email']; ?></div>
				<?php } ?>
				</div>
			</div>


			<div class="form-group row">
				<div class="col-xs-12 text-center">
				<?php
						echo Form::input( 'referreruser', $form['referreruser'], array(
								'tabindex' => 7,
								'placeholder' => __('page-homepage.referraluser'),
								'id' => 'referreruser',
								'class' => 'form-control') );
				?>
				</div>
			</div>

			<div class="form-group row">
				<div class="col-xs-12 text-center">
					<div class="g-recaptcha" data-theme = 'dark' data-sitekey="6Lf_v3MUAAAAAFs0o7zvMGoDd1XUvFSAP3qDq49Q"></div>

				 <?php if (!empty ($errors['captchaanswer']))
					{
					?>
        <div class="alert alert-danger text-left"><?= $errors['captchaanswer']; ?></div>
				<?php } ?>

			</div>

			<div class="form-group row">
				<div class="col-xs-12">
				<?php echo Form::submit('signup',
				        __('page-homepage.signup'),
						array (
						'class' => 'btn btn-me',
						'onclick' => 'fbq(\'track\', \'CompleteRegistration\');',
						'tabindex' => 7,
						'style' => 'width:100%'
						)); ?>
				</div>
			</div>

			<div>
			<small>
				<?php
					echo __('page-homepage.tosacceptance');
						echo HTML::anchor(
							'/page/display/terms-of-use',
							__('page-homepage.tos')
						);
				?>
			</small>

			</div>

	</div>
</div>	<!-- container -->
