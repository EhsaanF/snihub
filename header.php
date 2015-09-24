<!DOCTYPE HTML>
<html>
	<head>
		<link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>">
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,initial-scale=1.0">

		<?php wp_head(); ?>
	</head>
	<body id="site">
		<nav class="navbar navbar-default navbar-fixed-top">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-nav">
						<span class="sr-only">ناوبری</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="<?php bloginfo( 'url' ); ?>"><h3 style="display: inline;"><span class="icon-snippets"></span> SniHub</h3></a>
				</div>

				<div class="collapse navbar-collapse" id="main-nav">
					<form class="navbar-form navbar-left" method="<?php bloginfo( 'url' ); ?>" method="get">
						<div class="form-group">
							<div class="input-group" style="direction: ltr;">
								<input name="s" type="search" class="form-control" id="search_place" value="<?php the_search_query(); ?>" placeholder="Search...">
								<span class="input-group-btn">
									<button class="btn btn-default" type="submit"><span class="icon-search"></span></button>
								</span>
							</div>
						</div>
					</form>

					<ul class="nav navbar-nav navbar-right">
						<?php
							if ( is_user_logged_in() ) { global $snihub_options;
								?><li<?php if ( is_page( $snihub_options['new_snippet_page'] ) ) echo ' class="active"'; ?>><a href="<?php echo get_permalink( $snihub_options['new_snippet_page'] ); ?>"><span class="icon-snippets"></span> Submit snippet</a></li>
								<li<?php if ( is_page( $snihub_options['my_snippets_page'] ) ) echo ' class="active"'; ?>><a href="<?php echo get_permalink( $snihub_options['my_snippets_page'] ); ?>"><span class="icon-collection"></span> My snippets</a></li>
								<li<?php if ( is_page( $snihub_options['profile_page'] ) ) echo ' class="active"'; ?>><a href="<?php echo get_permalink( $snihub_options['profile_page'] ); ?>"><span class="icon-user"></span> Your profile</a></li>
								<li><a href="<?php echo wp_logout_url(); ?>"><span class="icon-sign-out"></span> Sign out</a></li><?php
							} else {
								?><li><a href="<?php echo wp_login_url(); ?>"><span class="icon-sign-in"></span> Sign in</a></li><?php
							}
						?>
					</ul>
				</div>
			</div>
		</nav>